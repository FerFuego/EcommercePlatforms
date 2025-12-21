<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Crear review para un pedido
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verificar que el pedido pertenece al usuario actual
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        // Verificar que el pedido puede ser revisado
        if (!$order->canBeReviewed()) {
            return back()->with('error', 'Este pedido no puede ser calificado');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'order_id' => $order->id,
            'customer_id' => auth()->id(),
            'cook_id' => $order->cook_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', '¡Gracias por tu calificación!');
    }

    /**
     * Ver reviews de un cocinero
     */
    public function cookReviews($cookId)
    {
        $cook = \App\Models\Cook::with('user')->findOrFail($cookId);

        $reviews = Review::where('cook_id', $cookId)
            ->with('customer', 'order')
            ->latest()
            ->paginate(20);

        return view('reviews.cook-reviews', compact('reviews', 'cook'));
    }
}
