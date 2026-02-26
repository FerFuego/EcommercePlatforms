@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <a href="{{ route('cook.subscription.index') }}"
                class="inline-flex items-center text-sm text-gray-500 hover:text-primary mb-6">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a planes
            </a>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Confirma tu Suscripción</h1>
                    <p class="mt-2 text-gray-500">Estás a un paso de mejorar tu cuenta.</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8 border border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-500">Facturación
                                {{ $plan->billing_period == 'monthly' ? 'mensual' : 'anual' }}
                            </p>
                        </div>
                        <div class="text-2xl font-bold text-primary">
                            ${{ number_format($plan->price, 2) }}
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex justify-between">
                            <span>Límite de Ventas</span>
                            <span
                                class="font-medium">{{ $plan->monthly_sales_limit ? '$' . number_format($plan->monthly_sales_limit, 2) : 'Ilimitado' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Límite de Pedidos</span>
                            <span class="font-medium">{{ $plan->monthly_orders_limit ?? 'Ilimitado' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Comisión por Venta</span>
                            <span
                                class="font-medium">{{ rtrim(rtrim(number_format($plan->commission_percentage, 2), '0'), '.') }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                @if($stripeConfigured || $mpConfigured)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Selecciona tu método de pago</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($stripeConfigured)
                                <label class="relative flex flex-col items-center p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-purple-500 transition-all group has-[:checked]:border-purple-500 has-[:checked]:ring-2 has-[:checked]:ring-purple-200">
                                    <input type="radio" name="payment_method" value="stripe" class="sr-only" required {{ !$mpConfigured ? 'checked' : '' }}>
                                    <div class="w-12 h-12 mb-2 flex items-center justify-center">
                                        <svg viewBox="0 0 40 40" class="w-10 h-10"><path fill="#635bff" d="M34.7 20.3c0-4.6-2.3-7.2-6.5-7.2-4.1 0-6.9 2.7-6.9 7.2 0 5.4 3.7 7.2 8.3 7.2 1.5 0 3-.2 4.1-.7l-.4-2.8c-1 .4-2 .5-3.1.5-2.6 0-5.1-.7-5.1-4.2h10.4c-.1-1.3-.8-4.5-.8-4.5zm-5.6-2.6c0-.1 0-.1.1-.1.9 0 1.5.7 1.7 1.7h-3.4c.1-1.1.7-1.6 1.6-1.6zm-17 1.3c0-2 1.3-3.1 3.5-3.1 1.2 0 2 .1 2.9.5l-.3-2.7c-.7-.3-1.6-.5-3-.5-4.2 0-6.8 2.3-6.8 6.5 0 5.8 3.7 7.2 8.3 7.2 1 0 2-.1 2.8-.4l-.4-2.8c-.8.3-1.4.4-2.3.4-2.7 0-4.7-.9-4.7-4.1v-.8c-.1-.2-.1-.2-.1-.2z"/></svg>
                                    </div>
                                    <span class="font-bold text-gray-700 dark:text-gray-200">Stripe</span>
                                    <span class="text-xs text-gray-400">Tarjetas de Crédito / Débito</span>
                                </label>
                            @endif

                            @if($mpConfigured)
                                <label class="relative flex flex-col items-center p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-blue-500 transition-all group has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                                    <input type="radio" name="payment_method" value="mercadopago" class="sr-only" required {{ !$stripeConfigured ? 'checked' : '' }}>
                                    <div class="w-12 h-12 mb-2 flex items-center justify-center">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/bc/Mercado_Pago_logo.svg" alt="Mercado Pago" class="h-8">
                                    </div>
                                    <span class="font-bold text-gray-700 dark:text-gray-200">MercadoPago</span>
                                    <span class="text-xs text-gray-400">Todo tipo de pagos locales</span>
                                </label>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    No hay métodos de pago configurados por el administrador. Contacta al soporte.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('cook.subscription.process', $plan) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="mt-auto w-full text-center bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-4 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                        Confirmar Suscripción - ${{ number_format($plan->price, 2) }}
                    </button>
                </form>

                <div class="mt-6 flex justify-center items-center space-x-2 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span class="text-xs">Pago seguro y cifrado (Stripe / MercadoPago)</span>
                </div>
            </div>
        </div>
    </div>
@endsection