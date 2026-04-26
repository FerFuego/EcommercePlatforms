@extends('layouts.app')

@section('title', 'Estadísticas Avanzadas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Estadísticas y Rendimiento</h1>
            <p class="text-gray-600 mt-2">Analiza tus ventas y descubre qué platos son los favoritos.</p>
        </div>
        @if($isPremium)
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-yellow-900 px-4 py-2 rounded-xl font-bold shadow-md flex items-center">
                <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
                Acceso Premium
            </div>
        @endif
    </div>

    @if(!$isPremium)
        <!-- PAYWALL OVERLAY -->
        <div class="relative">
            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-white/60 backdrop-blur-md rounded-3xl border-2 border-yellow-200">
                <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-md text-center transform hover:scale-105 transition-all">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Desbloquea tus Estadísticas</h2>
                    <p class="text-gray-600 mb-6">Accede a datos reales de ventas, mapas de calor y descubre cuáles de tus platos generan más ingresos.</p>
                    <a href="{{ route('cook.subscription.index') }}" class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all">
                        Mejorar a Premium
                    </a>
                </div>
            </div>
    @endif

            <!-- CONTENT (Blurred if not premium) -->
            <div class="{{ !$isPremium ? 'opacity-40 filter blur-sm pointer-events-none' : '' }}">
                
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                        <div class="w-14 h-14 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-2xl mr-4">
                            📦
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium uppercase">Órdenes Completadas</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                        <div class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center text-2xl mr-4">
                            💵
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium uppercase">Ventas Brutas</p>
                            <h3 class="text-3xl font-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                        <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mr-4">
                            💰
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium uppercase">Ganancia Neta (Est.)</p>
                            <h3 class="text-3xl font-bold text-gray-800">${{ number_format($netEarnings, 2) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Top Dishes Chart -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Platos Más Vendidos</h3>
                        @if(count($topDishes) > 0 || !$isPremium)
                            <div class="relative h-64">
                                <canvas id="topDishesChart"></canvas>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                                <span class="text-4xl mb-2">🍽️</span>
                                <p>Aún no tienes ventas suficientes</p>
                            </div>
                        @endif
                    </div>

                    <!-- Heatmap Chart -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Días de Mayor Demanda</h3>
                        @if(array_sum($heatmapData) > 0 || !$isPremium)
                            <div class="relative h-64">
                                <canvas id="heatmapChart"></canvas>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                                <span class="text-4xl mb-2">📅</span>
                                <p>Aún no tienes ventas suficientes</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Daily Sales Chart -->
                <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Ventas (Platos) por Día - Últimos 30 Días</h3>
                    @if(array_sum($dailySalesData) > 0 || !$isPremium)
                        <div class="relative h-72">
                            <canvas id="dailySalesChart"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                            <span class="text-4xl mb-2">📈</span>
                            <p>Aún no tienes ventas suficientes este mes</p>
                        </div>
                    @endif
                </div>

                <!-- Festive Dates Predictor Table -->
                <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Calendario de Oportunidades 📅</h3>
                    <p class="text-gray-500 mb-6 text-sm">Fechas festivas próximas. El sistema sugerirá cantidades basadas en tu historial de ventas a medida que pase el tiempo.</p>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                                    <th class="py-3 px-4 rounded-tl-xl font-bold">Fecha / Evento</th>
                                    <th class="py-3 px-4 font-bold">Plato Sugerido</th>
                                    <th class="py-3 px-4 rounded-tr-xl font-bold">Proyección / Sugerencia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($festiveDates as $festive)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <p class="font-bold text-gray-800">{{ $festive['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $festive['date'] }}</p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-md font-semibold">
                                            {{ $festive['suggested_dish'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-600 font-medium">
                                        💡 {{ $festive['suggested_qty'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            
    @if(!$isPremium)
        </div> <!-- Close relative container for paywall -->
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // TOP DISHES
        const topDishesCtx = document.getElementById('topDishesChart');
        if (topDishesCtx) {
            const labels = {!! $isPremium && count($topDishes) > 0 ? json_encode($topDishes->pluck('dish.name')) : json_encode(['Pizza', 'Empanadas', 'Milanesa', 'Pasta', 'Asado']) !!};
            const data = {!! $isPremium && count($topDishes) > 0 ? json_encode($topDishes->pluck('total_sold')) : json_encode([45, 30, 25, 15, 5]) !!};

            new Chart(topDishesCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: data,
                        backgroundColor: 'rgba(236, 72, 153, 0.6)', // Pink
                        borderColor: 'rgba(236, 72, 153, 1)',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { display: false } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        // HEATMAP
        const heatmapCtx = document.getElementById('heatmapChart');
        if (heatmapCtx) {
            const labels = {!! json_encode(array_keys($heatmapData)) !!};
            const data = {!! $isPremium && array_sum($heatmapData) > 0 ? json_encode(array_values($heatmapData)) : json_encode([10, 5, 15, 20, 50, 60, 40]) !!};

            new Chart(heatmapCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Órdenes',
                        data: data,
                        backgroundColor: 'rgba(168, 85, 247, 0.2)', // Purple
                        borderColor: 'rgba(168, 85, 247, 1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(168, 85, 247, 1)',
                        pointRadius: 5,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // DAILY SALES
        const dailySalesCtx = document.getElementById('dailySalesChart');
        if (dailySalesCtx) {
            const labels = {!! $isPremium ? json_encode($dailySalesLabels) : json_encode(['01/04','05/04','10/04','15/04','20/04','25/04']) !!};
            const data = {!! $isPremium ? json_encode($dailySalesData) : json_encode([5, 12, 8, 25, 15, 30]) !!};

            new Chart(dailySalesCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Platos Vendidos',
                        data: data,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)', // Blue
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });
</script>
@endpush
