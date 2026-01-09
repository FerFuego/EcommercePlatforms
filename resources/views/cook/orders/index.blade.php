@extends('layouts.app')

@section('title', 'Pedidos - Cocinero')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    <span
                        class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Pedidos Recibidos
                    </span>
                </h1>
                <p class="text-gray-600">Gestiona los pedidos de tus clientes</p>
            </div>

            <!-- Filter Tabs -->
            <div class="flex space-x-2 mb-6 overflow-x-auto pb-2">
                <a href="{{ route('cook.orders.index') }}"
                    class="px-6 py-3 rounded-xl font-semibold transition {{ !request('status') ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    Todos
                </a>
                <a href="{{ route('cook.orders.index', ['status' => 'awaiting_cook_acceptance']) }}"
                    class="px-6 py-3 rounded-xl font-semibold transition {{ request('status') == 'awaiting_cook_acceptance' ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    Pendientes
                </a>
                <a href="{{ route('cook.orders.index', ['status' => 'preparing']) }}"
                    class="px-6 py-3 rounded-xl font-semibold transition {{ request('status') == 'preparing' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    En Preparación
                </a>
                <a href="{{ route('cook.orders.index', ['status' => 'scheduled']) }}"
                    class="px-6 py-3 rounded-xl font-semibold transition {{ request('status') == 'scheduled' ? 'bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    Programados
                </a>
                <a href="{{ route('cook.orders.index', ['status' => 'delivered']) }}"
                    class="px-6 py-3 rounded-xl font-semibold transition {{ request('status') == 'delivered' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    Completados
                </a>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden">
                    @if(auth()->user()->is_suspended)
                        <div
                            class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
                            <span
                                class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
                        </div>
                    @endif
                    <h3 class="text-xl font-bold mb-4">
                        Acciones Rápidas
                    </h3>
                    <div class="space-y-3 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none' : '' }}">
                        <a href="{{ route('cook.dashboard') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Dashboard
                        </a>
                        <a href="{{ route('cook.orders.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Ver Pedidos
                        </a>
                        <a href="{{ route('cook.dishes.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Mis Platos
                        </a>
                        <a href="{{ route('cook.dishes.create') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Nuevo Plato
                        </a>
                        <a href="{{ route('cook.profile.edit') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Configuración
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                @if($orders->count() > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($orders as $order)
                            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all"
                                data-order-id="{{ $order->id }}">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-xl font-bold text-gray-800">#{{ $order->id }}</h3>
                                            @if($order->status == 'scheduled' && $order->scheduled_time)
                                                <span
                                                    class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold animate-pulse">
                                                    📅 PROGRAMADO: {{ $order->scheduled_time->format('d/m H:i') }}
                                                </span>
                                            @else
                                                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                                                                                {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' :
                                                    ($order->status == 'rejected_by_cook' ? 'bg-red-100 text-red-800' :
                                                        ($order->status == 'awaiting_cook_acceptance' ? 'bg-yellow-100 text-yellow-800' :
                                                            ($order->status == 'scheduled' ? 'bg-purple-100 text-purple-800' :
                                                                'bg-blue-100 text-blue-800'))) }}"
                                                                        data-order-status-label="{{ $order->id }}">
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
                                                    'scheduled' => '📅 Programado',
                                                    default => $order->status
                                                } }}
                                                                    </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 text-gray-600 mb-1">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                {{ substr($order->customer->name, 0, 1) }}
                                            </div>
                                            <span class="font-semibold">{{ $order->customer->name }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-pink-600">${{ number_format($order->total_amount, 0) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $order->delivery_type == 'delivery' ? '🛵 Delivery' : '🏃 Retiro' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Items -->
                                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                                    @foreach($order->items as $item)
                                        <div class="py-2 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-bold">{{ $item->quantity }}x {{ $item->dish->name }}</span>
                                                <span class="text-sm font-semibold">${{ number_format($item->total_price, 0) }}</span>
                                            </div>
                                            @if($item->options->count() > 0)
                                                <div class="mt-1 ml-4 space-y-1">
                                                    @foreach($item->options as $option)
                                                        <div class="text-[10px] text-gray-500 flex items-center">
                                                            <span class="mr-1 text-purple-400">•</span>
                                                            {{ $option->dishOption->name }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if($order->notes)
                                    <div class="bg-blue-50 rounded-xl p-3 mb-4">
                                        <p class="text-xs font-semibold text-blue-800 mb-1">📝 Notas del cliente:</p>
                                        <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                                    </div>
                                @endif

                                {{-- Delivery Driver Info (if assigned) --}}
                                @if($order->delivery_type === 'delivery' && $order->deliveryAssignment)
                                            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-3 mb-4 border border-blue-200">
                                                <p class="text-xs font-semibold text-blue-800 mb-2">🚴 Repartidor Asignado:</p>
                                                @php
                                                    $driver = $order->deliveryAssignment->deliveryUser->deliveryDriver;
                                                @endphp
                                                <div class="flex items-center space-x-2">
                                                    @if($driver && $driver->profile_photo)
                                                        <img src="{{ asset('uploads/' . $driver->profile_photo) }}"
                                                            alt="{{ $order->deliveryAssignment->deliveryUser->name }}"
                                                            class="w-8 h-8 object-cover rounded-full">
                                                    @else
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                            {{ substr($order->deliveryAssignment->deliveryUser->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div class="flex-1">
                                                        <p class="text-sm font-bold text-gray-800">
                                                            {{ $order->deliveryAssignment->deliveryUser->name }}
                                                        </p>
                                                        @if($driver)
                                                            <p class="text-xs text-gray-600">
                                                                @if($driver->vehicle_type === 'bicycle')
                                                                    🚲 Bicicleta
                                                                @elseif($driver->vehicle_type === 'motorcycle')
                                                                    🏍️ Moto
                                                                @else
                                                                    🚗 Auto
                                                                @endif
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="text-xs px-2 py-1 rounded-full font-semibold
                                                                                                                                                                                                                                                                                                                {{ $order->deliveryAssignment->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ match ($order->deliveryAssignment->status) {
                                        'assigned' => 'Asignado',
                                        'picked_up' => 'Recogido',
                                        'on_the_way' => 'En Camino',
                                        'delivered' => 'Entregado',
                                        default => $order->deliveryAssignment->status
                                    } }}
                                                    </span>
                                                </div>
                                            </div>
                                @elseif($order->delivery_type === 'delivery' && in_array($order->status, ['assigned_to_delivery', 'on_the_way']))
                                    <div class="bg-yellow-50 rounded-xl p-3 mb-4 border border-yellow-200">
                                        <p class="text-xs font-semibold text-yellow-800">⏳ Esperando repartidor...</p>
                                        <p class="text-xs text-yellow-700 mt-1">El pedido está listo para ser recogido</p>
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="space-y-2">
                                    @if($order->status == 'awaiting_cook_acceptance')
                                        <div class="grid grid-cols-2 gap-2">
                                            <form action="{{ route('cook.orders.accept', $order->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                                    ✓ Aceptar
                                                </button>
                                            </form>
                                            <button type="button" onclick="openRejectModal({{ $order->id }})"
                                                class="w-full bg-gradient-to-r from-red-500 to-pink-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                                ✗ Rechazar
                                            </button>
                                        </div>
                                    @elseif($order->status == 'preparing')
                                        <form action="{{ route('cook.orders.update-status', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="ready_for_pickup">
                                            <button type="submit"
                                                class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                                ✓ Marcar como Listo
                                            </button>
                                        </form>
                                    @elseif($order->status == 'scheduled')
                                        <form action="{{ route('cook.orders.update-status', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="preparing">
                                            <button type="submit"
                                                class="w-full bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                                👨‍🍳 Empezar Preparación
                                            </button>
                                        </form>
                                    @elseif($order->status == 'ready_for_pickup' && $order->delivery_type == 'pickup')
                                        <form action="{{ route('cook.orders.update-status', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="delivered">
                                            <button type="submit"
                                                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                                ✓ Marcar como Entregado
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('orders.show', $order->id) }}"
                                        class="block w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold text-center hover:bg-gray-200 transition-all">
                                        Ver Detalle
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <div class="text-8xl mb-4">🍽️</div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">No hay pedidos
                            {{ request('status') ? 'en esta categoría' : 'todavía' }}
                        </h2>
                        <p class="text-gray-600">Los pedidos aparecerán aquí cuando los clientes compren tus platos</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50"
        onclick="closeRejectModal(event)">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4" onclick="event.stopPropagation()">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Rechazar Pedido</h3>
            <p class="text-gray-600 mb-6">Por favor, indica el motivo del rechazo. Esta información será visible para el
                cliente.</p>

            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Motivo del rechazo <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required maxlength="500"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring focus:ring-red-200 transition"
                        placeholder="Ej: No tengo ingredientes disponibles, No puedo cumplir con el horario solicitado, etc."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                        Confirmar Rechazo
                    </button>
                    <button type="button" onclick="closeRejectModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                if (window.Echo) {
                    window.Echo.private('cook.{{ auth()->user()->id }}')
                        .listen('OrderStatusUpdated', (e) => {
                            console.log('Order status updated:', e);
                            // Refresh page to show new orders or status changes
                            window.location.reload();
                        });
                }
            });

            function openRejectModal(orderId) {
                document.getElementById('rejectModal').classList.remove('hidden');
                document.getElementById('rejectModal').classList.add('flex');
                document.getElementById('rejectForm').action = `/cook/orders/${orderId}/reject`;
                document.getElementById('rejection_reason').value = '';
            }

            function closeRejectModal(event) {
                if (!event || event.target.id === 'rejectModal') {
                    document.getElementById('rejectModal').classList.add('hidden');
                    document.getElementById('rejectModal').classList.remove('flex');
                }
            }
        </script>
    @endpush
@endsection