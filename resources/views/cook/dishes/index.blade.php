@extends('layouts.app')

@section('title', 'Mis Platos')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    <span
                        class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Mis Platos
                    </span>
                </h1>
                <p class="text-gray-600">Gestiona tu menú</p>
            </div>
            <a href="{{ route('cook.dishes.create') }}"
                class="bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                + Nuevo Plato
            </a>
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
            </div>

            <div class="lg:col-span-2">
                @if($dishes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        @foreach($dishes as $dish)
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all group">
                                <!-- Image -->
                                <div class="relative">
                                    @if($dish->photo_url)
                                        <img src="{{ Storage::url($dish->photo_url) }}" alt="{{ $dish->name }}"
                                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform">
                                    @else
                                        <div
                                            class="w-full h-48 bg-gradient-to-br from-orange-300 to-pink-400 flex items-center justify-center text-6xl">
                                            🍲
                                        </div>
                                    @endif

                                    <!-- Status Badge -->
                                    <div class="absolute top-4 right-4">
                                        @if($dish->is_active && $dish->available_stock > 0)
                                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                ✓ Activo
                                            </span>
                                        @elseif(!$dish->is_active)
                                            <span class="px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                ⏸️ Inactivo
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                ❌ Sin Stock
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $dish->name }}</h3>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $dish->description }}</p>

                                    <div class="flex items-center justify-between items-end mb-4">
                                        <span class="text-2xl font-bold text-pink-600">${{ number_format($dish->price, 0) }}</span>
                                        <div class="text-right">
                                            <!-- <p class="text-sm text-gray-500">Stock</p> -->
                                            <div class="flex items-center space-x-2">
                                                <button onclick="updateStock({{ $dish->id }}, -1, {{ $dish->available_stock }})"
                                                    class="w-6 h-6 bg-gray-200 rounded-lg hover:bg-gray-300 transition">-</button>
                                                <span id="stock-{{ $dish->id }}"
                                                    class="font-bold text-lg w-10 text-center">{{ $dish->available_stock }}</span>
                                                <button onclick="updateStock({{ $dish->id }}, 1, {{ $dish->available_stock }})"
                                                    class="w-6 h-6 bg-gray-200 rounded-lg hover:bg-gray-300 transition">+</button>
                                            </div>
                                        </div>
                                    </div>

                                    @if($dish->diet_tags && count($dish->diet_tags) > 0)
                                        <div class="flex flex-wrap gap-1 mb-4">
                                            @foreach($dish->diet_tags as $tag)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">
                                                    {{ ucfirst($tag) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <label
                                            class="flex items-center justify-center p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                                            <input type="checkbox" {{ $dish->is_active ? 'checked' : '' }}
                                                onchange="toggleActive({{ $dish->id }})" class="w-4 h-4 text-purple-600 rounded">
                                            <span class="ml-2 text-sm font-medium">Activo</span>
                                        </label>

                                        <a href="{{ route('cook.dishes.edit', $dish->id) }}"
                                            class="flex items-center justify-center p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                                            ✏️ Editar
                                        </a>
                                    </div>

                                    <form action="{{ route('cook.dishes.destroy', $dish->id) }}" method="POST" class="mt-2"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este plato?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $dishes->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <div class="text-8xl mb-4">🍽️</div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">Aún no tienes platos</h2>
                        <p class="text-gray-600 mb-6">Comienza creando tu primer plato delicioso</p>
                        <a href="{{ route('cook.dishes.create') }}"
                            class="inline-block bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                            Crear Mi Primer Plato
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function toggleActive(dishId) {
                fetch(`/cook/dishes/${dishId}/toggle-active`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }

            function updateStock(dishId, change, currentStock) {
                const newStock = Math.max(0, currentStock + change);

                fetch(`/cook/dishes/${dishId}/update-stock`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ available_stock: newStock })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`stock-${dishId}`).textContent = newStock;
                        }
                    });
            }
        </script>
    @endpush

@endsection