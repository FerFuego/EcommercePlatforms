@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <a href="{{ route('cook.subscription.index') }}"
                class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-primary mb-6 transition-all group">
                <svg class="h-5 w-5 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a planes
            </a>

            <div
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-8 sm:p-12">
                    <div class="text-center mb-10">
                        <h1 class="text-4xl font-black mb-3">
                            <span
                                class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                                Confirma tu Suscripción
                            </span>
                        </h1>
                        <p class="text-gray-500 font-medium">Estás a un paso de mejorar tu cuenta y desbloquear nuevas
                            herramientas.</p>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-8 mb-10 border border-gray-100 dark:border-gray-800 shadow-inner">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-center mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="text-center sm:text-left mb-4 sm:mb-0">
                                <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                    {{ $plan->name }}
                                </h3>
                                <p class="text-sm font-bold text-purple-600/70 uppercase tracking-widest mt-1">
                                    Facturación {{ $plan->billing_period == 'monthly' ? 'mensual' : 'anual' }}
                                </p>
                            </div>
                            <div class="text-4xl font-black text-gray-900 dark:text-white">
                                ${{ number_format($plan->price, 2) }}
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                            <div class="flex flex-col">
                                <span class="text-gray-400 font-bold uppercase text-[10px] tracking-widest mb-1">Límite
                                    Ventas</span>
                                <span
                                    class="font-black text-gray-800 dark:text-gray-200">{{ $plan->monthly_sales_limit ? '$' . number_format($plan->monthly_sales_limit, 0) : 'Ilimitado' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-400 font-bold uppercase text-[10px] tracking-widest mb-1">Límite
                                    Pedidos</span>
                                <span
                                    class="font-black text-gray-800 dark:text-gray-200">{{ $plan->monthly_orders_limit ?? 'Ilimitado' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-gray-400 font-bold uppercase text-[10px] tracking-widest mb-1">Comisión</span>
                                <span
                                    class="font-black text-gray-800 dark:text-gray-200">{{ rtrim(rtrim(number_format($plan->commission_percentage, 2), '0'), '.') }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    @if($stripeConfigured || $mpConfigured)
                        <div class="mb-10">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 uppercase tracking-tight">
                                Selecciona tu método de pago</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                @if($stripeConfigured)
                                    <label
                                        class="relative flex flex-col items-center p-6 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl cursor-pointer hover:border-purple-400 transition-all group has-[:checked]:border-purple-600 has-[:checked]:ring-4 has-[:checked]:ring-purple-500/10 has-[:checked]:shadow-xl shadow-sm hover:shadow-md">
                                        <input type="radio" name="payment_method" value="stripe" class="sr-only" required {{ !$mpConfigured ? 'checked' : '' }}>
                                        <div
                                            class="w-16 h-16 mb-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-2xl group-hover:scale-110 transition-transform">
                                            <svg viewBox="0 0 40 40" class="w-10 h-10">
                                                <path fill="#635bff"
                                                    d="M34.7 20.3c0-4.6-2.3-7.2-6.5-7.2-4.1 0-6.9 2.7-6.9 7.2 0 5.4 3.7 7.2 8.3 7.2 1.5 0 3-.2 4.1-.7l-.4-2.8c-1 .4-2 .5-3.1.5-2.6 0-5.1-.7-5.1-4.2h10.4c-.1-1.3-.8-4.5-.8-4.5zm-5.6-2.6c0-.1 0-.1.1-.1.9 0 1.5.7 1.7 1.7h-3.4c.1-1.1.7-1.6 1.6-1.6zm-17 1.3c0-2 1.3-3.1 3.5-3.1 1.2 0 2 .1 2.9.5l-.3-2.7c-.7-.3-1.6-.5-3-.5-4.2 0-6.8 2.3-6.8 6.5 0 5.8 3.7 7.2 8.3 7.2 1 0 2-.1 2.8-.4l-.4-2.8c-.8.3-1.4.4-2.3.4-2.7 0-4.7-.9-4.7-4.1v-.8c-.1-.2-.1-.2-.1-.2z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="font-black text-gray-900 dark:text-gray-100 uppercase tracking-tight">Stripe</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest text-center">Tarjetas
                                            Internacionales</span>
                                    </label>
                                @endif

                                @if($mpConfigured)
                                    <label
                                        class="relative flex flex-col items-center p-6 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl cursor-pointer hover:border-blue-400 transition-all group has-[:checked]:border-blue-600 has-[:checked]:ring-4 has-[:checked]:ring-blue-500/10 has-[:checked]:shadow-xl shadow-sm hover:shadow-md">
                                        <input type="radio" name="payment_method" value="mercadopago" class="sr-only" required {{ !$stripeConfigured ? 'checked' : '' }}>
                                        <div
                                            class="w-16 h-16 mb-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-2xl group-hover:scale-110 transition-transform">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/bc/Mercado_Pago_logo.svg"
                                                alt="Mercado Pago" class="h-8">
                                        </div>
                                        <span
                                            class="font-black text-gray-900 dark:text-gray-100 uppercase tracking-tight">MercadoPago</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest text-center">Pagos
                                            Locales AR/CL/BR</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    @else
                        <div
                            class="bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 p-6 rounded-2xl mb-10 flex items-center">
                            <svg class="h-8 w-8 text-red-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <p class="text-sm font-bold text-red-800 dark:text-red-300">
                                Los métodos de pago no están disponibles actualmente. Contacta al soporte técnico.
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('cook.subscription.process', $plan) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-center bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 text-white px-8 py-5 rounded-2xl font-black text-lg uppercase tracking-widest shadow-2xl hover:shadow-purple-500/30 hover:-translate-y-1 transform transition-all duration-300">
                            Pagar Ahora - ${{ number_format($plan->price, 2) }}
                        </button>
                    </form>

                    <div class="mt-8 flex flex-col items-center space-y-3">
                        <div class="flex items-center space-x-2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-widest italic">Pago seguro y cifrado
                                SSL</span>
                        </div>
                        <div class="flex space-x-4 opacity-30 grayscale hover:grayscale-0 transition-all duration-500">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Visa_Inc._logo_%282021%E2%80%93present%29.svg"
                                alt="Visa" class="h-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                                alt="Mastercard" class="h-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg"
                                alt="PayPal" class="h-4">
                            <img src="{{ asset('assets/front/Mercado_Pago.svg') }}" alt="Mercago Pago" class="h-5">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection