<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;

class DishController extends Controller
{
    /**
     * Mostrar lista de platos del cocinero
     */
    public function index()
    {
        $cook = auth()->user()->cook;
        $dishes = $cook->dishes()->latest()->paginate(12);

        return view('cook.dishes.index', compact('dishes'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('cook.dishes.create');
    }

    /**
     * Guardar nuevo plato
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'photo' => 'required|image|max:2048',
            'available_stock' => 'required|integer|min:0',
            'available_days' => 'nullable|array',
            'available_days.*' => 'integer|between:1,7',
            'preparation_time_minutes' => 'required|integer|min:10',
            'delivery_method' => 'required|in:pickup,delivery,both',
            'diet_tags' => 'nullable|array',
            'option_groups' => 'nullable|array',
        ]);

        $cook = auth()->user()->cook;

        // Subir foto del plato
        $photoPath = $request->file('photo')->store('dishes', 'public');

        $dish = $cook->dishes()->create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'photo_url' => $photoPath,
            'available_stock' => $request->available_stock,
            'available_days' => $request->available_days,
            'preparation_time_minutes' => $request->preparation_time_minutes,
            'delivery_method' => $request->delivery_method,
            'diet_tags' => $request->diet_tags ?? [],
            'is_active' => true,
        ]);

        // Guardar Grupos de Opciones
        if ($request->has('option_groups')) {
            foreach ($request->option_groups as $groupData) {
                $group = $dish->optionGroups()->create([
                    'name' => $groupData['name'],
                    'min_options' => $groupData['min_options'] ?? 0,
                    'max_options' => $groupData['max_options'] ?? 1,
                    'is_required' => isset($groupData['is_required']),
                ]);

                if (isset($groupData['options'])) {
                    foreach ($groupData['options'] as $optionData) {
                        $group->options()->create([
                            'name' => $optionData['name'],
                            'additional_price' => $optionData['additional_price'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('cook.dishes.index')
            ->with('success', '¡Plato creado exitosamente!');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $cook = auth()->user()->cook;
        $dish = $cook->dishes()->with('optionGroups.options')->findOrFail($id);

        return view('cook.dishes.edit', compact('dish'));
    }

    /**
     * Actualizar plato
     */
    public function update(Request $request, $id)
    {
        $cook = auth()->user()->cook;
        $dish = $cook->dishes()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'available_stock' => 'required|integer|min:0',
            'available_days' => 'nullable|array',
            'available_days.*' => 'integer|between:1,7',
            'preparation_time_minutes' => 'required|integer|min:10',
            'delivery_method' => 'required|in:pickup,delivery,both',
            'diet_tags' => 'nullable|array',
            'is_active' => 'boolean',
            'option_groups' => 'nullable|array',
        ]);

        // Actualizar foto si se sube una nueva
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('dishes', 'public');
            $dish->photo_url = $photoPath;
        }

        $dish->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'available_stock' => $request->available_stock,
            'available_days' => $request->available_days,
            'preparation_time_minutes' => $request->preparation_time_minutes,
            'delivery_method' => $request->delivery_method,
            'diet_tags' => $request->diet_tags ?? [],
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Sincronizar Opciones (Simplificado: borrar y recrear para esta fase)
        $dish->optionGroups()->delete();
        if ($request->has('option_groups')) {
            foreach ($request->option_groups as $groupData) {
                $group = $dish->optionGroups()->create([
                    'name' => $groupData['name'],
                    'min_options' => $groupData['min_options'] ?? 0,
                    'max_options' => $groupData['max_options'] ?? 1,
                    'is_required' => isset($groupData['is_required']),
                ]);

                if (isset($groupData['options'])) {
                    foreach ($groupData['options'] as $optionData) {
                        $group->options()->create([
                            'name' => $optionData['name'],
                            'additional_price' => $optionData['additional_price'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('cook.dishes.index')
            ->with('success', 'Plato actualizado exitosamente');
    }

    /**
     * Eliminar plato
     */
    public function destroy($id)
    {
        $cook = auth()->user()->cook;
        $dish = $cook->dishes()->findOrFail($id);

        $dish->delete();

        return redirect()->route('cook.dishes.index')
            ->with('success', 'Plato eliminado');
    }

    /**
     * Activar/Desactivar plato
     */
    public function toggleActive($id)
    {
        $cook = auth()->user()->cook;
        $dish = $cook->dishes()->findOrFail($id);

        $dish->is_active = !$dish->is_active;
        $dish->save();

        return response()->json([
            'success' => true,
            'is_active' => $dish->is_active
        ]);
    }

    /**
     * Actualizar stock
     */
    public function updateStock(Request $request, $id)
    {
        $cook = auth()->user()->cook;
        $dish = $cook->dishes()->findOrFail($id);

        $request->validate([
            'available_stock' => 'required|integer|min:0|max:100',
        ]);

        $dish->available_stock = $request->available_stock;
        $dish->save();

        return response()->json(['success' => true]);
    }
}
