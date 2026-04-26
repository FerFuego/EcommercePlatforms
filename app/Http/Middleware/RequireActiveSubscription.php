<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || !$user->cook) {
            return $next($request); // Not a cook, ignore or handle as guest
        }

        $cook = $user->cook;
        $subscription = $cook->currentSubscription;

        // If no subscription or not active, redirect to plans page
        if (!$subscription || $subscription->status !== 'active') {
            return redirect()->route('cook.subscription.index')
                ->with('warning', 'Esta función requiere una suscripción activa. Elige un plan para continuar.');
        }

        return $next($request);
    }
}
