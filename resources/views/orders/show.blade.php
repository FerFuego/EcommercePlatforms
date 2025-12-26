@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . $order->id)

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div data-order-id="{{ $order->id }}">
                    <h1 class="text-3xl font-bold mb-2">
                        <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            Pedido #{{ $order->id }}
                        </span>
                    </h1>
                    <p class="text-gray-600">Realizado el {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="px-6 py-3 rounded-full text-lg font-bold shadow-sm
                        @if($order->status === 'delivered') bg-green-100 text-green-700
                                    @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                                    @elseif($order->status === 'awaiting_cook_acceptance') bg-yellow-100 text-yellow-700
                                    @elseif($order->status === 'ready_for_pickup') bg-purple-100 text-purple-700
                                    @elseif($order->status === 'on_the_way') bg-indigo-100 text-indigo-700
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                    @elseif($order->status === 'rejected_by_cook') bg-orange-100 text-orange-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
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
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Review Section -->
                    @if($order->status === 'delivered')
                        @if(!$order->review)
                            <div
                                class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl shadow-lg p-6 border border-purple-100">
                                <h2 class="text-xl font-bold mb-4 flex items-center text-purple-800">
                                    <span class="mr-2">⭐</span> Califica tu Pedido
                                </h2>
                                <form action="{{ route('orders.review.store', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Puntuación</label>
                                        <div class="flex items-center space-x-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" onclick="setRating({{ $i }})"
                                                    class="star-btn text-4xl text-gray-300 hover:scale-110 transition-all focus:outline-none">
                                                    ★
                                                </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="rating_value" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Comentario (Opcional)</label>
                                        <textarea name="comment" rows="3"
                                            class="w-full px-4 py-2 rounded-xl border-gray-200 focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                            placeholder="¿Qué te pareció la comida?"></textarea>
                                    </div>
                                    <button type="submit"
                                        class="bg-purple-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-purple-700 transition shadow-md">
                                        Enviar Calificación
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-400">
                                <h2 class="text-xl font-bold mb-4 flex items-center">
                                    <span class="mr-2">✨</span> Tu Calificación
                                </h2>
                                <div class="flex items-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span
                                            class="text-2xl {{ $i <= $order->review->rating ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                    @endfor
                                    <span class="ml-2 font-bold text-gray-700">{{ $order->review->rating }}/5</span>
                                </div>
                                @if($order->review->comment)
                                    <p class="text-gray-600 italic">"{{ $order->review->comment }}"</p>
                                @endif
                            </div>
                        @endif

                        <div class="mt-6 flex justify-end">
                            <form action="{{ route('orders.reorder', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-gradient-to-r from-green-500 to-teal-600 text-white px-8 py-3 rounded-xl font-bold hover:shadow-xl transition shadow-md flex items-center">
                                    <span class="mr-2">🛒</span> Volver a Pedir
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Rejection Reason (if rejected) -->
                    @if($order->status === 'rejected_by_cook' && $order->rejection_reason)
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-2xl shadow-lg p-6">
                            <h2 class="text-xl font-bold mb-3 flex items-center text-red-800">
                                <span class="mr-2">❌</span> Pedido Rechazado
                            </h2>
                            <div class="bg-white rounded-xl p-4">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Motivo del rechazo:</p>
                                <p class="text-gray-800 italic">"{{ $order->rejection_reason }}"</p>
                            </div>
                            <p class="text-xs text-gray-600 mt-3">
                                El cocinero no pudo aceptar este pedido. Si pagaste, se procesará el reembolso automáticamente.
                            </p>
                        </div>
                    @endif

                    {{-- Delivery Driver Info (if assigned) --}}
                    @if($order->delivery_type === 'delivery' && $order->deliveryAssignment)
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-2xl shadow-lg p-6">
                            <h2 class="text-xl font-bold mb-4 flex items-center text-blue-800">
                                <span class="mr-2">🚴</span> Información del Repartidor
                            </h2>
                            <div class="bg-white rounded-xl p-4">
                                @php
                                    $driver = $order->deliveryAssignment->deliveryUser->deliveryDriver;
                                @endphp
                                <div class="flex items-center space-x-4 mb-4">
                                    @if($driver && $driver->profile_photo)
                                        <img src="{{ Storage::url($driver->profile_photo) }}" alt="{{ $order->deliveryAssignment->deliveryUser->name }}"
                                            class="w-16 h-16 object-cover rounded-full">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                            {{ substr($order->deliveryAssignment->deliveryUser->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="font-bold text-lg">{{ $order->deliveryAssignment->deliveryUser->name }}</p>
                                        @if($driver)
                                            <p class="text-sm text-gray-600">
                                                @if($driver->vehicle_type === 'bicycle')
                                                    🚲 Bicicleta
                                                @elseif($driver->vehicle_type === 'motorcycle')
                                                    🏍️ Moto
                                                @else
                                                    🚗 Auto
                                                @endif
                                                @if($driver->vehicle_plate)
                                                    - {{ $driver->vehicle_plate }}
                                                @endif
                                            </p>
                                            @if($driver->rating_avg > 0)
                                                <div class="flex items-center space-x-1 mt-1">
                                                    <span class="text-yellow-500">⭐</span>
                                                    <span class="text-sm font-semibold">{{ number_format($driver->rating_avg, 1) }}</span>
                                                    <span class="text-xs text-gray-600">({{ $driver->rating_count }} reviews)</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $order->deliveryAssignment->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                               ($order->deliveryAssignment->status === 'on_the_way' ? 'bg-blue-100 text-blue-800' : 
                                               ($order->deliveryAssignment->status === 'picked_up' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ match($order->deliveryAssignment->status) {
                                                'assigned' => '📋 Asignado',
                                                'picked_up' => '📦 Recogido',
                                                'on_the_way' => '🚴 En Camino',
                                                'delayed' => '⏰ Demorado',
                                                'delivered' => '✅ Entregado',
                                                default => $order->deliveryAssignment->status
                                            } }}
                                        </span>
                                    </div>
                                </div>
                                @if(in_array($order->deliveryAssignment->status, ['on_the_way', 'delayed']))
                                    <div class="bg-blue-50 rounded-lg p-3 text-sm">
                                        <p class="text-blue-800 font-semibold">📍 Tu pedido está en camino</p>
                                        <p class="text-blue-700 text-xs mt-1">El repartidor está llevando tu pedido a la dirección de entrega</p>
                                    </div>
                                @elseif($order->deliveryAssignment->status === 'picked_up')
                                    <div class="bg-purple-50 rounded-lg p-3 text-sm">
                                        <p class="text-purple-800 font-semibold">📦 Pedido recogido</p>
                                        <p class="text-purple-700 text-xs mt-1">El repartidor ha recogido tu pedido y pronto estará en camino</p>
                                    </div>
                                @elseif($order->deliveryAssignment->status === 'delivered')
                                    <div class="bg-green-50 rounded-lg p-3 text-sm">
                                        <p class="text-green-800 font-semibold">✅ Pedido entregado</p>
                                        <p class="text-green-700 text-xs mt-1">Entregado el {{ $order->deliveryAssignment->delivered_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Items -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-xl font-bold mb-6">Items del Pedido</h2>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center space-x-4">
                                        @if($item->dish->photo_url)
                                            <img src="{{ Storage::url($item->dish->photo_url) }}" alt="{{ $item->dish->name }}"
                                                class="w-20 h-20 object-cover rounded-xl shadow-sm">
                                        @else
                                            <div
                                                class="w-20 h-20 bg-gradient-to-br from-orange-300 to-pink-400 rounded-xl flex items-center justify-center text-3xl shadow-sm">
                                                🍲
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-lg text-gray-800">{{ $item->dish->name }}</p>
                                            <p class="text-gray-600">Cantidad: {{ $item->quantity }}</p>
                                            <p class="text-sm text-gray-500">${{ number_format($item->unit_price, 0) }} c/u</p>
                                        </div>
                                    </div>
                                    <span
                                        class="font-bold text-lg text-purple-600">${{ number_format($item->total_price, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Payment & Delivery Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <span
                                    class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-2">💳</span>
                                Pago
                            </h3>
                            <p class="text-gray-700 font-medium">
                                {{ match ($order->payment_method) {
        'mercadopago' => 'MercadoPago',
        'cash' => 'Efectivo',
        'transfer' => 'Transferencia Bancaria',
        default => $order->payment_method
    } }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Total: ${{ number_format($order->total_amount, 0) }}
                            </p>
                        </div>

                        <!-- Delivery -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <span
                                    class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2">
                                    {{ $order->delivery_type == 'delivery' ? '🛵' : '🏃' }}
                                </span>
                                {{ $order->delivery_type == 'delivery' ? 'Delivery' : 'Retiro' }}
                            </h3>
                            @if($order->delivery_type == 'delivery')
                                <p class="text-gray-700">{{ $order->delivery_address }}</p>
                            @else
                                <p class="text-gray-700">Retiro en cocina</p>
                            @endif

                            @if($order->notes)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs font-bold text-gray-500 uppercase">Notas:</p>
                                    <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Cook Info -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Cocinero</h3>
                        <div class="flex items-center space-x-4 mb-4">
                            @if($order->cook->user->profile_photo_path)
                                <img src="{{ asset('storage/' . $order->cook->user->profile_photo_path) }}"
                                    alt="{{ $order->cook->user->name }}"
                                    class="w-16 h-16 rounded-full object-cover border-2 border-purple-100 shadow-md">
                            @else
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-orange-400 to-pink-600 rounded-full flex items-center justify-center text-3xl shadow-md">
                                    👨‍🍳
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $order->cook->user->name }}</h4>
                                <div class="flex items-center text-sm text-yellow-500">
                                    <span>⭐ {{ number_format($order->cook->rating_avg, 1) }}</span>
                                    <span class="text-gray-400 ml-1">({{ $order->cook->rating_count }})</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm text-gray-600 border-t border-gray-100 pt-4">
                            <p class="flex items-center">
                                <span class="mr-2">📍</span> {{ $order->cook->user->address }}
                            </p>
                            @if($order->cook->user->phone)
                                <p class="flex items-center">
                                    <span class="mr-2">📞</span> {{ $order->cook->user->phone }}
                                </p>
                            @endif
                        </div>

                        <a href="{{ route('marketplace.cook.profile', $order->cook->id) }}"
                            class="block w-full mt-4 text-center text-purple-600 font-semibold hover:text-pink-600 transition">
                            Ver Perfil Completo
                        </a>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                        <h3 class="text-lg font-bold mb-4">Resumen de Costos</h3>
                        <div class="space-y-2 text-gray-600">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>${{ number_format($order->subtotal, 0) }}</span>
                            </div>
                            @if($order->delivery_fee > 0)
                                <div class="flex justify-between">
                                    <span>Envío</span>
                                    <span>${{ number_format($order->delivery_fee, 0) }}</span>
                                </div>
                            @endif
                            <div
                                class="flex justify-between font-bold text-xl text-gray-800 border-t border-gray-200 pt-2 mt-2">
                                <span>Total</span>
                                <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                    ${{ number_format($order->total_amount, 0) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('orders.my') }}"
                        class="block w-full bg-gray-800 text-white text-center py-3 rounded-xl font-bold hover:bg-gray-900 transition shadow-lg">
                        ← Volver a Mis Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    function setRating(rating) {
        document.getElementById('rating_value').value = rating;
        const stars = document.querySelectorAll('.star-btn');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }
</script>
@endpush
@endsection