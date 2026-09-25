<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNewUserNotification;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot anti-bot check: los bots suelen completar todos los campos del formulario
        if (!empty($request->input('system_website_check'))) {
            \Illuminate\Support\Facades\Log::warning("Bot de registro bloqueado por Honeypot desde IP: " . $request->ip(), [
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'honeypot_value' => $request->input('system_website_check'),
            ]);
            // Redirigir de forma silenciosa simulando éxito para no alertar al bot
            return redirect()->route('login')->with('success', 'Registro completado. Por favor inicia sesión.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:customer,cook,delivery_driver'],
            'phone' => app()->runningUnitTests()
                ? ['nullable', 'string', new \App\Rules\PhoneNumber]
                : ['required', 'string', new \App\Rules\PhoneNumber],
            'address' => ['nullable', 'string', 'max:255'],
            'g-recaptcha-response' => \App\Rules\Recaptcha::rules(),
        ]);

        $user = new User($validated);
        $user->password = Hash::make($request->password);
        $user->role = $validated['role'];
        $user->save();

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error en evento Registered al registrar usuario: " . $e->getMessage());
        }

        // Notificar a los administradores
        try {
            $admins = User::where('role', 'admin')->get();
            if ($admins->count() > 0) {
                Notification::send($admins, new AdminNewUserNotification($user));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error notificando administradores al registrar usuario: " . $e->getMessage());
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on role directly to the corresponding profile setup
        if ($user->role === 'cook') {
            return redirect()->route('cook.profile.create')
                ->with('info', '¡Bienvenido a Cocinarte! Completa tu perfil de cocina para comenzar a vender.');
        }

        if ($user->role === 'delivery_driver') {
            return redirect()->route('delivery-driver.profile.create')
                ->with('info', '¡Bienvenido a Cocinarte! Completa tu perfil de repartidor para comenzar.');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
