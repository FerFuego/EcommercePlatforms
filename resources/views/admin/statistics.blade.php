@extends('layouts.admin')

@section('title', 'Estadísticas')

@section('content')
    <div class="min-h-screen py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        Estadísticas de la Plataforma
                    </h1>
                    <p class="text-gray-600">Métricas y reportes detallados</p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="bg-gray-200 hover:bg-gray-300 px-6 py-3 rounded-xl font-semibold transition">
                    ← Volver al Dashboard
                </a>
            </div>

            <!-- Revenue Chart Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
                <h2
                    class="text-2xl font-bold mb-6 bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    Resumen Financiero
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl">
                        <p class="text-gray-600 text-sm mb-2">Ventas Totales</p>
                        <p class="text-3xl font-bold text-green-600">
                            ${{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl">
                        <p class="text-gray-600 text-sm mb-2">Comisiones</p>
                        <p class="text-3xl font-bold text-purple-600">
                            ${{ number_format($stats['total_commission'], 0, ',', '.') }}</p>
                        <div class="flex flex-col mt-2">
                             <span class="text-xs text-gray-500">{{ $stats['commission_percentage'] }}% Efectivo (Histórico)</span>
                             <span class="text-xs font-semibold text-purple-700">Tasa Actual: {{ $current_commission_rate }}%</span>
                        </div>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl">
                        <p class="text-gray-600 text-sm mb-2">Ticket Promedio</p>
                        <p class="text-3xl font-bold text-blue-600">
                            ${{ number_format($stats['average_order'], 0, ',', '.') }}</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-orange-50 to-pink-50 rounded-xl">
                        <p class="text-gray-600 text-sm mb-2">Ganancia por Pedido</p>
                        <p class="text-3xl font-bold text-orange-600">
                            ${{ number_format($stats['average_commission'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Users Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Cooks Stats -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <span
                            class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Cocineros</span>
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-green-50 rounded-xl">
                            <span class="text-gray-700">Aprobados y Activos</span>
                            <span class="font-bold text-lg text-green-600">{{ $stats['active_cooks'] }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-yellow-50 rounded-xl">
                            <span class="text-gray-700">Pendientes de Aprobación</span>
                            <span class="font-bold text-lg text-yellow-600">{{ $stats['pending_cooks'] }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                            <span class="text-gray-700">Inactivos</span>
                            <span class="font-bold text-lg text-gray-600">{{ $stats['inactive_cooks'] }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-purple-50 rounded-xl">
                            <span class="text-gray-700">Total</span>
                            <span class="font-bold text-lg text-purple-600">{{ $stats['total_cooks'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Orders Stats -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <span
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Pedidos</span>
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-green-50 rounded-xl">
                            <span class="text-gray-700">Entregados</span>
                            <span class="font-bold text-lg text-green-600">{{ $stats['delivered_orders'] }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-blue-50 rounded-xl">
                            <span class="text-gray-700">En Proceso</span>
                            <span class="font-bold text-lg text-blue-600">{{ $stats['pending_orders'] }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-red-50 rounded-xl">
                            <span class="text-gray-700">Cancelados/Rechazados</span>
                            <span class="font-bold text-lg text-red-600">{{ $stats['cancelled_orders'] }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-purple-50 rounded-xl">
                            <span class="text-gray-700">Total</span>
                            <span class="font-bold text-lg text-purple-600">{{ $stats['total_orders'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dishes Stats -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">
                    <span class="bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">Platos en la
                        Plataforma</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex justify-between items-center p-4 bg-green-50 rounded-xl">
                        <span class="text-gray-700">Activos con Stock</span>
                        <span class="font-bold text-lg text-green-600">{{ $stats['available_dishes'] }}</span>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-xl">
                        <span class="text-gray-700">Activos Total</span>
                        <span class="font-bold text-lg text-blue-600">{{ $stats['active_dishes'] }}</span>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                        <span class="text-gray-700">Inactivos</span>
                        <span class="font-bold text-lg text-gray-600">{{ $stats['inactive_dishes'] }}</span>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-purple-50 rounded-xl">
                        <span class="text-gray-700">Total</span>
                        <span class="font-bold text-lg text-purple-600">{{ $stats['total_dishes'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Top Cooks -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4">
                        <span class="bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">🏆 Top
                            Cocineros</span>
                    </h3>

                    <div class="space-y-3">
                        @forelse($top_cooks as $index => $cook)
                            <div
                                class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    @if($cook->user->profile_photo_path)
                                        <img src="{{ asset('uploads/' . $cook->user->profile_photo_path) }}" alt="{{ $cook->user->name }}" class="w-10 h-10 rounded-full object-cover border border-yellow-200">
                                    @else
                                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-sm font-bold text-yellow-600 border border-yellow-200">
                                            {{ substr($cook->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold">{{ $cook->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $cook->orders_count }} pedidos •
                                            {{ number_format($cook->rating_avg, 1) }} ⭐</p>
                                    </div>
                                </div>
                                <p class="font-bold text-green-600">${{ number_format($cook->total_sales, 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-4">No hay datos disponibles</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Dishes -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4">
                        <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">🍽️ Platos
                            Más Vendidos</span>
                    </h3>

                    <div class="space-y-3">
                        @forelse($top_dishes as $index => $dish)
                            <div
                                class="flex items-center justify-between p-4 bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="w-8 h-8 bg-gradient-to-br from-pink-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="font-semibold">{{ $dish->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $dish->cook->user->name }} •
                                            {{ $dish->orders_count }} ventas</p>
                                    </div>
                                </div>
                                <p class="font-bold text-green-600">
                                    ${{ number_format($dish->price * $dish->orders_count, 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-4">No hay datos disponibles</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection