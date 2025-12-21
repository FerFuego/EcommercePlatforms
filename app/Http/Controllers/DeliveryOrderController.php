<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAssignment;
use App\Models\DeliveryDriver;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    /**
     * Mapa de pedidos disponibles
     */
    public function available()
    {
        $driver = auth()->user()->deliveryDriver;

        if (!$driver || !$driver->is_approved) {
            return redirect()->route('delivery-driver.dashboard')
                ->with('error', 'Tu perfil debe estar aprobado para ver pedidos disponibles.');
        }

        // Pedidos listos para delivery O en preparación que no tienen repartidor asignado
        $availableOrders = Order::whereIn('status', [Order::STATUS_ASSIGNED_DELIVERY, Order::STATUS_PREPARING])
            ->where('delivery_type', 'delivery') // Asegurar que sea delivery
            ->whereDoesntHave('deliveryAssignment')
            ->with(['cook.user', 'items.dish'])
            ->get()
            ->filter(function ($order) use ($driver) {
                // Filtrar por área de cobertura
                return $driver->isWithinCoverage(
                    (float) $order->cook->location_lat,
                    (float) $order->cook->location_lng
                );
            });

        return view('delivery-driver.orders.available', compact('driver', 'availableOrders'));
    }

    /**
     * Mis entregas (asignadas, en progreso, completadas)
     */
    public function myDeliveries(Request $request)
    {
        $driver = auth()->user()->deliveryDriver;
        $status = $request->input('status', 'active');

        $query = $driver->deliveries()->with(['order.customer', 'order.cook.user', 'order.items.dish']);

        if ($status === 'active') {
            // Incluimos assigned, picked_up, on_the_way, delayed. 
            // La orden puede estar en PREPARING pero la asignación está en 'assigned'.
            $query->whereIn('status', ['assigned', 'picked_up', 'on_the_way', 'delayed']);
        } elseif ($status === 'completed') {
            $query->where('status', 'delivered');
        } elseif ($status === 'rejected') {
            $query->where('status', 'rejected');
        }

        $deliveries = $query->orderBy('created_at', 'desc')->get();

        return view('delivery-driver.orders.index', compact('driver', 'deliveries', 'status'));
    }

    /**
     * Ver detalle de una entrega
     */
    public function show($id)
    {
        $driver = auth()->user()->deliveryDriver;

        $delivery = DeliveryAssignment::with(['order.customer', 'order.cook.user', 'order.items.dish'])
            ->where('delivery_user_id', $driver->user_id)
            ->findOrFail($id);

        return view('delivery-driver.orders.show', compact('driver', 'delivery'));
    }

    /**
     * Aceptar un pedido
     */
    public function accept($orderId)
    {
        $driver = auth()->user()->deliveryDriver;
        $order = Order::findOrFail($orderId);

        // Verificar que el pedido esté disponible (En preparación o Listo para enviar)
        if (!in_array($order->status, [Order::STATUS_ASSIGNED_DELIVERY, Order::STATUS_PREPARING])) {
            return back()->with('error', 'Este pedido ya no está disponible.');
        }

        // Verificar que no tenga repartidor asignado
        if ($order->deliveryAssignment) {
            return back()->with('error', 'Este pedido ya fue asignado a otro repartidor.');
        }

        // Verificar cobertura
        if (!$driver->isWithinCoverage((float) $order->cook->location_lat, (float) $order->cook->location_lng)) {
            return back()->with('error', 'Este pedido está fuera de tu área de cobertura.');
        }

        // Calcular tarifa de delivery (puedes ajustar la lógica)
        $deliveryFee = $order->delivery_fee ?? 500; // Default $500

        // Crear asignación
        DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_user_id' => $driver->user_id,
            'status' => 'assigned',
            'pickup_lat' => $order->cook->location_lat,
            'pickup_lng' => $order->cook->location_lng,
            'delivery_lat' => $order->delivery_lat,
            'delivery_lng' => $order->delivery_lng,
            'delivery_fee' => $deliveryFee,
        ]);

        return redirect()->route('delivery-driver.orders.index')
            ->with('success', 'Pedido aceptado exitosamente. Dirígete al punto de retiro.');
    }

    /**
     * Rechazar un pedido
     */
    public function reject(Request $request, $orderId)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $driver = auth()->user()->deliveryDriver;
        $order = Order::findOrFail($orderId);

        // Verificar que el pedido esté asignado a este repartidor
        $delivery = DeliveryAssignment::where('order_id', $order->id)
            ->where('delivery_user_id', $driver->user_id)
            ->firstOrFail();

        $delivery->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason']
        ]);

        // El pedido vuelve a estar disponible para otros repartidores
        // (opcional: podrías eliminar la asignación en lugar de marcarla como rejected)

        return redirect()->route('delivery-driver.orders.index')
            ->with('success', 'Pedido rechazado.');
    }

    /**
     * Actualizar estado de la entrega
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:picked_up,on_the_way,delayed,delivered'
        ]);

        $driver = auth()->user()->deliveryDriver;

        $delivery = DeliveryAssignment::where('delivery_user_id', $driver->user_id)
            ->findOrFail($id);

        $delivery->status = $validated['status'];

        // Actualizar timestamps
        if ($validated['status'] === 'picked_up' && !$delivery->picked_up_at) {
            $delivery->picked_up_at = now();
            $delivery->order->status = Order::STATUS_ON_THE_WAY;
            $delivery->order->save();
        }

        if ($validated['status'] === 'delivered') {
            $delivery->delivered_at = now();
            $delivery->order->status = Order::STATUS_DELIVERED;
            $delivery->order->completed_at = now();
            $delivery->order->save();

            // Actualizar ganancias del repartidor
            $driver->addEarnings((float) $delivery->delivery_fee);
        }

        $delivery->save();

        return back()->with('success', 'Estado actualizado exitosamente.');
    }
}
