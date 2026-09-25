@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

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
                @php
                    $operatingStatus = $operatingStatus ?? $cook->getOperatingStatus();
                    $isScheduledOnly = !$operatingStatus['accepts_immediate'];
                @endphp

                @if($isScheduledOnly)
                    <div class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 text-white p-6 rounded-2xl shadow-xl border border-purple-400/30 flex items-start space-x-4 mb-6">
                        <div class="text-3xl p-3 bg-white/10 rounded-2xl flex-shrink-0">📅</div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $operatingStatus['status'] === 'closing_soon' ? 'bg-amber-400 text-amber-950 font-extrabold' : 'bg-rose-500 text-white' }}">
                                    {{ $operatingStatus['label'] }}
                                </span>
                                <h3 class="font-bold text-lg text-white">Este pedido se procesará como Pedido Programado</h3>
                            </div>
                            <p class="text-purple-100 text-sm leading-relaxed">
                                {{ $operatingStatus['reason'] }}
                            </p>
                            <p class="text-xs text-purple-200 mt-2 font-medium">
                                ✨ Por favor selecciona abajo en el <strong>Paso 3 ("¿Cuándo lo quieres?")</strong> la fecha y hora en la que deseas recibirlo.
                            </p>
                        </div>
                    </div>
                @endif

                <form id="orderForm" action="{{ route('orders.process') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

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
                            <label class="relative {{ $isScheduledOnly ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                <input type="radio" name="schedule_type" value="immediate" 
                                    {{ $isScheduledOnly ? 'disabled' : 'checked' }} 
                                    class="peer sr-only"
                                    onclick="toggleScheduleFields(false)">
                                <div
                                    class="bg-gray-50 peer-checked:bg-orange-50 border-2 border-gray-200 peer-checked:border-orange-500 rounded-2xl p-4 transition-all">
                                    <h3 class="font-bold text-center">Lo antes posible 🚀</h3>
                                    @if($isScheduledOnly)
                                        <p class="text-xs text-rose-600 text-center font-semibold mt-1">No disponible (cocina fuera de horario)</p>
                                    @endif
                                </div>
                            </label>

                            <label class="relative cursor-pointer {{ $hasNonSchedulable ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <input type="radio" name="schedule_type" value="scheduled" class="peer sr-only"
                                    {{ $isScheduledOnly ? 'checked' : '' }}
                                    {{ $hasNonSchedulable ? 'disabled' : '' }}
                                    onclick="toggleScheduleFields(true)">
                                <div
                                    class="bg-gray-50 peer-checked:bg-purple-50 border-2 border-gray-200 peer-checked:border-purple-500 rounded-2xl p-4 transition-all {{ $isScheduledOnly ? 'border-purple-500 bg-purple-50' : '' }}">
                                    <h3 class="font-bold text-center text-purple-900">Programar Pedido 📅</h3>
                                    @if($isScheduledOnly)
                                        <p class="text-xs text-purple-700 text-center font-bold mt-1">Requerido (cocina fuera de turno)</p>
                                    @endif
                                </div>
                            </label>
                        </div>

                        <div id="scheduleFields" class="{{ $isScheduledOnly ? '' : 'hidden' }} animate-fade-in">
                            <div class="space-y-4">
                                <div class="bg-purple-50 rounded-xl p-4 border border-purple-100 flex items-start">
                                    <span class="text-xl mr-3 flex-shrink-0">💡</span>
                                    <div class="text-sm text-purple-900">
                                        <p class="font-semibold">Horario de atención del cocinero:</p>
                                        <p>De <strong>{{ $cook->opening_time ? \Carbon\Carbon::parse($cook->opening_time)->format('H:i') : '08:00' }}</strong> a <strong>{{ $cook->closing_time ? \Carbon\Carbon::parse($cook->closing_time)->format('H:i') : '22:00' }} hs</strong>.</p>
                                        @if($operatingStatus['next_available_date'] === 'tomorrow')
                                            <p class="mt-1 text-purple-950 font-bold">⚠️ Entregas programadas disponibles a partir de <u>mañana</u>.</p>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Selecciona Fecha y Hora de Entrega/Retiro *</label>
                                    <div class="relative">
                                        <input type="text" name="scheduled_time" id="scheduled_time"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition bg-white"
                                            placeholder="Click para elegir fecha y hora..." readonly {{ $isScheduledOnly ? 'required' : '' }}>
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

                    <!-- Phone / WhatsApp -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white mr-3">4</span>
                            Tu Teléfono Celular (WhatsApp)
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Número Celular *
                                </label>
                                <input type="tel" name="phone" id="customer_phone"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition @error('phone') border-red-500 @enderror"
                                    placeholder="Ej: 11 2345 6789 o 351 234 5678"
                                    value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                                <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                    <span class="mr-1">💬</span> El cocinero y la plataforma usarán este número para coordinar y avisarte el estado de tu pedido por WhatsApp.
                                </p>
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-full flex items-center justify-center text-white mr-3">5</span>
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

                        <button type="submit" id="submitOrderBtn"
                            class="w-full bg-gradient-to-r from-green-500 via-green-600 to-emerald-600 text-white px-8 py-5 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all flex items-center justify-center space-x-3 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none">
                            <span id="btnDefaultState" class="flex items-center justify-center space-x-3">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                <span>Confirmar y Contactar por WhatsApp</span>
                            </span>
                            <span id="btnLoadingState" class="hidden items-center justify-center space-x-3">
                                <svg class="animate-spin -ml-1 mr-3 h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Procesando pedido...</span>
                            </span>
                        </button>

                        @if($isScheduledOnly)
                            <div class="mt-3 text-center">
                                <span class="inline-flex items-center text-xs font-bold text-purple-800 bg-purple-50 px-3 py-1.5 rounded-full border border-purple-200">
                                    📅 Pedido Programado: El cocinero preparará tu orden para la fecha y horario elegido
                                </span>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-2xl p-6 sticky top-24">
                    <h3 class="text-2xl font-bold mb-6">Resumen del Pedido</h3>

                    <!-- Cook Info Mini -->
                    <div class="flex items-center space-x-3 mb-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
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

                    @if($isScheduledOnly)
                        <div class="mb-5 p-3 bg-purple-50 border border-purple-200 rounded-xl text-xs text-purple-900 flex items-start space-x-2">
                            <span class="text-base flex-shrink-0">📅</span>
                            <div>
                                <span class="font-bold block">Pedido Programado</span>
                                <span class="text-[11px] text-purple-700 leading-tight block">Cocina fuera de turno inmediato. Tu pedido se preparará fresco para la fecha seleccionada.</span>
                            </div>
                        </div>
                    @endif

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
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
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
            const isScheduledOnly = {{ $isScheduledOnly ? 'true' : 'false' }};

            function toggleScheduleFields(show) {
                if (isScheduledOnly && !show) {
                    return; // Bloqueado: solo pedidos programados
                }

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
                const isNextDateTomorrow = {{ $operatingStatus['next_available_date'] === 'tomorrow' ? 'true' : 'false' }};
                
                let minDateValue = "today";
                if (isNextDateTomorrow) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    minDateValue = tomorrow;
                }

                flatpickr("#scheduled_time", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    minDate: minDateValue,
                    time_24hr: true,
                    locale: "es",
                    disableMobile: "true",
                    minTime: "{{ $cook->opening_time ? \Carbon\Carbon::parse($cook->opening_time)->format('H:i') : '08:00' }}",
                    maxTime: "{{ $cook->closing_time ? \Carbon\Carbon::parse($cook->closing_time)->format('H:i') : '22:00' }}",
                });

                if (isScheduledOnly) {
                    const input = document.getElementById('scheduled_time');
                    if (input) input.required = true;
                }
            });

            // Control del loader y prevención de doble submit
            let isSubmitting = false;

            function showCheckoutLoader() {
                const overlay = document.getElementById('checkoutLoadingOverlay');
                const btn = document.getElementById('submitOrderBtn');
                const btnDefault = document.getElementById('btnDefaultState');
                const btnLoading = document.getElementById('btnLoadingState');

                if (overlay) {
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                }
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                }
                if (btnDefault) btnDefault.classList.add('hidden');
                if (btnLoading) {
                    btnLoading.classList.remove('hidden');
                    btnLoading.classList.add('flex');
                }
            }

            function hideCheckoutLoader() {
                const overlay = document.getElementById('checkoutLoadingOverlay');
                const btn = document.getElementById('submitOrderBtn');
                const btnDefault = document.getElementById('btnDefaultState');
                const btnLoading = document.getElementById('btnLoadingState');

                if (overlay) {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                }
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
                if (btnDefault) btnDefault.classList.remove('hidden');
                if (btnLoading) {
                    btnLoading.classList.add('hidden');
                    btnLoading.classList.remove('flex');
                }
                isSubmitting = false;
            }

            // reCAPTCHA handler y submit seguro
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                const form = this;

                // 1. Validar requeridos estándar de HTML5
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // 2. Validaciones condicionales de entrega
                const deliveryRadio = document.querySelector('input[name="delivery_type"]:checked');
                if (deliveryRadio && deliveryRadio.value === 'delivery') {
                    const address = document.getElementById('delivery_address');
                    if (!address || !address.value.trim()) {
                        e.preventDefault();
                        alert('Por favor ingresa la dirección completa de entrega.');
                        if (address) address.focus();
                        return;
                    }
                }

                // 3. Validar teléfono de contacto
                const phoneInput = document.getElementById('customer_phone');
                if (phoneInput && !phoneInput.value.trim()) {
                    e.preventDefault();
                    alert('Por favor ingresa tu número de teléfono celular para recibir notificaciones por WhatsApp.');
                    phoneInput.focus();
                    return;
                }

                // 4. Validaciones condicionales de pedido programado
                const scheduleRadio = document.querySelector('input[name="schedule_type"]:checked');
                if (scheduleRadio && scheduleRadio.value === 'scheduled') {
                    const scheduledTime = document.getElementById('scheduled_time');
                    if (!scheduledTime || !scheduledTime.value.trim()) {
                        e.preventDefault();
                        alert('Por favor selecciona la fecha y hora para tu pedido programado.');
                        if (scheduledTime) scheduledTime.focus();
                        return;
                    }
                }

                e.preventDefault();
                isSubmitting = true;
                showCheckoutLoader();

                // Timeout de seguridad: si después de 25 segundos no hubo respuesta, liberar la interfaz
                const safetyTimeout = setTimeout(function() {
                    hideCheckoutLoader();
                }, 25000);

                if (typeof window.getRecaptchaToken === 'function') {
                    window.getRecaptchaToken('order_process').then(token => {
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    }).catch(err => {
                        console.error('reCAPTCHA error:', err);
                        form.submit();
                    });
                } else {
                    form.submit();
                }
            });
        </script>
    @endpush

    @push('styles')
        <style>
            @keyframes indeterminate {
                0% { transform: translateX(-100%); width: 45%; }
                50% { transform: translateX(60%); width: 75%; }
                100% { transform: translateX(220%); width: 45%; }
            }
            .animate-indeterminate {
                animation: indeterminate 1.6s infinite cubic-bezier(0.65, 0.815, 0.735, 0.395);
            }
        </style>
    @endpush

    <!-- Fullscreen Processing Overlay Loader -->
    <div id="checkoutLoadingOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/65 backdrop-blur-md hidden transition-all duration-300">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-100 text-center relative overflow-hidden animate-fade-in">
            <!-- Barra decorativa superior con gradiente de marca -->
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-orange-500 via-pink-500 to-green-500"></div>

            <!-- Contenedor del ícono animado -->
            <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-30"></div>
                <div class="relative w-20 h-20 bg-gradient-to-tr from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-4xl shadow-xl shadow-green-500/30">
                    <span class="animate-bounce">🍳</span>
                </div>
                <!-- Anillo de progreso circular giratorio -->
                <svg class="absolute inset-0 w-24 h-24 animate-spin text-green-500/40" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="70 200" stroke-linecap="round"></circle>
                </svg>
            </div>

            <h3 class="text-2xl font-black text-gray-900 mb-2">¡Procesando tu pedido!</h3>
            <p class="text-gray-600 text-sm mb-6 leading-relaxed">
                Estamos registrando tus platos y notificando a la cocina. Esto demorará sólo unos segundos...
            </p>

            <!-- Barra de progreso indeterminada animada -->
            <div class="w-full bg-gray-100 rounded-full h-2 mb-4 overflow-hidden relative">
                <div class="h-full bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 rounded-full animate-indeterminate"></div>
            </div>

            <!-- Mensaje de seguridad y paciencia -->
            <div class="flex items-center justify-center space-x-2 text-xs text-gray-500 bg-gray-50 py-2.5 px-4 rounded-xl">
                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Por favor no cierres ni toques el botón otra vez</span>
            </div>
        </div>
    </div>

@endsection