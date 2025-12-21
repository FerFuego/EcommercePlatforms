@extends('layouts.app')

@section('title', 'Pedidos Disponibles')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6 bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
            Pedidos Disponibles
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden">
                @if(auth()->user()->is_suspended)
                    <div class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
                    </div>
                @endif

                <h3 class="text-xl font-bold mb-4">Acciones Rápidas</h3>
                <div class="space-y-3 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none' : '' }}">
                    @if($driver->is_approved)
                        <a href="{{ route('delivery-driver.dashboard') }}" 
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Dashboard
                        </a>
                        <a href="{{ route('delivery-driver.orders.available') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Ver Pedidos Disponibles
                        </a>
                        <a href="{{ route('delivery-driver.orders.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Mis Entregas
                        </a>
                        <a href="{{ route('delivery-driver.earnings') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Ver Ganancias
                        </a>
                    @endif
                    <a href="{{ route('delivery-driver.profile.edit') }}"
                        class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                        Editar Perfil
                    </a>
                </div>
            </div>

            <div class="g:col-span-2 col-span-2">
                @if($availableOrders->isEmpty())
                    <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                        <div class="text-6xl mb-4">📭</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay pedidos disponibles</h3>
                        <p class="text-gray-600">Vuelve más tarde o amplía tu área de cobertura</p>
                    </div>
                @else
                    <!-- Map View -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                        <div id="map" style="height: 500px; border-radius: 1rem;"></div>
                    </div>

                    <!-- List View -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($availableOrders as $order)
                            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition" id="order-{{ $order->id }}">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold">Pedido #{{ $order->id }}</h3>
                                        @if($order->status === 'preparing')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                ⏳ En Preparación (Reservar)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                                🚀 Listo para Retiro
                                            </span>
                                        @endif
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                        ${{ number_format($order->delivery_fee ?? 500, 0) }}
                                    </span>
                                </div>

                                <div class="space-y-3 mb-4">
                                    <div class="flex items-start space-x-2">
                                        <span class="text-gray-600">📍 Retiro:</span>
                                        <span class="font-semibold">{{ $order->cook->user->name }}</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-gray-600">📦 Items:</span>
                                        <span>{{ $order->items->count() }} productos</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-gray-600">💰 Total:</span>
                                        <span class="font-bold">${{ number_format($order->total_amount, 0) }}</span>
                                    </div>
                                    @if($order->status === 'preparing')
                                        <p class="text-sm text-yellow-700 bg-yellow-50 p-2 rounded">
                                            ⚠️ El pedido se está preparando. Al aceptar, lo reservas y se te notificará cuando esté listo.
                                        </p>
                                    @endif
                                </div>

                                <form action="{{ route('delivery-driver.orders.accept', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r {{ $order->status === 'preparing' ? 'from-yellow-500 to-orange-600' : 'from-green-500 to-emerald-600' }} text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                        {{ $order->status === 'preparing' ? '⏳ Reservar Pedido' : '✓ Aceptar Pedido' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            @if($availableOrders->isNotEmpty())
                // Initialize map
                const map = L.map('map').setView([{{ $driver->location_lat }}, {{ $driver->location_lng }}], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Driver location
                const driverIcon = L.divIcon({
                    html: '<div style="background: #3B82F6; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 16px;">🚴</div>',
                    iconSize: [30, 30],
                    className: ''
                });

                L.marker([{{ $driver->location_lat }}, {{ $driver->location_lng }}], { icon: driverIcon })
                    .addTo(map)
                    .bindPopup('<b>Tu ubicación</b>');

                // Coverage area
                L.circle([{{ $driver->location_lat }}, {{ $driver->location_lng }}], {
                    radius: {{ $driver->coverage_radius_km * 1000 }},
                    color: '#3B82F6',
                    fillColor: '#3B82F6',
                    fillOpacity: 0.1
                }).addTo(map);

                // Order markers
                @foreach($availableOrders as $order)
                    const orderIcon{{ $order->id }} = L.divIcon({
                        html: '<div style="background: #10B981; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 18px;">📦</div>',
                        iconSize: [35, 35],
                        className: ''
                    });

                    L.marker([{{ $order->cook->location_lat }}, {{ $order->cook->location_lng }}], { icon: orderIcon{{ $order->id }} })
                        .addTo(map)
                        .bindPopup(`
                                            <div style="min-width: 200px;">
                                                <h4 style="font-weight: bold; margin-bottom: 8px;">Pedido #{{ $order->id }}</h4>
                                                <p style="margin: 4px 0;"><strong>Retiro:</strong> {{ $order->cook->user->name }}</p>
                                                <p style="margin: 4px 0;"><strong>Items:</strong> {{ $order->items->count() }}</p>
                                                <p style="margin: 4px 0;"><strong>Pago:</strong> ${{ number_format($order->delivery_fee ?? 500, 0) }}</p>
                                                <button onclick="document.querySelector('#order-{{ $order->id }} form').submit()" 
                                                    style="margin-top: 8px; width: 100%; background: linear-gradient(to right, #10B981, #059669); color: white; padding: 8px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold;">
                                                    Aceptar
                                                </button>
                                            </div>
                                        `);
                @endforeach
            @endif
        </script>
    @endpush
@endsection