@extends('layouts.app')

@section('title', 'Carrito de Compras')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8">
        <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
            Tu Carrito 🛒
        </span>
    </h1>

    @if(empty($cart))
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <div class="text-8xl mb-4">🛍️</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-3">Tu carrito está vacío</h2>
            <p class="text-gray-600 mb-6">¡Explora nuestros cocineros y encuentra algo delicioso!</p>
            <a href="{{ route('marketplace.catalog') }}" class="inline-block bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                Explorar Cocineros
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart as $index => $item)
                    <div class="bg-white rounded-2xl shadow-lg p-6 flex items-center space-x-6 hover:shadow-xl transition-shadow">
                        @if($item['photo_url'])
                            <img src="{{ Storage::url($item['photo_url']) }}" 
                                 alt="{{ $item['name'] }}" 
                                 class="w-24 h-24 object-cover rounded-xl">
                        @else
                            <div class="w-24 h-24 bg-gradient-to-br from-orange-300 to-pink-400 rounded-xl flex items-center justify-center text-4xl">
                                🍲
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800">{{ $item['name'] }}</h3>
                            <p class="text-gray-600">Cantidad: {{ $item['quantity'] }}</p>
                            <p class="text-lg font-bold text-pink-600">${{ number_format($item['price'] * $item['quantity'], 0) }}</p>
                        </div>
                        
                        <form action="{{ route('cart.remove', $index) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-orange-600 via-pink-600 to-purple-600 rounded-2xl shadow-2xl p-6 text-white sticky top-24">
                    <h3 class="text-2xl font-bold mb-6">Resumen</h3>
                    
                    @php
                        $subtotal = array_reduce($cart, function($carry, $item) {
                            return $carry + ($item['price'] * $item['quantity']);
                        }, 0);
                    @endphp
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-lg">
                            <span>Subtotal:</span>
                            <span class="font-bold">${{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span>Items:</span>
                            <span class="font-bold">{{ count($cart) }}</span>
                        </div>
                        <div class="border-t border-white/30 pt-4">
                            <div class="flex justify-between text-2xl font-bold">
                                <span>Total:</span>
                                <span>${{ number_format($subtotal, 0) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('orders.checkout') }}" class="block w-full bg-white text-purple-600 px-6 py-4 rounded-xl font-bold text-center shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                        Proceder al Pago →
                    </a>
                    
                    <a href="{{ route('marketplace.catalog') }}" class="block w-full text-center mt-4 text-white/90 hover:text-white transition">
                        ← Seguir comprando
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
