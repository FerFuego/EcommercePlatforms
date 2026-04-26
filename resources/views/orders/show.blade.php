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
                    @if($order->status === 'scheduled' && $order->scheduled_time)
                        <div class="mt-2 inline-flex items-center px-4 py-2 bg-purple-100 text-purple-800 rounded-xl font-bold animate-pulse border border-purple-200">
                            <span class="mr-2">📅</span> PROGRAMADO PARA: {{ $order->scheduled_time->format('d/m/Y H:i') }}
                        </div>
                    @elseif($order->scheduled_time && !in_array($order->status, ['delivered', 'cancelled', 'rejected_by_cook']))
                        <p class="text-xs text-purple-600 mt-1 font-bold flex items-center">
                            <span class="mr-1">🕒</span> Programado: {{ $order->scheduled_time->format('d/m H:i') }}
                        </p>
                    @endif
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
                                    @elseif($order->status === 'scheduled') bg-purple-100 text-purple-700
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
                        'scheduled' => '📅 Programado',
                        default => $order->status
                    } }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Review Section -->
                    @if($order->status === 'delivered' && auth()->user() && auth()->user()->isCustomer())
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
                                El cocinero no pudo aceptar este pedido. Contacta al cocinero por WhatsApp si necesitas más información.
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
                                        <img src="{{ asset('uploads/' . $driver->profile_photo) }}" alt="{{ $order->deliveryAssignment->deliveryUser->name }}"
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

                    <!-- Timeline / Order Tracking -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-xl font-bold mb-6 flex items-center">
                            <span class="mr-2">🕒</span> Seguimiento del Pedido
                        </h2>
                        <div class="relative pl-8 space-y-6">
                            <!-- Vertical Line -->
                            <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-gray-200"></div>

                            @foreach($order->logs as $log)
                                <div class="relative">
                                    <!-- Dot -->
                                    <div class="absolute -left-[24px] top-1.5 w-4 h-4 rounded-full border-2 border-white shadow-sm
                                        {{ $loop->last ? 'bg-green-500 ring-4 ring-green-100' : 'bg-gray-400' }}">
                                    </div>
                                    
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                                        <div>
                                            <p class="font-bold text-gray-800 {{ $loop->last ? 'text-lg text-green-700' : 'text-sm' }}">
                                                {{ $log->description }}
                                            </p>
                                            @if($log->user)
                                                <p class="text-[10px] text-gray-400 flex items-center mt-0.5">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Realizado por {{ $log->user->name }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-bold text-gray-500 whitespace-nowrap bg-gray-100 px-2 py-1 rounded-lg">
                                                {{ $log->created_at->format('H:i') }}
                                            </span>
                                            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">
                                                {{ $log->created_at->format('d/m/y') }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($log->metadata && !empty($log->metadata['reason']))
                                        <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded-lg border border-red-100 italic">
                                            "{{ $log->metadata['reason'] }}"
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-xl font-bold mb-6">Items del Pedido</h2>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center space-x-4">
                                        @if($item->dish->photo_url)
                                            <img src="{{ asset('uploads/' . $item->dish->photo_url) }}" alt="{{ $item->dish->name }}"
                                                class="w-20 h-20 object-cover rounded-xl shadow-sm">
                                        @else
                                            <div
                                                class="w-20 h-20 bg-gradient-to-br from-orange-300 to-pink-400 rounded-xl flex items-center justify-center text-3xl shadow-sm">
                                                🍲
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-lg text-gray-800">{{ $item->dish->name }}</p>
                                            
                                            @if($item->options->count() > 0)
                                                <div class="mt-1 space-y-1">
                                                    @foreach($item->options as $option)
                                                        <div class="flex items-center text-xs text-gray-500 bg-gray-50 px-2 py-0.5 rounded-lg w-fit border border-gray-100">
                                                            <span class="mr-1 text-purple-500">•</span>
                                                            {{ $option->dishOption->name }}
                                                            @if($option->price > 0)
                                                                <span class="ml-1 font-bold text-purple-600">(+${{ number_format($option->price, 0) }})</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <p class="text-gray-600 mt-2">Cantidad: {{ $item->quantity }}</p>
                                            <p class="text-sm text-gray-500">${{ number_format($item->unit_price, 0) }} c/u</p>
                                        </div>
                                    </div>
                                    <span
                                        class="font-bold text-lg text-purple-600">${{ number_format($item->total_price, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Delivery & WhatsApp Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- WhatsApp Contact -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <span
                                    class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-2">💬</span>
                                Contacto
                            </h3>
                            @if(isset($whatsappUrl) && $whatsappUrl)
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center space-x-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-5 py-3 rounded-xl font-semibold hover:shadow-lg transition-all w-full justify-center">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    <span>Abrir WhatsApp</span>
                                </a>
                                <p class="text-xs text-gray-500 mt-2 text-center">Coordina pago y entrega directamente</p>
                            @else
                                <p class="text-gray-500 text-sm">Teléfono no disponible. El cocinero fue notificado por la plataforma.</p>
                            @endif
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
                                <img src="{{ asset('uploads/' . $order->cook->user->profile_photo_path) }}"
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
                            <div class="flex text-gray-700">
                                <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="font-semibold mr-1">Dirección:</span> {{ $order->cook->user->address }}
                            </div>
                            @if($order->cook->opening_time && $order->cook->closing_time)
                                <div class="flex text-gray-700">
                                    <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold mr-1">Horario:</span>
                                    {{ \Carbon\Carbon::parse($order->cook->opening_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($order->cook->closing_time)->format('H:i') }}
                                </div>
                            @endif
                            @if($order->cook->user->phone)
                                <div class="flex items-center text-gray-700">
                                    <span class="mr-2">📞</span>
                                    <span>{{ $order->cook->user->phone }}</span>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('marketplace.cook.profile', $order->cook->id) }}"
                            class="block w-full mt-4 text-center text-purple-600 font-semibold hover:text-pink-600 transition">
                            Ver Perfil Completo
                        </a>
                    </div>

                    <!-- Order Summary -->
                    <div class="rounded-2xl p-6 bg-white rounded-2xl shadow-lg p-6">
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

                    @if (auth()->user() && auth()->user()->isCustomer())
                        <div class="w-full justify-center">
                            <form action="{{ route('orders.reorder', $order->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="bg-gradient-to-r from-green-500 to-teal-600 text-white text-center px-8 py-3 rounded-xl font-bold hover:shadow-xl transition shadow-md flex items-center justify-center w-full">
                                    <span class="mr-2">+</span> Volver a Pedir
                                </button>
                            </form>
                            
                            <a href="{{ route('orders.my') }}"
                                class="block w-full bg-gradient-to-r from-gray-500 to-gray-600 text-white mt-2 px-6 py-3 rounded-xl font-semibold text-center shadow-lg hover:shadow-xl transition-all">
                                ← Volver a Mis Pedidos
                            </a>
                        </div>
                    @endif


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

    // Real-time Updates with Laravel Echo
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.private('order.{{ $order->id }}')
                .listen('OrderStatusUpdated', (e) => {
                    console.log('Order status updated:', e);
                    // For now, a simple reload is the most reliable way 
                    // to update all complex UI components (timeline, badges, forms)
                    window.location.reload();
                });
        }
    });
</script>
@endpush
@endsection