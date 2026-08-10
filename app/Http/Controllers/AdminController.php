<?php

namespace App\Http\Controllers;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Dashboard del admin
     */
    public function index()
    {
        $stats = [
            'pending_cooks' => Cook::where('is_approved', false)->count(),
            'total_cooks' => Cook::where('is_approved', true)->count(),
            'pending_drivers' => \App\Models\DeliveryDriver::where('is_approved', false)->count(),
            'total_drivers' => \App\Models\DeliveryDriver::where('is_approved', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::whereIn('status', [Order::STATUS_AWAITING_COOK, Order::STATUS_PREPARING])->count(),
            'total_revenue' => Order::where('status', Order::STATUS_DELIVERED)->sum('total_amount'),
            'total_commission' => Order::where('status', Order::STATUS_DELIVERED)->sum('commission_amount'),
            'total_dishes' => Dish::count(),
            'active_dishes' => Dish::where('is_active', true)->count(),
        ];

        $recent_orders = Order::with(['customer', 'cook.user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }

    /**
     * Lista de cocineros pendientes de aprobación
     */
    public function pendingCooks()
    {
        $cooks = Cook::with('user')
            ->where('is_approved', false)
            ->latest()
            ->paginate(20);

        $pending_count = Cook::where('is_approved', false)->count();
        $approved_count = Cook::where('is_approved', true)->count();

        return view('admin.cooks.index', compact('cooks', 'pending_count', 'approved_count'));
    }

    /**
     * Ver detalle de solicitud de cocinero
     */
    public function showCook($cookId)
    {
        $cook = Cook::with('user')->findOrFail($cookId);
        return view('admin.cooks.show', compact('cook'));
    }

    /**
     * Aprobar cocinero
     */
    public function approveCook(Request $request, $cookId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.users.index');
        }

        $cook = Cook::findOrFail($cookId);
        $cook->is_approved = true;
        $cook->active = true;
        $cook->save();

        if ($cook->user) {
            $cook->user->update(['last_rejection_reason' => null]);
            if ($cook->user->email) {
                try {
                    Mail::to($cook->user->email)->send(new \App\Mail\CookApprovedMail($cook));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending cook approval email: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Cocinero aprobado exitosamente');
    }

    /**
     * Rechazar cocinero
     */
    public function rejectCook(Request $request, $cookId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.users.index');
        }

        $cook = Cook::findOrFail($cookId);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($cook->user) {
            $cook->user->update(['last_rejection_reason' => $request->rejection_reason]);

            if ($cook->user->email) {
                try {
                    Mail::to($cook->user->email)->send(new \App\Mail\CookRejectedMail($cook, $request->rejection_reason));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending cook rejection email: " . $e->getMessage());
                }
            }
        }

        $cook->delete();

        return back()->with('success', 'Solicitud rechazada exitosamente');
    }

    /**
     * Lista de todos los cocineros
     */
    public function allCooks(Request $request)
    {
        $isApproved = $request->get('filter') === 'approved';

        $cooks = Cook::with('user')
            ->where('is_approved', $isApproved)
            ->latest()
            ->paginate(20);

        $pending_count = Cook::where('is_approved', false)->count();
        $approved_count = Cook::where('is_approved', true)->count();

        return view('admin.cooks.index', compact('cooks', 'pending_count', 'approved_count'));
    }

    /**
     * Lista de todos los pedidos
     */
    public function allOrders(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Order::with(['customer', 'cook.user'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Estadísticas
     */
    public function statistics()
    {
        $totalRevenue = Order::where('status', Order::STATUS_DELIVERED)->sum('total_amount');
        $totalCommission = Order::where('status', Order::STATUS_DELIVERED)->sum('commission_amount');
        $deliveredOrdersCount = Order::where('status', Order::STATUS_DELIVERED)->count();

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_commission' => $totalCommission,
            'commission_percentage' => $totalRevenue > 0 ? round(($totalCommission / $totalRevenue) * 100, 1) : 0,
            'average_order' => $deliveredOrdersCount > 0 ? round($totalRevenue / $deliveredOrdersCount) : 0,
            'average_commission' => $deliveredOrdersCount > 0 ? round($totalCommission / $deliveredOrdersCount) : 0,

            // Cooks
            'total_cooks' => Cook::count(),
            'active_cooks' => Cook::where('active', true)->where('is_approved', true)->count(),
            'pending_cooks' => Cook::where('is_approved', false)->count(),
            'inactive_cooks' => Cook::where('active', false)->where('is_approved', true)->count(),

            // Orders
            'total_orders' => Order::count(),
            'delivered_orders' => $deliveredOrdersCount,
            'pending_orders' => Order::whereIn('status', [Order::STATUS_AWAITING_COOK, Order::STATUS_PREPARING, Order::STATUS_READY, Order::STATUS_ON_THE_WAY])->count(),
            'cancelled_orders' => Order::whereIn('status', [Order::STATUS_CANCELLED, Order::STATUS_REJECTED])->count(),

            // Dishes
            'total_dishes' => Dish::count(),
            'active_dishes' => Dish::where('is_active', true)->count(),
            'available_dishes' => Dish::where('is_active', true)->where('available_stock', '>', 0)->count(),
            'inactive_dishes' => Dish::where('is_active', false)->count(),
        ];

        // Top cocineros
        $top_cooks = Cook::with('user')
            ->withCount([
                'orders' => function ($query) {
                    $query->where('status', Order::STATUS_DELIVERED);
                }
            ])
            ->get()
            ->map(function ($cook) {
                $cook->total_sales = $cook->orders()->where('status', Order::STATUS_DELIVERED)->sum('total_amount');
                return $cook;
            })
            ->sortByDesc('total_sales')
            ->take(5);

        // Top platos
        $top_dishes = Dish::with('cook.user')
            ->withCount([
                'orders' => function ($query) {
                    $query->where('status', Order::STATUS_DELIVERED);
                }
            ])
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        $current_commission_rate = \App\Models\Setting::get('commission_rate', 15);

        return view('admin.statistics', compact('stats', 'top_cooks', 'top_dishes', 'current_commission_rate'));
    }

    /**
     * Lista de repartidores pendientes de aprobación
     */
    public function pendingDrivers()
    {
        $drivers = \App\Models\DeliveryDriver::with('user')
            ->where('is_approved', false)
            ->latest()
            ->paginate(20);

        return view('admin.drivers.pending', compact('drivers'));
    }

    /**
     * Lista de todos los repartidores
     */
    public function allDrivers()
    {
        $drivers = \App\Models\DeliveryDriver::with('user')
            ->where('is_approved', true)
            ->latest()
            ->paginate(20);

        $pending_count = \App\Models\DeliveryDriver::where('is_approved', false)->count();
        $approved_count = \App\Models\DeliveryDriver::where('is_approved', true)->count();

        return view('admin.drivers.index', compact('drivers', 'pending_count', 'approved_count'));
    }

    /**
     * Ver detalle de repartidor
     */
    public function showDriver($driverId)
    {
        $driver = \App\Models\DeliveryDriver::with('user', 'deliveries.order')->findOrFail($driverId);
        return view('admin.drivers.show', compact('driver'));
    }

    /**
     * Aprobar repartidor
     */
    public function approveDriver(Request $request, $driverId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.users.index');
        }

        $driver = \App\Models\DeliveryDriver::findOrFail($driverId);
        $driver->is_approved = true;
        $driver->save();

        if ($driver->user) {
            $driver->user->update(['last_rejection_reason' => null]);

            if ($driver->user->email) {
                try {
                    Mail::to($driver->user->email)->send(new \App\Mail\DriverApprovedMail($driver));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending driver approval email: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Repartidor aprobado exitosamente');
    }

    /**
     * Rechazar repartidor
     */
    public function rejectDriver(Request $request, $driverId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.users.index');
        }

        $driver = \App\Models\DeliveryDriver::findOrFail($driverId);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($driver->user) {
            $driver->user->update(['last_rejection_reason' => $request->rejection_reason]);

            if ($driver->user->email) {
                try {
                    Mail::to($driver->user->email)->send(new \App\Mail\DriverRejectedMail($driver, $request->rejection_reason));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending driver rejection email: " . $e->getMessage());
                }
            }
        }

        $driver->delete();

        return back()->with('success', 'Solicitud rechazada exitosamente');
    }

    /**
     * Gestión de Usuarios - Lista todos los usuarios
     */
    /**
     * Lista de todos los usuarios con filtros
     */
    public function allUsers(Request $request)
    {
        $query = User::with(['cook', 'deliveryDriver']);

        // Filtro por búsqueda general (Nombre, Email, Teléfono, Dirección)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
                // TODO:
                // También buscar en localidad/ciudad si existiera en un futuro o en address
                // Por ahora address cubre "localidad" si está en el string
            });
        }

        // Filtro por fecha de registro
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtro por rol
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filtro por estado (moderación / revisión)
        if ($request->has('status') && $request->status) {
            $status = $request->status;
            if ($status === 'pending') {
                $query->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('role', 'cook')
                           ->whereHas('cook', fn($c) => $c->where('is_approved', false));
                    })->orWhere(function ($q3) {
                        $q3->where('role', 'delivery_driver')
                           ->whereHas('deliveryDriver', fn($d) => $d->where('is_approved', false));
                    });
                });
            } elseif ($status === 'pending_cooks') {
                $query->where('role', 'cook')
                      ->whereHas('cook', fn($c) => $c->where('is_approved', false));
            } elseif ($status === 'pending_drivers') {
                $query->where('role', 'delivery_driver')
                      ->whereHas('deliveryDriver', fn($d) => $d->where('is_approved', false));
            } elseif ($status === 'suspended') {
                $query->where('is_suspended', true);
            } elseif ($status === 'active') {
                $query->where('is_suspended', false)
                      ->where(function ($q) {
                          $q->where(fn($q2) => $q2->where('role', 'cook')->whereHas('cook', fn($c) => $c->where('is_approved', true)))
                            ->orWhere(fn($q3) => $q3->where('role', 'delivery_driver')->whereHas('deliveryDriver', fn($d) => $d->where('is_approved', true)))
                            ->orWhereNotIn('role', ['cook', 'delivery_driver']);
                      });
            }
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $pendingCooksCount = Cook::where('is_approved', false)->count();
        $pendingDriversCount = \App\Models\DeliveryDriver::where('is_approved', false)->count();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'cooks' => User::where('role', 'cook')->count(),
            'drivers' => User::where('role', 'delivery_driver')->count(),
            'customers' => User::where('role', 'customer')->count(),
            'pending_cooks' => $pendingCooksCount,
            'pending_drivers' => $pendingDriversCount,
            'pending' => $pendingCooksCount + $pendingDriversCount,
            'suspended' => User::where('is_suspended', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Suspender/Activar usuario
     */
    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);

        // No permitir suspender a otros administradores
        if ($user->isAdmin()) {
            return back()->with('error', 'No puedes suspender la cuenta de otro administrador');
        }

        // Toggle suspended status
        $user->is_suspended = !($user->is_suspended ?? false);
        $user->save();

        $status = $user->is_suspended ? 'suspendido' : 'activado';
        return back()->with('success', "Usuario {$status} exitosamente");
    }

    /**
     * Eliminar usuario
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        // No permitir eliminar a otros administradores
        if ($user->isAdmin()) {
            return back()->with('error', 'No puedes eliminar la cuenta de otro administrador');
        }

        // Eliminar relaciones asociadas
        if ($user->isCook() && $user->cook) {
            $user->cook->delete();
        }

        if ($user->isDeliveryDriver() && $user->deliveryDriver) {
            $user->deliveryDriver->delete();
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }
}
