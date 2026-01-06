@extends('layouts.admin')

@section('title', 'Panel de Administración')

@section('content')
    <div class="min-h-screen py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2">
                    <span
                        class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Panel de Administración
                    </span>
                </h1>
                <p class="text-gray-600">Gestión completa de la plataforma</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Cooks -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Cocineros</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['total_cooks'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $stats['pending_cooks'] }} pendientes</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-100 to-pink-100 p-4 rounded-xl">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Pedidos</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $stats['pending_orders'] }} activos</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 p-4 rounded-xl">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Ingresos</p>
                            <p class="text-3xl font-bold text-green-600">
                                ${{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400 mt-1">Comisión:
                                ${{ number_format($stats['total_commission'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-4 rounded-xl">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Dishes -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Platos</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $stats['total_dishes'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $stats['active_dishes'] }} activos</p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-100 to-pink-100 p-4 rounded-xl">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.247 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('admin.users.index') }}"
                    class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl shadow-lg p-6 text-white hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Gestión de Usuarios</h3>
                            <p class="text-purple-100 text-sm">Administrar plataforma</p>
                            @php
                                $totalPending = $stats['pending_cooks'] + $stats['pending_drivers'];
                            @endphp
                            @if($totalPending > 0)
                                <span
                                    class="inline-block mt-2 bg-white text-purple-600 px-3 py-1 rounded-full text-xs font-bold">{{ $totalPending }}
                                    pendientes</span>
                            @endif
                        </div>
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl shadow-lg p-6 text-white hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Ver Pedidos</h3>
                            <p class="text-blue-100 text-sm">Monitorear actividad</p>
                            @if($stats['pending_orders'] > 0)
                                <span
                                    class="inline-block mt-2 bg-white text-blue-600 px-3 py-1 rounded-full text-xs font-bold">{{ $stats['pending_orders'] }}
                                    activos</span>
                            @endif
                        </div>
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('admin.statistics') }}"
                    class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl shadow-lg p-6 text-white hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Estadísticas</h3>
                            <p class="text-green-100 text-sm">Reportes y métricas</p>
                        </div>
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">
                    Actividad Reciente
                </h2>

                <div class="space-y-4">
                    @forelse($recent_orders as $order)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                                                #{{ $order->id }}
                                            </div>
                                            <div>
                                                <p class="font-semibold">Pedido de {{ $order->customer->name }}</p>
                                                <p class="text-sm text-gray-500">Cocinero: {{ $order->cook->user->name }} •
                                                    ${{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                                @if($order->notes)
                                                    <div class="mt-1 flex items-start space-x-1">
                                                        <span
                                                            class="text-[10px] font-bold text-blue-600 uppercase mt-0.5 whitespace-nowrap">Nota:</span>
                                                        <p class="text-[11px] text-gray-600 italic line-clamp-1">"{{ $order->notes }}"</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold  {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' :
                            ($order->status == 'rejected_by_cook' ? 'bg-red-100 text-red-800' :
                                ($order->status == 'awaiting_cook_acceptance' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-blue-100 text-blue-800')) }}">
                                                {{ match ($order->status) {
                            'pending_payment' => '⏳ Pendiente de Pago',
                            'paid' => '✓ Pagado',
                            'awaiting_cook_acceptance' => '⏰ Esperando Confirmación',
                            'rejected_by_cook' => '❌ Rechazado',
                            'preparing' => '👨‍🍳 En Preparación',
                            'ready_for_pickup' => '✅ Listo para Retiro',
                            'assigned_to_delivery' => '🛵 En Camino',
                            'on_the_way' => '🚗 En Camino',
                            'delivered' => '✓ Entregado',
                            'cancelled' => '❌ Cancelado',
                            default => $order->status
                        } }}
                                            </span>
                                            <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                    @empty
                        <p class="text-center text-gray-400 py-8">No hay actividad reciente</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection