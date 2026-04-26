@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="{{ route('cook.dashboard') }}"
                class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-primary mb-3 transition-colors group">
                <svg class="h-4 w-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Inicio
            </a>
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <h1 class="text-4xl font-bold">
                    <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Suscripción y Límites
                    </span>
                </h1>
                <a href="{{ route('cook.subscription.history') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md text-gray-700 dark:text-gray-200 rounded-xl transition-all text-sm font-bold">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ver Historial de Pagos
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-green-700 bg-green-100/50 border border-green-200 rounded-2xl dark:bg-green-900/20 dark:text-green-300 dark:border-green-800/30"
                role="alert">
                <span class="font-bold">¡Éxito!</span> {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 mb-6 text-sm text-blue-700 bg-blue-100/50 border border-blue-200 rounded-2xl dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800/30"
                role="alert">
                <span class="font-bold">Información:</span> {{ session('info') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Current Plan Overview -->
            <div
                class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border-l-4 border-purple-600 transform transition hover:scale-[1.02]">
                <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4">Plan Actual</h2>
                @if($activePlan)
                    <div class="mb-4">
                        <p class="text-4xl font-black text-gray-900 dark:text-white leading-none mb-1">{{ $activePlan->name }}
                        </p>
                        <p class="text-purple-600 dark:text-purple-400 font-bold text-xl">
                            ${{ number_format($activePlan->price, 2) }} <span class="text-sm font-medium text-gray-500">/
                                {{ $activePlan->billing_period == 'monthly' ? 'mes' : 'año' }}</span></p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <span
                            class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold border {{ $cook->is_selling_blocked ? 'bg-red-50 text-red-700 border-red-100' : 'bg-green-50 text-green-700 border-green-100' }}">
                            <span
                                class="w-2 h-2 rounded-full {{ $cook->is_selling_blocked ? 'bg-red-500' : 'bg-green-500' }} mr-2"></span>
                            {{ $cook->is_selling_blocked ? 'VENTAS BLOQUEADAS' : 'CUENTA ACTIVA' }}
                        </span>
                    </div>
                @else
                    <p class="text-lg text-gray-500 font-medium mb-4 italic">No tienes un plan activo.</p>
                @endif
            </div>

            <!-- Metrics Overview -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border-l-4 border-orange-500">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                        <svg class="w-6 h-6 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Uso del Mes Actual
                    </h2>
                    <span
                        class="text-xs font-bold text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full uppercase">
                        Reinicio: {{ $cook->sales_reset_at ? $cook->sales_reset_at->format('d M') : 'Pendiente' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <!-- Sales limit progress -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Ventas
                                    Acumuladas</span>
                                <p class="text-2xl font-black text-gray-900 dark:text-white">
                                    ${{ number_format($cook->monthly_sales_accumulated, 2) }}</p>
                            </div>
                            @if($activePlan && $activePlan->monthly_sales_limit)
                                <span
                                    class="text-xs font-bold text-gray-500 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 px-2 py-1 rounded">Límite:
                                    ${{ number_format($activePlan->monthly_sales_limit, 0) }}</span>
                            @endif
                        </div>

                        @if($activePlan && $activePlan->monthly_sales_limit)
                            @php
                                $salesPercent = min(100, ($cook->monthly_sales_accumulated / $activePlan->monthly_sales_limit) * 100);
                                $salesColor = $salesPercent >= 100 ? 'from-red-500 to-orange-600' : ($salesPercent >= 80 ? 'from-yellow-400 to-orange-500' : 'from-green-400 to-emerald-500');
                            @endphp
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r {{ $salesColor }} h-full rounded-full transition-all duration-1000"
                                    style="width: {{ $salesPercent }}%"></div>
                            </div>
                        @else
                            <div class="pt-2"><span
                                    class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-black uppercase tracking-widest">♾️
                                    Ventas Ilimitadas</span></div>
                        @endif
                    </div>

                    <!-- Orders limit progress -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Pedidos
                                    Recibidos</span>
                                <p class="text-2xl font-black text-gray-900 dark:text-white">
                                    {{ $cook->monthly_orders_accumulated }}</p>
                            </div>
                            @if($activePlan && $activePlan->monthly_orders_limit)
                                <span
                                    class="text-xs font-bold text-gray-500 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 px-2 py-1 rounded">Límite:
                                    {{ $activePlan->monthly_orders_limit }}</span>
                            @endif
                        </div>

                        @if($activePlan && $activePlan->monthly_orders_limit)
                            @php
                                $ordersPercent = min(100, ($cook->monthly_orders_accumulated / $activePlan->monthly_orders_limit) * 100);
                                $ordersColor = $ordersPercent >= 100 ? 'from-red-500 to-orange-600' : ($ordersPercent >= 80 ? 'from-yellow-400 to-orange-500' : 'from-blue-400 to-indigo-500');
                            @endphp
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r {{ $ordersColor }} h-full rounded-full transition-all duration-1000"
                                    style="width: {{ $ordersPercent }}%"></div>
                            </div>
                        @else
                            <div class="pt-2"><span
                                    class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-black uppercase tracking-widest">♾️
                                    Pedidos Ilimitados</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Plans -->
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Planes Disponibles</h2>
            <p class="text-gray-500">Escoge el plan que mejor se adapte al crecimiento de tu negocio.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            @foreach($plans as $plan)
                @php
                    $isCurrent = $activePlan && $activePlan->id === $plan->id;
                @endphp
                <div
                    class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-xl border-2 {{ $isCurrent ? 'border-purple-500 ring-4 ring-purple-500/10' : 'border-transparent' }} hover:border-purple-200 dark:hover:border-gray-700 transition-all duration-500 group overflow-hidden flex flex-col">

                    @if($isCurrent)
                        <div
                            class="absolute top-0 right-0 bg-purple-500 text-white text-[10px] font-black px-4 py-1 rounded-bl-xl uppercase tracking-widest shadow-lg">
                            Tu Plan Actual
                        </div>
                    @endif

                    <div class="p-8 text-center bg-gray-50/50 dark:bg-gray-900/10">
                        <h3
                            class="text-xl font-black text-gray-900 dark:text-white group-hover:text-purple-600 transition-colors uppercase tracking-tight">
                            {{ $plan->name }}</h3>
                        <div class="mt-6 flex items-baseline justify-center">
                            <span
                                class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">${{ number_format($plan->price, 0) }}</span>
                            <span
                                class="ml-1 text-sm font-bold text-gray-400 uppercase tracking-widest">/{{ $plan->billing_period == 'monthly' ? 'mes' : 'año' }}</span>
                        </div>
                        <p class="mt-2 text-xs font-bold text-purple-600/60 uppercase">
                            {{ rtrim(rtrim(number_format($plan->commission_percentage, 2), '0'), '.') }}% de comisión
                        </p>
                    </div>

                    <div class="p-8 flex-1 flex flex-col">
                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-center text-sm">
                                <div class="bg-green-100 dark:bg-green-900/30 p-1 rounded-full mr-3">
                                    <svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                    </svg>
                                </div>
                                <span class="text-gray-600 dark:text-gray-400 font-medium tracking-tight">Ventas: <strong
                                        class="text-gray-900 dark:text-white">{{ $plan->monthly_sales_limit ? '$' . number_format($plan->monthly_sales_limit, 0) : 'Ilimitadas' }}</strong></span>
                            </li>
                            <li class="flex items-center text-sm">
                                <div class="bg-green-100 dark:bg-green-900/30 p-1 rounded-full mr-3">
                                    <svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                    </svg>
                                </div>
                                <span class="text-gray-600 dark:text-gray-400 font-medium tracking-tight">Pedidos: <strong
                                        class="text-gray-900 dark:text-white">{{ $plan->monthly_orders_limit ?? 'Ilimitados' }}</strong></span>
                            </li>

                            @if($plan->features)
                                @if(isset($plan->features['premium_badge']) && $plan->features['premium_badge'])
                                    <li class="flex items-center text-sm">
                                        <div class="bg-purple-100 dark:bg-purple-900/30 p-1 rounded-full mr-3">
                                            <svg class="h-3 w-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <span class="text-gray-600 dark:text-gray-400 font-medium tracking-tight">Insignia
                                            Premium</span>
                                    </li>
                                @endif
                                @if(isset($plan->features['priority_listing']) && $plan->features['priority_listing'])
                                    <li class="flex items-center text-sm">
                                        <div class="bg-orange-100 dark:bg-orange-900/30 p-1 rounded-full mr-3">
                                            <svg class="h-3 w-3 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                        </div>
                                        <span class="text-gray-600 dark:text-gray-400 font-medium tracking-tight">Prioridad en
                                            Búsquedas</span>
                                    </li>
                                @endif
                            @endif
                        </ul>

                        @if($isCurrent)
                            <button disabled
                                class="mt-auto w-full bg-gray-100 dark:bg-gray-700 text-gray-400 font-black py-4 px-4 rounded-2xl cursor-not-allowed uppercase text-xs tracking-widest border border-gray-200 dark:border-gray-600">
                                Plan Activo
                            </button>
                        @else
                            <a href="{{ route('cook.subscription.checkout', $plan) }}"
                                class="mt-auto w-full text-center bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 text-white px-4 py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl hover:shadow-purple-500/25 hover:-translate-y-1 transform transition-all duration-300">
                                Seleccionar
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection