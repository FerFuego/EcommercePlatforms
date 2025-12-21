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
                        <img src="{{ asset('storage/front/icon.png') }}" alt="Logo" class="h-20 w-100">
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

                <form method="POST" action="{{ route('login') }}">
                    @csrf

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
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('password') border-red-500 @enderror">
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