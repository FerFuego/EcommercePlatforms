@extends('layouts.app')

@section('title', 'Verificar Email')

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
                    Verifica tu Email
                </h2>
                <p class="text-gray-600 mt-2">
                    ¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico
                    haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos
                    otro.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.') }}
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between flex-wrap gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            Reenviar Email de Verificación
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline font-medium">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection