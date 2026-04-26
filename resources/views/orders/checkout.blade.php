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

                    <!-- Scheduling -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white mr-3">3</span>
                            ¿Cuándo lo quieres?
                        </h2>

                        @php
                            $hasNonSchedulable = collect($cart)->contains('is_schedulable', false);
                        @endphp

                        @if($hasNonSchedulable)
                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-xl">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <span class="text-amber-400 text-xl">⚠️</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-amber-700">
                                            Algunos platos en tu carrito <strong>no aceptan pedidos programados</strong>.
                                            Para programar este pedido, deberás retirar esos platos o elegir "Lo antes posible".
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="schedule_type" value="immediate" checked class="peer sr-only"
                                    onclick="toggleScheduleFields(false)">
                                <div
                                    class="bg-gray-50 peer-checked:bg-orange-50 border-2 border-gray-200 peer-checked:border-orange-500 rounded-2xl p-4 transition-all">
                                    <h3 class="font-bold text-center">Lo antes posible 🚀</h3>
                                </div>
                            </label>

                            <label class="relative cursor-pointer {{ $hasNonSchedulable ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <input type="radio" name="schedule_type" value="scheduled" class="peer sr-only"
                                    {{ $hasNonSchedulable ? 'disabled' : '' }}
                                    onclick="toggleScheduleFields(true)">
                                <div
                                    class="bg-gray-50 peer-checked:bg-purple-50 border-2 border-gray-200 peer-checked:border-purple-500 rounded-2xl p-4 transition-all">
                                    <h3 class="font-bold text-center">Programar Pedido 📅</h3>
                                </div>
                            </label>
                        </div>

                        <div id="scheduleFields" class="hidden animate-fade-in">
                            <div class="space-y-4">
                                <div class="bg-purple-50 rounded-xl p-4 border border-purple-100 flex items-center">
                                    <span class="text-xl mr-3">💡</span>
                                    <p class="text-sm text-purple-800">
                                        El cocinero acepta pedidos programados entre las
                                        <strong>{{ $cook->opening_time ? \Carbon\Carbon::parse($cook->opening_time)->format('H:i') : '08:00' }}</strong>
                                        y las
                                        <strong>{{ $cook->closing_time ? \Carbon\Carbon::parse($cook->closing_time)->format('H:i') : '22:00' }}</strong>.
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Selecciona Fecha y Hora
                                        *</label>
                                    <div class="relative">
                                        <input type="text" name="scheduled_time" id="scheduled_time"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition bg-white"
                                            placeholder="Click para elegir..." readonly>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-full flex items-center justify-center text-white mr-3">4</span>
                            Notas Adicionales (Opcional)
                        </h2>

                        <div class="mb-6">
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-pink-500 focus:ring focus:ring-pink-200 transition resize-none"
                                placeholder="Escribe aquí cualquier instrucción especial o preferencia para tu pedido...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- WhatsApp info banner -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-4 mb-6">
                            <div class="flex items-start space-x-3">
                                <span class="text-2xl flex-shrink-0">💬</span>
                                <div>
                                    <p class="font-semibold text-green-800">Se abrirá WhatsApp al confirmar</p>
                                    <p class="text-sm text-green-700 mt-1">
                                        Al confirmar tu pedido, se abrirá una conversación de WhatsApp con el cocinero para que coordinen el pago y la entrega directamente.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-green-500 via-green-600 to-emerald-600 text-white px-8 py-5 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all flex items-center justify-center space-x-3">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span>Confirmar y Contactar por WhatsApp</span>
                        </button>
                    </div>
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

                                    @if(isset($item['is_schedulable']) && !$item['is_schedulable'])
                                        <span class="inline-block px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded mt-1">
                                            🚫 No Programable
                                        </span>
                                    @endif

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

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4">
                        <p class="text-xs text-gray-700 text-center">
                            <span class="font-semibold">💬 WhatsApp:</span> Al confirmar, podrás coordinar el pago y entrega directamente con el cocinero
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

            // Scheduling logic
            function toggleScheduleFields(show) {
                const fields = document.getElementById('scheduleFields');
                const input = document.getElementById('scheduled_time');
                if (show) {
                    fields.classList.remove('hidden');
                    input.required = true;
                } else {
                    fields.classList.add('hidden');
                    input.required = false;
                    input.value = '';
                }
            }

            // Flatpickr initialization
            document.addEventListener('DOMContentLoaded', function () {
                flatpickr("#scheduled_time", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    minDate: "today",
                    time_24hr: true,
                    locale: "es",
                    disableMobile: "true",
                    minTime: "{{ $cook->opening_time ? \Carbon\Carbon::parse($cook->opening_time)->format('H:i') : '08:00' }}",
                    maxTime: "{{ $cook->closing_time ? \Carbon\Carbon::parse($cook->closing_time)->format('H:i') : '22:00' }}",
                });
            });
        </script>
    @endpush

@endsection