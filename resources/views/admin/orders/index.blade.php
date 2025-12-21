@extends('layouts.admin')

@section('title', 'Gestión de Pedidos')

@section('content')
    <div class="min-h-screen py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        Gestión de Pedidos
                    </h1>
                    <p class="text-gray-600">Monitorear todos los pedidos de la plataforma</p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="bg-gray-200 hover:bg-gray-300 px-6 py-3 rounded-xl font-semibold transition">
                    ← Volver al Dashboard
                </a>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white rounded-2xl shadow-lg p-2 mb-6 flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.index') }}"
                    class="px-4 py-2 rounded-xl font-semibold {{ !request('status') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">
                    Todos
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'awaiting_cook_acceptance']) }}"
                    class="px-4 py-2 rounded-xl font-semibold {{ request('status') === 'awaiting_cook_acceptance' ? 'bg-yellow-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">
                    Pendientes
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'preparing']) }}"
                    class="px-4 py-2 rounded-xl font-semibold {{ request('status') === 'preparing' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">
                    En Preparación
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}"
                    class="px-4 py-2 rounded-xl font-semibold {{ request('status') === 'delivered' ? 'bg-green-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">
                    Entregados
                </a>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                @forelse($orders as $order)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                        <div class="p-6">
                            <div class="flex flex-wrap items-center justify-between mb-4">
                                <!-- Order ID & Status -->
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        #{{ $order->id }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg">Pedido #{{ $order->id }}</h3>
                                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="px-4 py-2 rounded-full text-sm font-semibold
                                                                        @if($order->status === 'delivered') bg-green-100 text-green-700
                                                                        @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                                                                        @elseif($order->status === 'awaiting_cook_acceptance') bg-yellow-100 text-yellow-700
                                                                        @elseif($order->status === 'ready_for_pickup') bg-purple-100 text-purple-700
                                                                        @elseif($order->status === 'on_the_way') bg-indigo-100 text-indigo-700
                                                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                                                        @elseif($order->status === 'rejected_by_cook') bg-orange-100 text-orange-700
                                                                        @else bg-gray-100 text-gray-700
                                                                        @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>

                            <!-- Order Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <!-- Customer -->
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Cliente</p>
                                    <p class="font-semibold">{{ $order->customer->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->customer->email }}</p>
                                </div>

                                <!-- Cook -->
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Cocinero</p>
                                    <p class="font-semibold">{{ $order->cook->user->name }}</p>
                                    <p class="text-sm text-gray-600">Rating: {{ number_format($order->cook->rating_avg, 1) }} ⭐
                                    </p>
                                </div>

                                <!-- Payment -->
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Pago</p>
                                    <p class="font-semibold text-green-600">
                                        ${{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-600">{{ ucfirst($order->payment_method) }}</p>
                                </div>
                            </div>

                            <!-- Items -->
                            <div class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Items del Pedido:</p>
                                <div class="space-y-2">
                                    @foreach($order->items as $item)
                                        <div class="flex justify-between items-center bg-gray-50 rounded-lg p-3">
                                            <div>
                                                <p class="font-medium">{{ $item->dish->name }}</p>
                                                <p class="text-sm text-gray-500">Cantidad: {{ $item->quantity }} x
                                                    ${{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                            </div>
                                            <p class="font-bold">${{ number_format($item->total_price, 0, ',', '.') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div class="flex flex-wrap items-center justify-between pt-4 border-t">
                                <div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $order->delivery_type === 'delivery' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $order->delivery_type === 'delivery' ? '🚚 Delivery' : '🏪 Retiro' }}
                                    </span>
                                    @if($order->delivery_address)
                                        <span class="text-sm text-gray-600 ml-2">{{ $order->delivery_address }}</span>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Comisión Plataforma</p>
                                    <p class="font-semibold text-purple-600">
                                        ${{ number_format($order->commission_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <div class="text-6xl mb-4">📦</div>
                        <p class="text-xl text-gray-500">No hay pedidos para mostrar</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection