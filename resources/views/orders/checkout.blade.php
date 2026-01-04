@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold mb-8 text-center">
            <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                Finalizar Pedido
            </span>
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('orders.process') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Delivery Type -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-orange-500 to-pink-600 rounded-full flex items-center justify-center text-white mr-3">1</span>
                            Método de Entrega
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="delivery_type" value="pickup" checked class="peer sr-only">
                                <div
                                    class="bg-gradient-to-br from-gray-50 to-blue-50 peer-checked:from-blue-100 peer-checked:to-indigo-100 border-2 border-gray-200 peer-checked:border-blue-500 rounded-2xl p-6 transition-all">
                                    <div class="text-4xl mb-3 text-center">🏃</div>
                                    <h3 class="font-bold text-lg text-center mb-2">Retiro en Cocina</h3>
                                    <p class="text-sm text-gray-600 text-center">Sin costo adicional</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="delivery_type" value="delivery" class="peer sr-only"
                                    onclick="toggleDeliveryFields(true)">
                                <div
                                    class="bg-gradient-to-br from-gray-50 to-purple-50 peer-checked:from-purple-100 peer-checked:to-pink-100 border-2 border-gray-200 peer-checked:border-purple-500 rounded-2xl p-6 transition-all">
                                    <div class="text-4xl mb-3 text-center">🛵</div>
                                    <h3 class="font-bold text-lg text-center mb-2">Delivery</h3>
                                    <p class="text-sm text-gray-600 text-center">+ $500</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Delivery Address (conditional) -->
                    <div id="deliveryFields" class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white mr-3">2</span>
                            Dirección de Entrega
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Dirección Completa *</label>
                                <input type="text" name="delivery_address" id="delivery_address"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                    placeholder="Calle, número, piso, depto" value="{{ auth()->user()->address ?? '' }}">
                            </div>

                            <input type="hidden" name="delivery_lat" id="delivery_lat">
                            <input type="hidden" name="delivery_lng" id="delivery_lng">

                            <button type="button" onclick="getCurrentLocation()"
                                class="text-purple-600 font-medium hover:text-pink-600 transition text-sm">
                                📍 Usar mi ubicación actual
                            </button>
                        </div>
                    </div>

                    <!-- Cook Info -->
                    <div
                        class="bg-gradient-to-br from-orange-50 to-pink-50 rounded-2xl shadow-lg p-8 border-2 border-orange-200">
                        <div class="flex items-center space-x-4">
                            @if($cook->user->profile_photo_path)
                                <img src="{{ asset('uploads/' . $cook->user->profile_photo_path) }}"
                                    alt="{{ $cook->user->name }}"
                                    class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                            @else
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-orange-400 to-pink-600 rounded-full flex items-center justify-center text-3xl shadow-md">
                                    👨‍🍳
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-lg">{{ $cook->user->name }}</h3>
                                <p class="text-sm text-gray-600">📍 {{ $cook->user->address }}</p>
                                <div class="flex items-center text-sm mt-1">
                                    <span class="text-yellow-500 font-bold">⭐
                                        {{ number_format($cook->rating_avg, 1) }}</span>
                                    <span class="text-gray-400 ml-1">({{ $cook->rating_count }} reviews)</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">📞 {{ $cook->user->phone ?? 'No especificado' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white mr-3">3</span>
                            Método de Pago
                        </h2>

                        <div class="space-y-3">
                            <label
                                class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl cursor-pointer border-2 border-transparent hover:border-blue-500 transition">
                                <input type="radio" name="payment_method" value="mercadopago" checked
                                    class="w-5 h-5 text-blue-600">
                                <span class="ml-3 font-semibold">MercadoPago 💳</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl cursor-pointer border-2 border-transparent hover:border-green-500 transition">
                                <input type="radio" name="payment_method" value="cash" class="w-5 h-5 text-green-600">
                                <span class="ml-3 font-semibold">Efectivo 💵</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl cursor-pointer border-2 border-transparent hover:border-purple-500 transition">
                                <input type="radio" name="payment_method" value="transfer" class="w-5 h-5 text-purple-600">
                                <span class="ml-3 font-semibold">Transferencia 🏦</span>
                            </label>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6">Notas Adicionales (Opcional)</h2>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                            placeholder="Alergias, preferencias, instrucciones especiales..."></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-5 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all">
                        Confirmar Pedido →
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-2xl p-6 sticky top-24">
                    <h3 class="text-2xl font-bold mb-6">Resumen del Pedido</h3>

                    <!-- Cook Info Mini -->
                    <div class="flex items-center space-x-3 mb-6 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        @if($cook->user->profile_photo_path)
                            <img src="{{ asset('uploads/' . $cook->user->profile_photo_path) }}" alt="{{ $cook->user->name }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                        @else
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-orange-400 to-pink-600 rounded-full flex items-center justify-center text-xl text-white shadow-sm">
                                👨‍🍳
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Cocinando:</p>
                            <p class="font-semibold text-gray-800">{{ $cook->user->name }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-6">
                        @foreach($cart as $item)
                            <div class="flex items-center space-x-3 pb-3 border-b border-gray-100">
                                @if($item['photo_url'])
                                    <img src="{{ asset('uploads/' . $item['photo_url']) }}" alt="{{ $item['name'] }}"
                                        class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-orange-300 to-pink-400 rounded-lg flex items-center justify-center text-2xl">
                                        🍲
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-semibold text-sm">{{ $item['name'] }}</p>

                                    @if(!empty($item['options']))
                                        <div class="mt-0.5 space-y-0.5">
                                            @foreach($item['options'] as $option)
                                                <p class="text-[10px] text-gray-500 flex items-center">
                                                    <span class="mr-1 text-purple-400">•</span>
                                                    {{ $option['name'] }}
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif

                                    <p class="text-xs text-gray-600 mt-1">x{{ $item['quantity'] }}</p>
                                </div>
                                <span class="font-bold">${{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-3 border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-bold">${{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between" id="deliveryFeeRow" style="display: none;">
                            <span class="text-gray-600">Envío:</span>
                            <span class="font-bold text-purple-600">$500</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold border-t pt-3">
                            <span>Total:</span>
                            <span id="totalAmount"
                                class="bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">
                                ${{ number_format($subtotal, 0) }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4">
                        <p class="text-xs text-gray-700 text-center">
                            <span class="font-semibold">🔒 Pago Seguro:</span> Tus datos están protegidos con encriptación
                            SSL
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleDeliveryFields(show) {
                const fields = document.getElementById('deliveryFields');
                const feeRow = document.getElementById('deliveryFeeRow');
                const totalAmount = document.getElementById('totalAmount');

                if (show) {
                    fields.classList.remove('hidden');
                    feeRow.style.display = 'flex';
                    totalAmount.textContent = '${{ number_format($subtotal + 500, 0) }}';
                } else {
                    fields.classList.add('hidden');
                    feeRow.style.display = 'none';
                    totalAmount.textContent = '${{ number_format($subtotal, 0) }}';
                }
            }

            function getCurrentLocation() {
                const addressInput = document.getElementById('delivery_address');
                const originalPlaceholder = addressInput.placeholder;

                if (navigator.geolocation) {
                    addressInput.placeholder = "Detectando ubicación...";

                    navigator.geolocation.getCurrentPosition(position => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        document.getElementById('delivery_lat').value = lat.toFixed(4);
                        document.getElementById('delivery_lng').value = lng.toFixed(4);

                        // Reverse Geocoding with Nominatim (OpenStreetMap)
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.display_name) {
                                    addressInput.value = data.display_name;
                                }
                                addressInput.placeholder = originalPlaceholder;
                            })
                            .catch(error => {
                                console.error('Error in reverse geocoding:', error);
                                addressInput.placeholder = originalPlaceholder;
                            });
                    }, error => {
                        console.error('Geolocation error:', error);
                        addressInput.placeholder = originalPlaceholder;
                    });
                }
            }

            // Add event listener to pickup radio
            document.querySelector('input[value="pickup"]').addEventListener('click', () => toggleDeliveryFields(false));
        </script>
    @endpush

@endsection