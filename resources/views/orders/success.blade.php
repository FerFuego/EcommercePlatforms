@extends('layouts.app')

@section('title', '¡Pedido Realizado!')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Success Animation -->
            <div class="text-center mb-8">
                <div
                    class="inline-block bg-gradient-to-br from-green-400 to-emerald-500 rounded-full p-8 shadow-2xl mb-6 animate-bounce">
                    <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold mb-4">
                    <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        ¡Pedido Realizado con Éxito!
                    </span>
                </h1>
                <p class="text-xl text-gray-600">Tu pedido #{{ $order->id }} ha sido confirmado</p>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6">
                <h2 class="text-2xl font-bold mb-6">Resumen del Pedido</h2>

                <!-- Cook Info -->
                <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-orange-50 to-pink-50 rounded-xl mb-6">
                    @if($order->cook->user->profile_photo_path)
                        <img src="{{ asset('storage/' . $order->cook->user->profile_photo_path) }}"
                            alt="{{ $order->cook->user->name }}"
                            class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                    @else
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-orange-400 to-pink-600 rounded-full flex items-center justify-center text-3xl">
                            👨‍🍳
                        </div>
                    @endif
                    <div>
                        <h3 class="font-bold text-lg">{{ $order->cook->user->name }}</h3>
                        <p class="text-sm text-gray-600">📍 {{ $order->cook->user->address }}</p>
                        @if($order->cook->user->phone)
                            <p class="text-sm text-gray-600">📞 {{ $order->cook->user->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Items -->
                <div class="space-y-3 mb-6">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                @if($item->dish->photo_url)
                                    <img src="{{ Storage::url($item->dish->photo_url) }}" alt="{{ $item->dish->name }}"
                                        class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-orange-300 to-pink-400 rounded-lg flex items-center justify-center text-2xl">
                                        🍲
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold">{{ $item->dish->name }}</p>
                                    <p class="text-sm text-gray-600">Cantidad: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <span class="font-bold">${{ number_format($item->total_price, 0) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span class="font-semibold">${{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    @if($order->delivery_fee > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Envío:</span>
                            <span class="font-semibold">${{ number_format($order->delivery_fee, 0) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-2xl font-bold border-t pt-3">
                        <span>Total:</span>
                        <span class="bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">
                            ${{ number_format($order->total_amount, 0) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 mb-6">
                <h3 class="font-bold text-lg mb-4 flex items-center">
                    @if($order->delivery_type == 'delivery')
                        🛵 Información de Entrega
                    @else
                        🏃 Información de Retiro
                    @endif
                </h3>

                @if($order->delivery_type == 'delivery')
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Dirección:</span> {{ $order->delivery_address }}
                    </p>
                    <p class="text-sm text-gray-600">
                        Tiempo estimado: 30-45 minutos después de la preparación
                    </p>
                @else
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Retira en:</span> {{ $order->cook->user->address }}
                    </p>
                    <p class="text-sm text-gray-600">
                        Te notificaremos cuando esté listo para retirar
                    </p>
                @endif

                @if($order->notes)
                    <div class="mt-4 p-3 bg-white rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Notas:</p>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Payment Info -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-lg p-6 mb-6">
                <h3 class="font-bold text-lg mb-3">💳 Método de Pago</h3>
                <p class="text-gray-700">
                    {{ match ($order->payment_method) {
        'mercadopago' => 'MercadoPago',
        'cash' => 'Efectivo',
        'transfer' => 'Transferencia Bancaria',
        default => $order->payment_method
    } }}
                </p>
                @if($order->payment_id)
                    <p class="text-sm text-gray-600 mt-2">ID de pago: {{ $order->payment_id }}</p>
                @endif
            </div>

            <!-- Next Steps -->
            <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-2xl shadow-lg p-6 mb-6">
                <h3 class="font-bold text-lg mb-4">📋 Próximos Pasos</h3>
                <ol class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <span class="font-bold mr-2">1.</span>
                        <span>El cocinero revisará tu pedido</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">2.</span>
                        <span>Comenzará a preparar tu comida</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">3.</span>
                        <span>Te notificaremos cuando esté
                            {{ $order->delivery_type == 'delivery' ? 'en camino' : 'listo para retirar' }}</span>
                    </li>
                </ol>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('orders.show', $order->id) }}"
                    class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-4 rounded-xl font-bold text-center shadow-lg hover:shadow-xl transition-all">
                    Ver Detalle Completo
                </a>
                <a href="{{ route('marketplace.catalog') }}"
                    class="bg-white border-2 border-purple-600 text-purple-600 px-6 py-4 rounded-xl font-bold text-center hover:bg-purple-50 transition-all">
                    Seguir Explorando
                </a>
            </div>

            <p class="text-center text-gray-500 text-sm mt-6">
                ¿Necesitas ayuda? Contacta al cocinero directamente
            </p>
        </div>
    </div>
@endsection