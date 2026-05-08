<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CookPrepController extends Controller
{
    /**
     * Mostrar la Hoja de Producción (Prep List)
     */
    public function index(Request $request)
    {
        $cook = auth()->user()->cook;
        
        // Filtro por fecha (opcional, default hoy + futuro próximo)
        $date = $request->get('date', now()->format('Y-m-d'));

        // Obtener items de órdenes que requieren preparación
        // Statuses: paid, awaiting_cook_acceptance, preparing, scheduled
        $prepItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('dishes', 'order_items.dish_id', '=', 'dishes.id')
            ->select(
                'dishes.name',
                'dishes.photo_url',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->where('orders.cook_id', $cook->id)
            ->whereIn('orders.status', [
                Order::STATUS_PAID, 
                Order::STATUS_AWAITING_COOK, 
                Order::STATUS_PREPARING, 
                Order::STATUS_SCHEDULED
            ])
            ->where(function($query) use ($date) {
                // Si es scheduled, filtramos por la fecha programada
                // Si no, por la fecha de creación (o simplemente incluimos todos los pendientes)
                $query->whereDate('orders.scheduled_time', $date)
                      ->orWhere(function($q) use ($date) {
                          $q->whereNull('orders.scheduled_time')
                            ->whereDate('orders.created_at', $date);
                      });
            })
            ->groupBy('dishes.id', 'dishes.name', 'dishes.photo_url')
            ->get();

        return view('cook.prep.index', compact('prepItems', 'date'));
    }
}
