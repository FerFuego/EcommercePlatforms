@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4">
                    <span class="text-4xl">
                        <img src="{{ asset('assets/front/icon.png') }}" alt="Logo" class="h-20 w-100">
                    </span>
                </div>
                <h2
                    class="text-4xl font-bold bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                    Bienvenido de Vuelta
                </h2>
                <p class="text-gray-600 mt-2">Ingresa a tu cuenta para continuar</p>
            </div>

            <!-- Login Form Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 bg-gradient-to-r from-green-400 to-emerald-500 text-white px-4 py-3 rounded-xl">
                        {{ session('status') }}
                    </div>
                @endif

                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                autocomplete="username"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Contraseña
                            </label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required autocomplete="current-password"
                                    class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('password') border-red-500 @enderror">
                                <button type="button" onclick="togglePasswordVisibility('password', 'eyeIconPassword')"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                    title="Mostrar / Ocultar contraseña">
                                    <svg id="eyeIconPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember"
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-4 h-4">
                                <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            Iniciar Sesión
                        </button>
                    </form>

                    @push('scripts')
                    <script>
                        function togglePasswordVisibility(inputId, iconId) {
                            const input = document.getElementById(inputId);
                            const icon = document.getElementById(iconId);
                            if (!input || !icon) return;

                            if (input.type === 'password') {
                                input.type = 'text';
                                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 013.122-.563c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.49 4.385m-1.748 1.748L3 3l18 18" />';
                            } else {
                                input.type = 'password';
                                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                            }
                        }

                        document.getElementById('loginForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            const form = this;
                            window.getRecaptchaToken('login').then(token => {
                                document.getElementById('g-recaptcha-response').value = token;
                                form.submit();
                            }).catch(err => {
                                console.error(err);
                                form.submit(); // Fallback
                            });
                        });
                    </script>
                    @endpush
            </div>

            <!-- Register Link -->
            <p class="text-center mt-6 text-gray-600">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </div>
@endsection