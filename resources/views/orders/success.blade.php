@extends('layouts.app')

@section('title', '¡Pedido Realizado!')

@push('styles')
    <style>
        @keyframes success-pop {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            70% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-success-pop {
            animation: success-pop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Success Animation -->
            <div class="text-center mb-8">
                <div
                    class="inline-block bg-gradient-to-br from-green-400 to-emerald-500 rounded-full p-8 shadow-2xl mb-6 animate-success-pop">
                    <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold mb-4">
                    <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        ¡Pedido Registrado con Éxito!
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
                        <img src="{{ asset('uploads/' . $order->cook->user->profile_photo_path) }}"
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
                                    <img src="{{ asset('uploads/' . $item->dish->photo_url) }}" alt="{{ $item->dish->name }}"
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

            <!-- WhatsApp CTA -->
            @if(isset($whatsappUrl) && $whatsappUrl)
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-2xl p-8 mb-6 text-center">
                    <div class="text-white mb-4">
                        <h3 class="font-bold text-2xl mb-2">💬 Coordina con el Cocinero</h3>
                        <p class="text-green-100">Contacta a {{ $order->cook->user->name }} por WhatsApp para coordinar el pago y la entrega de tu pedido.</p>
                    </div>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                        class="inline-flex items-center space-x-3 bg-white text-green-700 px-10 py-5 rounded-2xl font-bold text-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>Abrir WhatsApp</span>
                    </a>
                </div>
            @else
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-6 mb-6">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <h3 class="font-bold text-amber-800">Teléfono no disponible</h3>
                            <p class="text-sm text-amber-700 mt-1">El cocinero no tiene un número de teléfono registrado. Puedes contactarlo a través de la plataforma — ya le hemos enviado una notificación de tu pedido.</p>
                            @if($order->cook->user->phone)
                                <p class="text-sm text-amber-700 mt-2">📞 {{ $order->cook->user->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Next Steps -->
            <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-2xl shadow-lg p-6 mb-6">
                <h3 class="font-bold text-lg mb-4">📋 Próximos Pasos</h3>
                <ol class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <span class="font-bold mr-2">1.</span>
                        <span>Contacta al cocinero por WhatsApp para coordinar pago y entrega</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">2.</span>
                        <span>El cocinero preparará tu comida</span>
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
                También le notificamos al cocinero por la plataforma 📢
            </p>
        </div>
    </div>
    @push('scripts')
        <script>
            // Real-time Updates with Laravel Echo
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Echo) {
                    window.Echo.private('order.{{ $order->id }}')
                        .listen('OrderStatusUpdated', (e) => {
                            console.log('Order status updated:', e);
                            window.location.reload();
                        });
                }
            });
        </script>
    @endpush
@endsection