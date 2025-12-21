@extends('layouts.app')

@section('title', 'Ganancias')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6 bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
            Mis Ganancias
        </h1>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Desde</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hasta</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 transition">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-xl">
                <p class="text-sm opacity-90 mb-2">Total Ganancias</p>
                <p class="text-4xl font-bold">${{ number_format($totalEarnings, 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-6 text-white shadow-xl">
                <p class="text-sm opacity-90 mb-2">Total Entregas</p>
                <p class="text-4xl font-bold">{{ $totalDeliveries }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
                <p class="text-sm opacity-90 mb-2">Promedio por Entrega</p>
                <p class="text-4xl font-bold">${{ number_format($averagePerDelivery, 0) }}</p>
            </div>
        </div>

        <!-- Deliveries Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4">Historial de Entregas</h3>
            </div>

            @if($deliveries->isEmpty())
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay entregas en este período</h3>
                    <p class="text-gray-600">Ajusta los filtros para ver más resultados</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Fecha</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Pedido</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Ruta</th>
                                <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">Ganancia</th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($deliveries as $delivery)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold">{{ $delivery->delivered_at->format('d/m/Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ $delivery->delivered_at->format('H:i') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('delivery-driver.orders.show', $delivery->id) }}"
                                            class="font-semibold text-blue-600 hover:text-blue-800">
                                            #{{ $delivery->order_id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm">
                                            <span class="font-semibold">{{ $delivery->order->cook->user->name }}</span>
                                            →
                                            <span class="font-semibold">{{ $delivery->order->customer->name }}</span>
                                        </p>
                                        <p class="text-xs text-gray-600">{{ Str::limit($delivery->order->delivery_address, 40) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-lg font-bold text-green-600">${{ number_format($delivery->delivery_fee, 0) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            ✅ Entregado
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold">Total:</td>
                                <td class="px-6 py-4 text-right">
                                    <p class="text-2xl font-bold text-green-600">${{ number_format($totalEarnings, 0) }}</p>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection