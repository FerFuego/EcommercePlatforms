@extends('layouts.app')

@section('title', 'Mis Pedidos')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Mis Pedidos
                    </span>
                </h1>
                <p class="text-gray-600">Historial de tus pedidos</p>
            </div>
            <a href="{{ route('marketplace.catalog') }}"
                class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Explorador
            </a>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all"
                        data-order-id="{{ $order->id }}">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-2xl font-bold text-gray-800">Pedido #{{ $order->id }}</h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ 
                                                $order->status == 'delivered' ? 'bg-green-100 text-green-800' :
                                                ($order->status == 'rejected_by_cook' ? 'bg-red-100 text-red-800' :
                                                ($order->status == 'awaiting_cook_acceptance' ? 'bg-yellow-100 text-yellow-800' :
                                                ($order->status == 'scheduled' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'))) 
                                            }}" 
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
                                                } 
                                            }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600">
                                        Cocinero: <span class="font-semibold">{{ $order->cook->user->name }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>

                                <div class="text-right">
                                    <p
                                        class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">
                                        ${{ number_format($order->total_amount, 0) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $order->delivery_type == 'delivery' ? '🛵 Delivery' : '🏃 Retiro' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Order Items Preview -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($order->items->take(3) as $item)
                                    <span class="px-3 py-1 bg-gradient-to-r from-gray-100 to-pink-50 rounded-lg text-sm">
                                        {{ $item->quantity }}x {{ $item->dish->name }}
                                    </span>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <span class="px-3 py-1 bg-gray-100 rounded-lg text-sm text-gray-600">
                                        +{{ $order->items->count() - 3 }} más
                                    </span>
                                @endif
                            </div>

                            <!-- Rejection Reason (if rejected) -->
                            @if($order->status === 'rejected_by_cook' && $order->rejection_reason)
                                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-4">
                                    <p class="text-xs font-bold text-red-800 mb-1">❌ Motivo del rechazo:</p>
                                    <p class="text-sm text-gray-700 italic">
                                        "{{ Str::limit($order->rejection_reason, 100) }}"
                                    </p>
                                    @if(strlen($order->rejection_reason) > 100)
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="text-xs text-red-600 hover:text-red-800 font-semibold mt-1 inline-block">
                                            Ver motivo completo →
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex items-center space-x-3 justify-end md:justify-end">
                                <a href="{{ route('orders.show', $order->id) }}"
                                    class="bg-gradient-to-r from-purple-500 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold text-center shadow-lg hover:shadow-xl transition-all max-w-xs w-full md:max-w-none md:w-auto">
                                    Ver Detalle
                                </a>

                                @if($order->canBeReviewed())
                                    <button onclick="openReviewModal({{ $order->id }})"
                                        class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                        ⭐ Calificar
                                    </button>
                                @endif

                                @if($order->status === 'delivered')
                                    <form action="{{ route('orders.reorder', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                            + Repetir Pedido
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="text-8xl mb-4">📦</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">No tienes pedidos aún</h2>
                <p class="text-gray-600 mb-6">¡Explora nuestros cocineros y haz tu primer pedido!</p>
                <a href="{{ route('marketplace.catalog') }}"
                    class="inline-block bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                    Explorar Cocineros
                </a>
            </div>
        @endif
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50"
        onclick="closeReviewModal(event)">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4" onclick="event.stopPropagation()">
            <h3 class="text-2xl font-bold mb-6">Calificar Pedido</h3>
            <form id="reviewForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Calificación</label>
                    <div class="flex items-center justify-center space-x-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})"
                                class="star-btn text-4xl hover:scale-110 transition-transform">
                                ⭐
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Comentario (opcional)</label>
                    <textarea name="comment" rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                        Enviar Calificación
                    </button>
                    <button type="button" onclick="closeReviewModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                        Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openReviewModal(orderId) {
                document.getElementById('reviewModal').classList.remove('hidden');
                document.getElementById('reviewModal').classList.add('flex');
                document.getElementById('reviewForm').action = `/orders/${orderId}/review`;
            }

            function closeReviewModal(event) {
                if (!event || event.target.id === 'reviewModal') {
                    document.getElementById('reviewModal').classList.add('hidden');
                    document.getElementById('reviewModal').classList.remove('flex');
                }
            }

            function setRating(stars) {
                document.getElementById('rating').value = stars;
                const buttons = document.querySelectorAll('.star-btn');
                buttons.forEach((btn, index) => {
                    if (index < stars) {
                        btn.style.color = '#fbbf24'; // yellow-400
                        btn.style.opacity = '1';
                    } else {
                        btn.style.color = '#d1d5db'; // gray-300
                        btn.style.opacity = '1';
                    }
                });
            }
        </script>
    @endpush

@endsection