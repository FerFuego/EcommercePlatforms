<?php

namespace App\Http\Controllers;

use App\Models\Cook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CookDashboardController extends Controller
{
    /**
     * Mostrar dashboard del cocinero
     */
    public function index()
    {
        $cook = auth()->user()->cook;

        if (!$cook) {
            return redirect()->route('cook.profile.create')
                ->with('info', 'Completa tu perfil de cocinero para comenzar');
        }

        // Estadísticas
        $totalOrders = $cook->orders()->count();
        $pendingOrders = $cook->orders()->where('status', 'awaiting_cook_acceptance')->count();
        $todayOrders = $cook->orders()->whereDate('created_at', today())->count();
        $totalRevenue = $cook->orders()
            ->where('status', 'delivered')
            ->sum('subtotal');

        return view('cook.dashboard', compact('cook', 'totalOrders', 'pendingOrders', 'todayOrders', 'totalRevenue'));
    }

    /**
     * Mostrar formulario de creación de perfil
     */
    public function createProfile()
    {
        if (auth()->user()->cook) {
            return redirect()->route('cook.dashboard');
        }

        return view('cook.profile.create');
    }

    /**
     * Guardar perfil de cocinero
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'bio' => 'required|string|max:1000',
            'dni_photo' => 'required|image|max:2048',
            'kitchen_photos' => 'required|array|min:3|max:5',
            'kitchen_photos.*' => 'image|max:2048',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
            'address' => 'required|string|max:255',
            'coverage_radius_km' => 'required|numeric|min:1|max:50',
            'payment_details' => 'required|string',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'terms' => 'accepted',
        ]);

        // Subir DNI
        $dniPath = Storage::disk('uploads')->putFile('cooks/dni', $request->file('dni_photo'));

        // Subir fotos de cocina
        $kitchenPhotos = [];
        foreach ($request->file('kitchen_photos') as $photo) {
            $kitchenPhotos[] = Storage::disk('uploads')->putFile('cooks/kitchens', $photo);
        }

        // Actualizar dirección en el usuario
        auth()->user()->update(['address' => $request->address]);

        // Crear perfil de cocinero
        $cook = Cook::create([
            'user_id' => auth()->id(),
            'bio' => $request->bio,
            'dni_photo' => $dniPath,
            'kitchen_photos' => $kitchenPhotos,
            'location_lat' => $request->location_lat,
            'location_lng' => $request->location_lng,
            'coverage_radius_km' => $request->coverage_radius_km,
            'payout_method' => 'CBU/Alias',
            'payout_details' => ['identifier' => $request->payment_details],
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'food_handler_declaration' => true,
            'is_approved' => false, // Requiere aprobación de admin
            'active' => false,
        ]);

        return redirect()->route('cook.dashboard')
            ->with('success', '¡Perfil creado! Tu solicitud será revisada por un administrador.');
    }

    /**
     * Mostrar formulario de edición de perfil
     */
    public function editProfile()
    {
        $cook = auth()->user()->cook;

        if (!$cook) {
            return redirect()->route('cook.profile.create');
        }

        return view('cook.profile.edit', compact('cook'));
    }

    /**
     * Actualizar perfil de cocinero
     */
    public function updateProfile(Request $request)
    {
        $cook = auth()->user()->cook;

        $request->validate([
            'bio' => 'required|string|max:1000',
            'kitchen_photos' => 'nullable|array|max:5',
            'kitchen_photos.*' => 'image|max:2048',
            'coverage_radius_km' => 'required|numeric|min:1|max:50',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
            'address' => 'required|string|max:255',
            'active' => 'boolean',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
        ]);

        $data = [
            'bio' => $request->bio,
            'coverage_radius_km' => $request->coverage_radius_km,
            'location_lat' => $request->location_lat,
            'location_lng' => $request->location_lng,
            'active' => $request->boolean('active'),
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
        ];

        // Actualizar fotos de cocina si se suben nuevas
        if ($request->hasFile('kitchen_photos')) {
            $kitchenPhotos = [];
            foreach ($request->file('kitchen_photos') as $photo) {
                $kitchenPhotos[] = Storage::disk('uploads')->putFile('cooks/kitchens', $photo);
            }
            // Merge with existing photos
            $currentPhotos = $cook->kitchen_photos ?? [];
            $data['kitchen_photos'] = array_merge($currentPhotos, $kitchenPhotos);
        }

        $cook->update($data);

        // Actualizar dirección en el usuario
        auth()->user()->update(['address' => $request->address]);

        return back()->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Eliminar una foto de cocina
     */
    public function deleteKitchenPhoto(Request $request)
    {
        $cook = auth()->user()->cook;
        $photoPath = $request->photo;

        $photos = $cook->kitchen_photos ?? [];
        $photos = array_filter($photos, fn($p) => $p !== $photoPath);

        $cook->kitchen_photos = array_values($photos);
        $cook->save();

        // Eliminar archivo
        Storage::disk('uploads')->delete($photoPath);

        return response()->json(['success' => true]);
    }

    /**
     * Alternar estado activo del perfil
     */
    public function toggleActive(Request $request)
    {
        $cook = auth()->user()->cook;
        $cook->active = $request->boolean('active');
        $cook->save();

        return response()->json(['success' => true, 'active' => $cook->active]);
    }
}
