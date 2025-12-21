<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryDriverController extends Controller
{
    /**
     * Dashboard del repartidor
     */
    public function index()
    {
        $driver = auth()->user()->deliveryDriver;

        if (!$driver) {
            return redirect()->route('delivery-driver.profile.create')
                ->with('info', 'Completa tu perfil de repartidor para comenzar');
        }

        // Estadísticas
        $todayDeliveries = $driver->deliveries()
            ->whereDate('created_at', today())
            ->where('status', 'delivered')
            ->count();

        $pendingDeliveries = $driver->deliveries()
            ->whereIn('status', ['assigned', 'picked_up', 'on_the_way'])
            ->count();

        $todayEarnings = $driver->deliveries()
            ->whereDate('delivered_at', today())
            ->where('status', 'delivered')
            ->sum('delivery_fee');

        $weekEarnings = $driver->deliveries()
            ->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'delivered')
            ->sum('delivery_fee');

        $monthEarnings = $driver->deliveries()
            ->whereMonth('delivered_at', now()->month)
            ->whereYear('delivered_at', now()->year)
            ->where('status', 'delivered')
            ->sum('delivery_fee');

        return view('delivery-driver.dashboard', compact(
            'driver',
            'todayDeliveries',
            'pendingDeliveries',
            'todayEarnings',
            'weekEarnings',
            'monthEarnings'
        ));
    }

    /**
     * Mostrar formulario de creación de perfil
     */
    public function createProfile()
    {
        if (auth()->user()->deliveryDriver) {
            return redirect()->route('delivery-driver.dashboard');
        }

        return view('delivery-driver.profile.create');
    }

    /**
     * Guardar perfil de repartidor
     */
    public function storeProfile(Request $request)
    {
        $validated = $request->validate([
            'dni_number' => 'required|string|unique:delivery_drivers,dni_number',
            'dni_photo' => 'required|image|max:2048',
            'profile_photo' => 'nullable|image|max:2048',
            'vehicle_type' => 'required|in:bicycle,motorcycle,car',
            'vehicle_plate' => 'required_if:vehicle_type,motorcycle,car|nullable|string',
            'vehicle_photo' => 'nullable|image|max:2048',
            'location_lat' => 'required|numeric|between:-90,90',
            'location_lng' => 'required|numeric|between:-180,180',
            'coverage_radius_km' => 'required|integer|min:1|max:50',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_type' => 'nullable|in:checking,savings',
            'cbu_cvu' => 'nullable|string',
        ]);

        // Subir fotos
        $validated['dni_photo'] = $request->file('dni_photo')->store('delivery-drivers/dni', 'public');

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('delivery-drivers/profiles', 'public');
        }

        if ($request->hasFile('vehicle_photo')) {
            $validated['vehicle_photo'] = $request->file('vehicle_photo')->store('delivery-drivers/vehicles', 'public');
        }

        $validated['user_id'] = auth()->id();

        DeliveryDriver::create($validated);

        return redirect()->route('delivery-driver.dashboard')
            ->with('success', 'Perfil creado exitosamente. Está pendiente de aprobación por el administrador.');
    }

    /**
     * Mostrar formulario de edición de perfil
     */
    public function editProfile()
    {
        $driver = auth()->user()->deliveryDriver;

        if (!$driver) {
            return redirect()->route('delivery-driver.profile.create');
        }

        return view('delivery-driver.profile.edit', compact('driver'));
    }

    /**
     * Actualizar perfil de repartidor
     */
    public function updateProfile(Request $request)
    {
        $driver = auth()->user()->deliveryDriver;

        $validated = $request->validate([
            'profile_photo' => 'nullable|image|max:2048',
            'vehicle_type' => 'required|in:bicycle,motorcycle,car',
            'vehicle_plate' => 'required_if:vehicle_type,motorcycle,car|nullable|string',
            'vehicle_photo' => 'nullable|image|max:2048',
            'location_lat' => 'required|numeric|between:-90,90',
            'location_lng' => 'required|numeric|between:-180,180',
            'coverage_radius_km' => 'required|integer|min:1|max:50',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_type' => 'nullable|in:checking,savings',
            'cbu_cvu' => 'nullable|string',
        ]);

        // Actualizar fotos si se suben nuevas
        if ($request->hasFile('profile_photo')) {
            if ($driver->profile_photo) {
                Storage::disk('public')->delete($driver->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('delivery-drivers/profiles', 'public');
        }

        if ($request->hasFile('vehicle_photo')) {
            if ($driver->vehicle_photo) {
                Storage::disk('public')->delete($driver->vehicle_photo);
            }
            $validated['vehicle_photo'] = $request->file('vehicle_photo')->store('delivery-drivers/vehicles', 'public');
        }

        $driver->update($validated);

        return redirect()->route('delivery-driver.dashboard')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Alternar disponibilidad (online/offline)
     */
    public function toggleAvailability(Request $request)
    {
        $driver = auth()->user()->deliveryDriver;
        $driver->toggleAvailability();

        return response()->json([
            'success' => true,
            'is_available' => $driver->is_available
        ]);
    }

    /**
     * Dashboard de ganancias
     */
    public function earnings(Request $request)
    {
        $driver = auth()->user()->deliveryDriver;

        // Filtros
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $deliveries = $driver->deliveries()
            ->with('order.customer')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->get();

        $totalEarnings = $deliveries->sum('delivery_fee');
        $totalDeliveries = $deliveries->count();
        $averagePerDelivery = $totalDeliveries > 0 ? $totalEarnings / $totalDeliveries : 0;

        return view('delivery-driver.earnings.index', compact(
            'driver',
            'deliveries',
            'totalEarnings',
            'totalDeliveries',
            'averagePerDelivery',
            'startDate',
            'endDate'
        ));
    }
}
