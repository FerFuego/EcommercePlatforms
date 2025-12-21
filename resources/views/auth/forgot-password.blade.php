@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-2xl shadow-2xl mb-4">
                    <span class="text-4xl">🔑</span>
                </div>
                <h2
                    class="text-4xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    ¿Olvidaste tu Contraseña?
                </h2>
                <p class="text-gray-600 mt-2">Te enviaremos un enlace para restablecer tu contraseña</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 bg-gradient-to-r from-green-400 to-emerald-500 text-white px-4 py-3 rounded-xl">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                        Enviar Enlace de Restablecimiento
                    </button>
                </form>
            </div>

            <!-- Back to Login -->
            <p class="text-center mt-6 text-gray-600">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    ← Volver al inicio de sesión
                </a>
            </p>
        </div>
    </div>
@endsection