<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cook;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Listar cocineros favoritos
     */
    public function index()
    {
        $favorites = Auth::user()->favoriteCooks()->with('user')->paginate(12);
        return view('favorites.index', compact('favorites'));
    }

    /**
     * Alternar favorito
     */
    public function toggle(Cook $cook)
    {
        $user = Auth::user();

        if ($user->favoriteCooks()->where('cook_id', $cook->id)->exists()) {
            $user->favoriteCooks()->detach($cook->id);
            $status = 'removed';
            $message = 'Eliminado de tus favoritos';
        } else {
            $user->favoriteCooks()->attach($cook->id);
            $status = 'added';
            $message = '¡Agregado a tus favoritos!';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
}
