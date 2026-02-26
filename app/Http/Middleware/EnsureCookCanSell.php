<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Dish;

class EnsureCookCanSell
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // El middleware se aplicará a /cart/add/{dishId}
        $dishId = $request->route('dishId');

        if ($dishId) {
            $dish = Dish::with('cook')->find($dishId);
            if ($dish && $dish->cook) {
                if ($dish->cook->is_selling_blocked) {
                    return back()->with('error', 'El cocinero ha alcanzado su límite de ventas/pedidos mensual y no puede recibir más pedidos por ahora.');
                }
            }
        }

        return $next($request);
    }
}
