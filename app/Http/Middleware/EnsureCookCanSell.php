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
            $dish = Dish::with(['cook.user'])->find($dishId);

            if (!$dish) {
                return $this->errorResponse($request, 'El plato seleccionado no existe o no está disponible.');
            }

            if ($dish->cook) {
                $cook = $dish->cook;

                // 1. Usuario suspendido
                if ($cook->user && $cook->user->is_suspended) {
                    return $this->errorResponse($request, 'El cocinero se encuentra suspendido temporalmente.');
                }

                // 2. Cocinero no aprobado
                if (!$cook->is_approved) {
                    return $this->errorResponse($request, 'La cocina aún no ha sido aprobada para recibir pedidos.');
                }

                // 3. Cocinero inactivo / cerrado
                if (!$cook->active) {
                    return $this->errorResponse($request, 'La cocina se encuentra cerrada en este momento.');
                }

                // 4. Límite de suscripción/ventas superado
                if ($cook->isSellingBlocked()) {
                    return $this->errorResponse($request, 'El cocinero está temporalmente fuera de servicio (alcanzó el límite de su plan).');
                }
            }
        }

        return $next($request);
    }

    private function errorResponse(Request $request, string $message): Response
    {
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        return back()->with('error', $message);
    }
}
