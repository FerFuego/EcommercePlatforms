@extends('layouts.app')

@section('title', 'Detalle de Entrega')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
    @php
        $pickupLat = $delivery->pickup_lat ?? $delivery->order->cook->location_lat;
        $pickupLng = $delivery->pickup_lng ?? $delivery->order->cook->location_lng;
        // Fallback to order coordinates if assignment ones are missing
        $deliveryLat = $delivery->delivery_lat ?? $delivery->order->delivery_lat;
        $deliveryLng = $delivery->delivery_lng ?? $delivery->order->delivery_lng;
    @endphp

    <div class="container mx-auto px-4 py-12 max-w-6xl">
        <div class="mb-6">
            <a href="{{ route('delivery-driver.orders.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                ← Volver a Mis Entregas
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Header -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-3xl font-bold">Pedido #{{ $delivery->order_id }}</h1>
                        <span class="px-4 py-2 rounded-full text-sm font-bold
                                                 {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' :
        ($delivery->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ match ($delivery->status) {
        'assigned' => '📋 Asignado',
        'picked_up' => '📦 Recogido',
        'on_the_way' => '🚴 En Camino',
        'delayed' => '⏰ Demorado',
        'delivered' => '✅ Entregado',
        'rejected' => '❌ Rechazado',
        default => $delivery->status
    } }}
                        </span>
                    </div>
                    <p class="text-gray-600">Creado: {{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                    @if($delivery->delivered_at)
                        <p class="text-green-600 font-semibold">Entregado: {{ $delivery->delivered_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>

                <!-- Map -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Ruta</h3>
                        @if(($deliveryLat && $deliveryLng) || $delivery->order->delivery_address)
                            @php
                                $destination = ($deliveryLat && $deliveryLng)
                                    ? "$deliveryLat,$deliveryLng"
                                    : urlencode($delivery->order->delivery_address);
                            @endphp
                            <a href="https://www.google.com/maps/dir/?api=1&origin={{ $pickupLat }},{{ $pickupLng }}&destination={{ $destination }}&travelmode=driving"
                                target="_blank"
                                class="flex items-center space-x-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-200 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                <span>Abrir en Google Maps</span>
                            </a>
                        @endif
                    </div>
                    <div id="map" style="height: 400px; border-radius: 1rem;"></div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Items del Pedido</h3>
                    <div class="space-y-3">
                        @foreach($delivery->order->items as $item)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center space-x-4">
                                    @if($item->dish->image)
                                        <img src="{{ Storage::url($item->dish->image) }}" alt="{{ $item->dish->name }}"
                                            class="w-16 h-16 object-cover rounded-lg">
                                    @endif
                                    <div>
                                        <p class="font-bold">{{ $item->dish->name }}</p>
                                        <p class="text-sm text-gray-600">Cantidad: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <p class="font-bold">${{ number_format($item->price * $item->quantity, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t-2 border-gray-200">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total del Pedido:</span>
                            <span>${{ number_format($delivery->order->total_amount, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-green-600 mt-2">
                            <span>Tu Ganancia:</span>
                            <span>${{ number_format($delivery->delivery_fee, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pickup Info -->
                <div class="bg-blue-50 rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <span class="mr-2">📍</span> Punto de Retiro
                    </h3>
                    <p class="font-bold text-lg mb-2">{{ $delivery->order->cook->user->name }}</p>
                    <p class="text-gray-700 mb-3">{{ $delivery->order->cook->user->address }}</p>
                    <a href="tel:{{ $delivery->order->cook->user->phone }}"
                        class="block bg-blue-600 text-white px-4 py-2 rounded-lg text-center font-semibold hover:bg-blue-700 transition">
                        📞 Llamar
                    </a>
                </div>

                <!-- Delivery Info -->
                <div class="bg-green-50 rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <span class="mr-2">🏠</span> Punto de Entrega
                    </h3>
                    @if($delivery->order->status === 'preparing')
                        <p class="font-bold text-lg mb-2 text-yellow-800">⏳ En Preparación</p>
                        <p class="text-yellow-700 mb-3 italic">La dirección se mostrará cuando el pedido esté listo.</p>
                        <button disabled
                            class="block w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-center font-semibold cursor-not-allowed">
                            📞 Llamar
                        </button>
                    @else
                        <p class="font-bold text-lg mb-2">{{ $delivery->order->customer->name }}</p>
                        <p class="text-gray-700 mb-3">{{ $delivery->order->delivery_address }}</p>
                        <a href="tel:{{ $delivery->order->customer->phone }}"
                            class="block bg-green-500 text-white px-4 py-2 rounded-lg text-center font-semibold hover:bg-green-700 transition">
                            📞 Llamar
                        </a>
                    @endif
                </div>

                <!-- Actions -->
                @if(in_array($delivery->status, ['assigned', 'picked_up', 'on_the_way', 'delayed']))
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold mb-4">Acciones</h3>
                        <div class="space-y-3">
                            @if($delivery->status === 'assigned')
                                @if($delivery->order->status === 'preparing')
                                    <div class="bg-yellow-50 p-4 rounded-xl text-center">
                                        <p class="text-yellow-800 font-bold mb-2">⏳ Esperando al Cocinero</p>
                                        <p class="text-sm text-yellow-600">El pedido está en preparación. Se habilitará el retiro cuando
                                            esté listo.</p>
                                    </div>
                                @else
                                    <form action="{{ route('delivery-driver.orders.update-status', $delivery->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="picked_up">
                                        <button type="submit"
                                            class="w-full bg-gradient-to-r from-blue-500 to-cyan-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                            📦 Marcar Recogido
                                        </button>
                                    </form>
                                @endif
                            @elseif($delivery->status === 'picked_up')
                                <form action="{{ route('delivery-driver.orders.update-status', $delivery->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="on_the_way">
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                        🚴 En Camino
                                    </button>
                                </form>
                            @elseif($delivery->status === 'on_the_way')
                                <form action="{{ route('delivery-driver.orders.update-status', $delivery->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="delayed">
                                    <button type="submit"
                                        class="w-full bg-yellow-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                        ⏰ Marcar Demorado
                                    </button>
                                </form>
                            @endif

                            @if(in_array($delivery->status, ['on_the_way', 'delayed']))
                                <form action="{{ route('delivery-driver.orders.update-status', $delivery->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                        ✅ Marcar Entregado
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const map = L.map('map').setView([{{ $pickupLat }}, {{ $pickupLng }}], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Pickup marker
            const pickupIcon = L.divIcon({
                html: '<div style="background: #3B82F6; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 18px;">📍</div>',
                iconSize: [35, 35],
                className: ''
            });

            L.marker([{{ $pickupLat }}, {{ $pickupLng }}], { icon: pickupIcon })
                .addTo(map)
                .bindPopup('<b>Punto de Retiro</b><br>{{ $delivery->order->cook->user->name }}<br>{{ $delivery->order->cook->user->address }}');

            @if($deliveryLat && $deliveryLng)
                // Delivery marker (Server-side coordinates)
                const deliveryIcon = L.divIcon({
                    html: '<div style="background: #10B981; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 18px;">🏠</div>',
                    iconSize: [35, 35],
                    className: ''
                });

                L.marker([{{ $deliveryLat }}, {{ $deliveryLng }}], { icon: deliveryIcon })
                    .addTo(map)
                    .bindPopup('<b>Punto de Entrega</b><br>{{ $delivery->order->customer->name }}<br>{{ $delivery->order->delivery_address }}');
                // Draw line
                L.polyline([
                    [{{ $pickupLat }}, {{ $pickupLng }}],
                    [{{ $deliveryLat }}, {{ $deliveryLng }}]
                ], {
                    color: '#4F46E5', // Indigo-600
                    weight: 4,
                    opacity: 0.8,
                    smoothFactor: 1
                }).addTo(map);

                // Fit bounds
                map.fitBounds([
                    [{{ $pickupLat }}, {{ $pickupLng }}],
                    [{{ $deliveryLat }}, {{ $deliveryLng }}]
                ], { padding: [50, 50] });

            @elseif($delivery->order->delivery_address)
                // Client-side geocoding fallback
                const address = "{{ $delivery->order->delivery_address }}";
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = data[0].lat;
                            const lng = data[0].lon;

                            // Delivery marker
                            const deliveryIcon = L.divIcon({
                                html: '<div style="background: #10B981; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 18px;">🏠</div>',
                                iconSize: [35, 35],
                                className: ''
                            });

                            L.marker([lat, lng], { icon: deliveryIcon })
                                .addTo(map)
                                .bindPopup('<b>Punto de Entrega</b><br>{{ $delivery->order->customer->name }}<br>{{ $delivery->order->delivery_address }}');

                            // Draw line
                            L.polyline([
                                [{{ $pickupLat }}, {{ $pickupLng }}],
                                [lat, lng]
                            ], {
                                color: '#4F46E5', // Indigo-600
                                weight: 4,
                                opacity: 0.8,
                                smoothFactor: 1
                            }).addTo(map);

                            // Fit bounds
                            map.fitBounds([
                                [{{ $pickupLat }}, {{ $pickupLng }}],
                                [lat, lng]
                            ], { padding: [50, 50] });
                        } else {
                            console.warn('No se pudieron obtener coordenadas para la dirección:', address);
                        }
                    })
                    .catch(error => console.error('Error geocoding address:', error));
            @endif
        </script>
    @endpush
@endsection