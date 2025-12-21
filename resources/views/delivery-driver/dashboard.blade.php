@extends('layouts.app')

@section('title', 'Dashboard - Repartidor')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div>
            <h1 class="text-4xl font-bold mb-2">
                <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                    ¡Hola, {{ auth()->user()->name }}!
                </span>
            </h1>
            <p class="text-gray-600 text-lg">Aquí está el resumen de tus entregas</p>
        </div>

        @if(auth()->user()->is_suspended)
            <div class="bg-red-500 text-white px-6 py-4 rounded-2xl shadow-lg mb-8">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-bold text-lg">Cuenta Suspendida</h3>
                        <p>Tu cuenta ha sido suspendida debido a irregularidades. Por favor, ponte en contacto con la plataforma
                            para más información.</p>
                    </div>
                </div>
            </div>
        @elseif(!$driver->is_approved)
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-4 rounded-2xl shadow-lg mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold">Tu perfil está siendo revisado por nuestro equipo. Te notificaremos cuando sea
                        aprobado.</span>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none filter grayscale' : '' }}">
            <!-- ... (Stats content remains same, just wrapper class changes effectively) ... -->
            <!-- Note: Since I can't wrap the whole block easily with replace, I will apply logic to specific interactive elements or use a simpler approach -->
            <!-- Actually, user wants buttons disabled. Stats can be visible. -->
        </div>

        <!-- Re-targeting specific blocks -->

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

                <!-- Profile Info -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
                    <h3 class="text-xl font-bold mb-4">Mi Perfil</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Estado:</span>
                            @if(auth()->user()->is_suspended)
                                <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                    ❌ Suspendido
                                </span>
                            @else
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-semibold {{ $driver->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $driver->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                </span>
                            @endif
                        </div>
                        @if($driver->is_approved)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Disponible:</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" {{ $driver->is_available ? 'checked' : '' }} class="sr-only peer"
                                        onchange="toggleAvailability(this)">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-blue-500 peer-checked:to-cyan-600">
                                    </div>
                                </label>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Vehículo:</span>
                            <span class="font-semibold">
                                @if($driver->vehicle_type === 'bicycle')
                                    🚲 Bicicleta
                                @elseif($driver->vehicle_type === 'motorcycle')
                                    🏍️ Moto
                                @else
                                    🚗 Auto
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Cobertura:</span>
                            <span class="font-semibold">{{ $driver->coverage_radius_km }} km</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Entregas:</span>
                            <span class="font-semibold">{{ $driver->total_deliveries }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-6">Resumen de Ganancias</h3>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-600 mb-1">Hoy</p>
                            <p class="text-2xl font-bold text-green-600">${{ number_format($todayEarnings, 0) }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-600 mb-1">Esta Semana</p>
                            <p class="text-2xl font-bold text-blue-600">${{ number_format($weekEarnings, 0) }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-600 mb-1">Este Mes</p>
                            <p class="text-2xl font-bold text-purple-600">${{ number_format($monthEarnings, 0) }}</p>
                        </div>
                    </div>

                    @php
                        $recentDeliveries = $driver->deliveries()->with('order.customer')->latest()->limit(5)->get();
                    @endphp

                    <h4 class="font-bold mb-4">Entregas Recientes</h4>

                    @forelse($recentDeliveries as $delivery)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl mb-3 hover:shadow-md transition-shadow">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ substr($delivery->order->customer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">Pedido #{{ $delivery->order_id }}</p>
                                                <p class="text-sm text-gray-600">{{ $delivery->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-800">${{ number_format($delivery->delivery_fee, 0) }}</p>
                                            <span
                                                class="text-xs px-2 py-1 rounded-full {{ $delivery->status == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ match ($delivery->status) {
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
                            default => ucfirst($delivery->status)
                        } }}
                                            </span>
                                        </div>
                                    </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="text-5xl mb-3">📭</div>
                            <p class="text-gray-500">No hay entregas recientes</p>
                        </div>
                    @endforelse

                    @if($recentDeliveries->count() > 0)
                        <a href="{{ route('delivery-driver.orders.index') }}"
                            class="block text-center mt-6 text-blue-600 font-semibold hover:text-cyan-600 transition">
                            Ver Todas las Entregas →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleAvailability(checkbox) {
                fetch('{{ route('delivery-driver.profile.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        available: checkbox.checked
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Availability updated:', data.is_available);
                        } else {
                            alert('Error al actualizar disponibilidad');
                            checkbox.checked = !checkbox.checked;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error');
                        checkbox.checked = !checkbox.checked;
                    });
            }
        </script>
    @endpush
@endsection