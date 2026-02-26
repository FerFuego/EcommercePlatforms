@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Suscripción y Límites</h1>
            <a href="{{ route('cook.subscription.history') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors text-sm font-semibold">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Historial de Pagos
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                {{ session('info') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Current Plan Overview -->
            <div class="md:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-b-4 border-purple-500">
                <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Plan Actual</h2>
                @if($activePlan)
                    <p class="text-3xl font-bold text-primary mb-1">{{ $activePlan->name }}</p>
                    <p class="text-gray-500 mb-4">${{ number_format($activePlan->price, 2) }} /
                        {{ $activePlan->billing_period == 'monthly' ? 'mes' : 'año' }}
                    </p>

                    <div class="mb-4">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cook->is_selling_blocked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $cook->is_selling_blocked ? 'Ventas Bloqueadas (Límite Alcanzado)' : 'Activo' }}
                        </span>
                    </div>
                @else
                    <p class="text-lg text-gray-500 mb-4">No tienes un plan activo.</p>
                @endif
            </div>

            <!-- Metrics Overview -->
            <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-b-4 border-purple-500">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Uso del Mes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Sales limit progress -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-300">Ventas Acumuladas</span>
                            <span
                                class="text-sm font-bold text-gray-800 dark:text-white">${{ number_format($cook->monthly_sales_accumulated, 2) }}</span>
                        </div>
                        @if($activePlan && $activePlan->monthly_sales_limit)
                            @php
                                $salesPercent = min(100, ($cook->monthly_sales_accumulated / $activePlan->monthly_sales_limit) * 100);
                                $salesColor = $salesPercent >= 100 ? 'bg-red-500' : ($salesPercent >= 80 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600 mb-1">
                                <div class="{{ $salesColor }} h-2.5 rounded-full" style="width: {{ $salesPercent }}%"></div>
                            </div>
                            <div class="text-right text-xs text-gray-500">Límite:
                                ${{ number_format($activePlan->monthly_sales_limit, 2) }}</div>
                        @else
                            <div class="text-sm text-green-600 font-medium">Ilimitadas</div>
                        @endif
                    </div>

                    <!-- Orders limit progress -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-300">Pedidos Acumulados</span>
                            <span
                                class="text-sm font-bold text-gray-800 dark:text-white">{{ $cook->monthly_orders_accumulated }}</span>
                        </div>
                        @if($activePlan && $activePlan->monthly_orders_limit)
                            @php
                                $ordersPercent = min(100, ($cook->monthly_orders_accumulated / $activePlan->monthly_orders_limit) * 100);
                                $ordersColor = $ordersPercent >= 100 ? 'bg-red-500' : ($ordersPercent >= 80 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600 mb-1">
                                <div class="{{ $ordersColor }} h-2.5 rounded-full" style="width: {{ $ordersPercent }}%"></div>
                            </div>
                            <div class="text-right text-xs text-gray-500">Límite: {{ $activePlan->monthly_orders_limit }}
                                pedidos</div>
                        @else
                            <div class="text-sm text-green-600 font-medium">Ilimitados</div>
                        @endif
                    </div>

                </div>

                <div class="mt-4 text-xs text-gray-400">
                    Los medidores se reinician automáticamente el primer día de cada mes (Último reinicio:
                    {{ $cook->sales_reset_at ? $cook->sales_reset_at->format('d/m/Y') : 'Nunca' }}).
                </div>
            </div>
        </div>

        <!-- Available Plans -->
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Mejora tu Plan</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            @foreach($plans as $plan)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col {{ $activePlan && $activePlan->id === $plan->id ? 'ring-2 ring-primary ring-offset-2' : '' }}">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                        <p
                            class="mt-4 flex items-baseline justify-center text-4xl font-extrabold text-gray-900 dark:text-white">
                            ${{ number_format($plan->price, 0) }}
                            <span
                                class="ml-1 text-xl font-medium text-gray-500 dark:text-gray-400">/{{ $plan->billing_period == 'monthly' ? 'mes' : 'año' }}</span>
                        </p>
                        <p class="mt-2 text-sm text-gray-500">Comisión:
                            {{ rtrim(rtrim(number_format($plan->commission_percentage, 2), '0'), '.') }}% por pedido
                        </p>
                    </div>

                    <div class="p-6 flex-1 flex flex-col bg-gray-50 dark:bg-gray-800/50">
                        <ul class="space-y-4 mb-8 flex-1">
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                    Límite de ventas:
                                    <strong>{{ $plan->monthly_sales_limit ? '$' . number_format($plan->monthly_sales_limit, 0) : 'Ilimitado' }}</strong>
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                    Límite de pedidos: <strong>{{ $plan->monthly_orders_limit ?? 'Ilimitados' }}</strong>
                                </span>
                            </li>

                            @if($plan->features)
                                @if(isset($plan->features['premium_badge']) && $plan->features['premium_badge'])
                                    <li class="flex items-start">
                                        <svg class="flex-shrink-0 h-5 w-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Insignia Premium</span>
                                    </li>
                                @endif
                                @if(isset($plan->features['priority_listing']) && $plan->features['priority_listing'])
                                    <li class="flex items-start">
                                        <svg class="flex-shrink-0 h-5 w-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Prioridad en Listados</span>
                                    </li>
                                @endif
                            @endif
                        </ul>

                        @if($activePlan && $activePlan->id === $plan->id)
                            <button disabled
                                class="mt-auto w-full bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg cursor-not-allowed">
                                Plan Actual
                            </button>
                        @else
                            <a href="{{ route('cook.subscription.checkout', $plan) }}"
                                class="mt-auto w-full text-center bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-4 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                                Seleccionar Plan
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection