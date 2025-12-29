<?php

namespace App\Http\Controllers;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Mostrar carrito
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('orders.cart', compact('cart'));
    }

    /**
     * Agregar al carrito
     */
    public function addToCart(Request $request, $dishId)
    {
        $dish = Dish::with(['cook', 'optionGroups.options'])->findOrFail($dishId);

        if (!$dish->isAvailableToday() || $dish->available_stock < 1) {
            return back()->with('error', 'Este plato no está disponible');
        }

        $cart = session()->get('cart', []);

        // Validar que todos los items sean del mismo cocinero
        if (!empty($cart) && $cart[0]['cook_id'] !== $dish->cook_id) {
            return back()->with('error', 'Solo puedes ordenar platos de un mismo cocinero a la vez');
        }

        $quantity = $request->input('quantity', 1);

        if ($quantity > $dish->available_stock) {
            return back()->with('error', 'Stock insuficiente');
        }

        // Procesar opciones seleccionadas
        $selectedOptions = [];
        $additionalPrice = 0;

        if ($request->has('options')) {
            foreach ($request->options as $groupId => $optionIds) {
                // $optionIds could be a single ID or an array depending on min/max
                if (!is_array($optionIds)) {
                    $optionIds = [$optionIds];
                }

                foreach ($optionIds as $optionId) {
                    $option = \App\Models\DishOption::find($optionId);
                    if ($option) {
                        $selectedOptions[] = [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->additional_price,
                        ];
                        $additionalPrice += $option->additional_price;
                    }
                }
            }
        }

        $finalPrice = $dish->price + $additionalPrice;

        $cart[] = [
            'dish_id' => $dish->id,
            'cook_id' => $dish->cook_id,
            'name' => $dish->name,
            'price' => $finalPrice,
            'base_price' => $dish->price,
            'quantity' => $quantity,
            'photo_url' => $dish->photo_url,
            'options' => $selectedOptions,
        ];

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Plato agregado al carrito',
                'cart_count' => count($cart)
            ]);
        }

        return back()->with('success', 'Plato agregado al carrito');
    }

    /**
     * Remover del carrito
     */
    public function removeFromCart($index)
    {
        $cart = session()->get('cart', []);
        unset($cart[$index]);
        session()->put('cart', array_values($cart));

        return back()->with('success', 'Plato eliminado del carrito');
    }

    /**
     * Mostrar checkout
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('marketplace.catalog')->with('error', 'Tu carrito está vacío');
        }

        $cookId = $cart[0]['cook_id'];
        $cook = Cook::with('user')->findOrFail($cookId);

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        return view('orders.checkout', compact('cart', 'cook', 'subtotal'));
    }

    /**
     * Procesar checkout
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'delivery_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_type,delivery',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
            'payment_method' => 'required|in:mercadopago,cash,transfer',
            'scheduled_time' => 'nullable|date',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('marketplace.catalog')->with('error', 'Tu carrito está vacío');
        }

        DB::beginTransaction();

        try {
            $cookId = $cart[0]['cook_id'];

            // Calcular subtotal
            $subtotal = array_reduce($cart, function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);

            // Calcular delivery fee (simple, puedes mejorarlo)
            $deliveryFee = $request->delivery_type === 'delivery' ? 500 : 0;

            // Crear orden
            $order = Order::create([
                'customer_id' => auth()->id(),
                'cook_id' => $cookId,
                'status' => Order::STATUS_PENDING_PAYMENT,
                'delivery_type' => $request->delivery_type,
                'delivery_address' => $request->delivery_address,
                'delivery_lat' => $request->delivery_lat,
                'delivery_lng' => $request->delivery_lng,
                'delivery_fee' => $deliveryFee,
                'subtotal' => $subtotal,
                'total_amount' => $subtotal + $deliveryFee,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'scheduled_time' => $request->scheduled_time,
            ]);

            // Calcular comisión
            $commissionPercentage = \App\Models\Setting::get('commission_rate', 15);
            $order->calculateCommission($commissionPercentage / 100);

            // Crear order items
            foreach ($cart as $item) {
                $dish = Dish::findOrFail($item['dish_id']);

                // Decrementar stock
                if (!$dish->decrementStock($item['quantity'])) {
                    throw new \Exception("Stock insuficiente para {$dish->name}");
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'dish_id' => $item['dish_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);

                // Guardar opciones del item
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $optionData) {
                        \App\Models\OrderItemOption::create([
                            'order_item_id' => $orderItem->id,
                            'dish_option_id' => $optionData['id'],
                            'quantity' => 1, // Por ahora 1, podrías permitir cantidades en el futuro
                            'price' => $optionData['price'],
                        ]);
                    }
                }
            }

            // Si es MercadoPago, redirigir a la pasarela
            if ($request->payment_method === 'mercadopago') {
                // TODO: Integrar MercadoPago
                // Por ahora simulamos pago exitoso
                $order->markAsPaid('SIMULATED_PAYMENT_ID');
            } else {
                // Para cash/transfer, marcar como pendiente de aceptación del cocinero
                $order->status = Order::STATUS_AWAITING_COOK;
                $order->save();
            }

            DB::commit();

            // Limpiar carrito
            session()->forget('cart');

            return redirect()->route('orders.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Página de éxito
     */
    public function success($orderId)
    {
        $order = Order::with(['items.dish', 'cook.user'])->findOrFail($orderId);

        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.success', compact('order'));
    }

    /**
     * Mis pedidos (cliente)
     */
    public function myOrders()
    {
        $orders = auth()->user()->orders()->with('cook.user')->latest()->paginate(10);
        return view('orders.my-orders', compact('orders'));
    }

    /**
     * Pedidos del cocinero
     */
    public function cookOrders()
    {
        $cook = auth()->user()->cook;
        $status = request()->get('status', 'all');

        $ordersQuery = $cook->orders()->with('customer')->latest();

        if ($status !== 'all') {
            $ordersQuery->where('status', $status);
        }

        $orders = $ordersQuery->paginate(15);

        return view('cook.orders.index', compact('orders', 'status'));
    }

    /**
     * Ver detalle de pedido
     */
    public function show($orderId)
    {
        $order = Order::with(['items.dish', 'cook.user', 'customer'])->findOrFail($orderId);

        // Verificar permisos
        if (
            $order->customer_id !== auth()->id() &&
            (!auth()->user()->cook || $order->cook_id !== auth()->user()->cook->id)
        ) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Aceptar pedido (cocinero)
     */
    public function accept($orderId)
    {
        $cook = auth()->user()->cook;
        $order = $cook->orders()->findOrFail($orderId);

        $order->acceptByCook();

        return back()->with('success', 'Pedido aceptado');
    }

    /**
     * Rechazar pedido (cocinero)
     */
    public function reject(Request $request, $orderId)
    {
        $cook = auth()->user()->cook;
        $order = $cook->orders()->findOrFail($orderId);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $order->rejectByCook($request->rejection_reason);

        return back()->with('success', 'Pedido rechazado');
    }

    /**
     * Actualizar estado del pedido
     */
    public function updateStatus(Request $request, $orderId)
    {
        $cook = auth()->user()->cook;
        $order = $cook->orders()->findOrFail($orderId);

        // Support both 'status' (from view) and 'action' (from tests/legacy code)
        $status = $request->input('status') ?? $request->input('action');

        switch ($status) {
            case Order::STATUS_PREPARING:
            case 'preparing':
                $order->markAsPreparing();
                break;
            case Order::STATUS_READY_FOR_PICKUP:
            case 'ready_for_pickup':
            case 'ready':
                $order->markAsReady();
                break;
            case Order::STATUS_ON_THE_WAY:
            case 'on_the_way':
                $order->markAsOnTheWay();
                break;
            case Order::STATUS_DELIVERED:
            case 'delivered':
                $order->markAsDelivered();
                break;
            default:
                return back()->with('error', 'Acción no válida');
        }

        return back()->with('success', 'Estado actualizado');
    }

    /**
     * Volver a realizar un pedido (reorder)
     */
    public function reorder($orderId)
    {
        $originalOrder = Order::with('items.dish.cook.user')->findOrFail($orderId);

        // Verificar pertenencia
        if ($originalOrder->customer_id !== auth()->id()) {
            abort(403);
        }

        $cart = session()->get('cart', []);

        // Validar cocinero si ya hay items en el carrito
        if (!empty($cart) && $cart[0]['cook_id'] !== $originalOrder->cook_id) {
            return back()->with('error', 'Tu carrito ya tiene items de otro cocinero. Vacía tu carrito primero o pide platos del mismo cocinero.');
        }

        $addedCount = 0;
        $unavailableCount = 0;

        foreach ($originalOrder->items as $item) {
            $dish = $item->dish;

            // Verificar si el plato existe, está activo y tiene stock
            if ($dish && $dish->is_active && $dish->available_stock > 0) {
                // Determinar cantidad a agregar (mínimo entre lo pedido originalmente y el stock actual)
                $quantityToAdd = min($item->quantity, $dish->available_stock);

                // Buscar si ya está en el carrito para actualizar cantidad
                $foundIndex = -1;
                foreach ($cart as $index => $cartItem) {
                    if ($cartItem['dish_id'] === $dish->id) {
                        $foundIndex = $index;
                        break;
                    }
                }

                if ($foundIndex !== -1) {
                    // Actualizar cantidad sin exceder stock
                    $cart[$foundIndex]['quantity'] = min($cart[$foundIndex]['quantity'] + $quantityToAdd, $dish->available_stock);
                } else {
                    // Agregar nuevo item
                    $cart[] = [
                        'dish_id' => $dish->id,
                        'cook_id' => $dish->cook_id,
                        'name' => $dish->name,
                        'price' => $dish->price,
                        'quantity' => $quantityToAdd,
                        'photo_url' => $dish->photo_url,
                    ];
                }
                $addedCount++;
            } else {
                $unavailableCount++;
            }
        }

        if ($addedCount === 0) {
            return back()->with('error', 'Lo sentimos, ninguno de los productos de este pedido está disponible actualmente.');
        }

        session()->put('cart', $cart);

        $message = "Se han agregado {$addedCount} productos a tu carrito.";
        if ($unavailableCount > 0) {
            $message .= " ({$unavailableCount} productos no estaban disponibles y no se agregaron).";
        }

        return redirect()->route('cart.index')->with('success', $message);
    }
}
