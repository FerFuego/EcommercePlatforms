<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->user()->isAdmin()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        if ($request->user()->isCook()) {
            if (!$request->user()->cook) {
                return redirect()->intended(route('cook.profile.create', absolute: false))
                    ->with('info', 'Por favor completa los datos de tu cocina para comenzar.');
            }
            return redirect()->intended(route('cook.dashboard', absolute: false));
        }

        if ($request->user()->isDeliveryDriver()) {
            if (!$request->user()->deliveryDriver) {
                return redirect()->intended(route('delivery-driver.profile.create', absolute: false))
                    ->with('info', 'Por favor completa tus datos de repartidor para comenzar.');
            }
            return redirect()->intended(route('delivery-driver.dashboard', absolute: false));
        }

        if ($request->user()->isCustomer()) {
            return redirect()->intended(route('orders.my', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
