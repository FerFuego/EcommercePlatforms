<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $cook = auth()->user()->cook;
        $isPremium = $cook->hasFeature('advanced_stats');

        // Always get some basic stats to show behind the paywall or to show real ones if premium
        // We consider orders that are not cancelled or pending
        $ordersQuery = \App\Models\Order::where('cook_id', $cook->id)
            ->whereNotIn('status', ['pending', 'cancelled']);

        $totalOrders = (clone $ordersQuery)->count();
        $totalRevenue = (clone $ordersQuery)->sum('total_amount');
        
        // Let's assume a 10% platform commission if not specified
        $commissionRate = $cook->plan() ? ($cook->plan()->commission_percentage / 100) : 0.10;
        $netEarnings = $totalRevenue * (1 - $commissionRate);

        // Top dishes
        $topDishes = \App\Models\OrderItem::selectRaw('dish_id, sum(quantity) as total_sold')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.cook_id', $cook->id)
            ->whereNotIn('orders.status', ['pending', 'cancelled'])
            ->groupBy('dish_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('dish')
            ->get();

        // Heatmap data (Sales by day of week)
        $salesByDay = (clone $ordersQuery)
            ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->get()
            ->pluck('count', 'day')
            ->toArray();
            
        // Map DAYOFWEEK to standard array (1=Sun, 2=Mon... in MySQL)
        $heatmapData = [
            'Lunes' => $salesByDay[2] ?? 0,
            'Martes' => $salesByDay[3] ?? 0,
            'Miércoles' => $salesByDay[4] ?? 0,
            'Jueves' => $salesByDay[5] ?? 0,
            'Viernes' => $salesByDay[6] ?? 0,
            'Sábado' => $salesByDay[7] ?? 0,
            'Domingo' => $salesByDay[1] ?? 0,
        ];

        // Daily sales in the last 30 days
        $thirtyDaysAgo = now()->subDays(30)->startOfDay();
        $dailySalesQuery = \App\Models\OrderItem::selectRaw('DATE(orders.created_at) as date, sum(quantity) as total_sold')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.cook_id', $cook->id)
            ->whereNotIn('orders.status', ['pending', 'cancelled'])
            ->where('orders.created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
            
        // Fill empty days
        $dailySalesData = [];
        $dailySalesLabels = [];
        for ($i = 30; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $dailySalesLabels[] = now()->subDays($i)->format('d/m');
            $sale = $dailySalesQuery->firstWhere('date', $dateStr);
            $dailySalesData[] = $sale ? (int)$sale->total_sold : 0;
        }

        // Upcoming Festive Dates and Predictions
        $festiveDates = [
            ['name' => 'Día de la Madre', 'date' => 'Tercer Domingo de Octubre', 'suggested_dish' => 'Menú Familiar (Ej. Pastas / Asado)', 'suggested_qty' => 'Preparar 50% más de lo habitual'],
            ['name' => 'Día del Padre', 'date' => 'Tercer Domingo de Junio', 'suggested_dish' => 'Carnes / Parrilladas', 'suggested_qty' => 'Preparar 40% más de lo habitual'],
            ['name' => 'Día del Amigo', 'date' => '20 de Julio', 'suggested_dish' => 'Pizzas / Picadas', 'suggested_qty' => 'Alta demanda de porciones grandes'],
            ['name' => 'Fiestas Patrias (25 de Mayo)', 'date' => '25 de Mayo', 'suggested_dish' => 'Locro / Empanadas', 'suggested_qty' => 'Preparar stock máximo posible'],
            ['name' => 'Navidad', 'date' => '24 y 25 de Diciembre', 'suggested_dish' => 'Vitel Toné / Platos Fríos', 'suggested_qty' => 'Ofrecer menú cerrado anticipado'],
        ];

        return view('cook.analytics.index', compact(
            'isPremium', 'totalOrders', 'totalRevenue', 'netEarnings', 'topDishes', 'heatmapData', 'dailySalesLabels', 'dailySalesData', 'festiveDates'
        ));
    }
}
