@extends('layouts.app')

@section('title', 'Dashboard - Cocinero')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2">
                <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                    ¡Hola, {{ auth()->user()->name }}!
                </span>
            </h1>
            <p class="text-gray-600 text-lg">Aquí está el resumen de tu cocina</p>
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
        @elseif(!$cook->is_approved)
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
            <!-- ... Stats ... -->
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
                        <a href="{{ route('cook.dishes.create') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Nuevo Plato
                        </a>
                        <a href="{{ route('cook.dishes.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Mis Platos
                        </a>
                        <a href="{{ route('cook.orders.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Ver Pedidos
                        </a>
                        <a href="{{ route('cook.profile.edit') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Configuración
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
                                    class="px-3 py-1 rounded-full text-sm font-semibold {{ $cook->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $cook->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Activo:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" {{ $cook->active ? 'checked' : '' }} class="sr-only peer"
                                    onchange="toggleActive(this)">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-purple-500 peer-checked:to-pink-600">
                                </div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Rating:</span>
                            <span class="font-bold text-yellow-500">⭐ {{ number_format($cook->rating_avg, 1) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Reviews:</span>
                            <span class="font-semibold">{{ $cook->rating_count }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-6">Actividad Reciente</h3>

                    @php
                        $recentOrders = $cook->orders()->with('customer')->latest()->limit(5)->get();
                    @endphp

                    @forelse($recentOrders as $order)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-pink-50 rounded-xl mb-3 hover:shadow-md transition-shadow">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ substr($order->customer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $order->customer->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $order->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-800">${{ number_format($order->total_amount, 0) }}</p>
                                            <span class="text-xs px-2 py-1 rounded-full  {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' :
                            ($order->status == 'rejected_by_cook' ? 'bg-red-100 text-red-800' :
                                ($order->status == 'awaiting_cook_acceptance' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-blue-100 text-blue-800')) }}">
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
                    @empty
                        <div class="text-center py-12">
                            <div class="text-5xl mb-3">📭</div>
                            <p class="text-gray-500">No hay pedidos recientes</p>
                        </div>
                    @endforelse

                    @if($recentOrders->count() > 0)
                        <a href="{{ route('cook.orders.index') }}"
                            class="block text-center mt-6 text-purple-600 font-semibold hover:text-pink-600 transition">
                            Ver Todos los Pedidos →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleActive(checkbox) {
                fetch('{{ route("cook.profile.toggle-active") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ active: checkbox.checked })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Optional: Show a toast notification or update UI elsewhere
                            console.log('Status updated to:', data.active);
                        } else {
                            alert('Error al actualizar el estado');
                            checkbox.checked = !checkbox.checked; // Revert
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al procesar la solicitud');
                        checkbox.checked = !checkbox.checked; // Revert
                    });
            }
        </script>
    @endpush

@endsection