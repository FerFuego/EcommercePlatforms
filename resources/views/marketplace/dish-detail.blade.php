@extends('layouts.app')

@section('title', $dish->name . ' — Cocinarte')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-5xl mx-auto">
            <!-- Back Button -->
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center text-gray-600 hover:text-purple-600 transition mb-6 group">
                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Left: Image -->
                <div>
                    @if($dish->photo_url)
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                            <img src="{{ asset('uploads/' . $dish->photo_url) }}" alt="{{ $dish->name }}"
                                class="w-full h-[400px] object-cover">
                            @if($dish->diet_tags && count($dish->diet_tags) > 0)
                                <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                                    @foreach($dish->diet_tags as $tag)
                                        <span
                                            class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-bold text-purple-700 shadow-sm">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            @if(!$dish->hasStock())
                                <div
                                    class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                    <span class="text-white text-2xl font-bold bg-red-600 px-6 py-3 rounded-xl">AGOTADO</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div
                            class="w-full h-[400px] bg-gradient-to-br from-orange-300 to-pink-400 rounded-3xl flex items-center justify-center shadow-2xl">
                            <span class="text-8xl">🍲</span>
                        </div>
                    @endif

                    <!-- Cook Mini-Profile -->
                    <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Cocinero</h3>
                        <div class="flex items-center space-x-4">
                            @if($dish->cook->user->profile_photo_path)
                                <img src="{{ asset('uploads/' . $dish->cook->user->profile_photo_path) }}"
                                    alt="{{ $dish->cook->user->name }}"
                                    class="w-16 h-16 rounded-full object-cover border-2 border-purple-100 shadow-md">
                            @else
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-orange-400 to-pink-600 rounded-full flex items-center justify-center text-3xl shadow-md">
                                    👨‍🍳
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="font-bold text-lg text-gray-800">{{ $dish->cook->user->name }}</h4>
                                <div class="flex items-center text-sm">
                                    <span class="text-yellow-500 font-bold">⭐
                                        {{ number_format($dish->cook->rating_avg, 1) }}</span>
                                    <span class="text-gray-400 ml-1">({{ $dish->cook->rating_count }} reviews)</span>
                                </div>
                                @if($dish->cook->user->address)
                                    <p class="text-sm text-gray-500 mt-1">📍 {{ $dish->cook->user->address }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-3 mt-4">
                            <a href="{{ route('marketplace.cook.profile', $dish->cook->id) }}"
                                class="flex-1 text-center text-purple-600 font-semibold border-2 border-purple-200 hover:bg-purple-50 px-4 py-2 rounded-xl transition text-sm">
                                Ver Perfil
                            </a>
                            @if($dish->cook->user->phone)
                                @php
                                    $whatsappService = app(\App\Services\WhatsAppService::class);
                                @endphp
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $dish->cook->user->phone) }}?text={{ urlencode('Hola ' . $dish->cook->user->name . '! Te escribo desde Cocinarte por tu plato: ' . $dish->name) }}"
                                    target="_blank" rel="noopener"
                                    class="flex-1 text-center text-green-700 font-semibold bg-green-50 border-2 border-green-200 hover:bg-green-100 px-4 py-2 rounded-xl transition text-sm flex items-center justify-center space-x-1">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                    </svg>
                                    <span>Consultar</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Details & Add to Cart -->
                <div>
                    <!-- Title & Price -->
                    <div class="mb-6">
                        <h1 class="text-4xl font-bold text-gray-800 mb-3">{{ $dish->name }}</h1>
                        <div class="flex items-center space-x-4 mb-4">
                            <span
                                class="text-4xl font-black bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">
                                ${{ number_format($dish->price, 0, ',', '.') }}
                            </span>
                            @if($dish->hasStock())
                                <span
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                                    ✓ Disponible ({{ $dish->available_stock }} restantes)
                                </span>
                            @else
                                <span
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-bold">
                                    Agotado
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    @if($dish->description)
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-700 mb-2">📝 Descripción</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $dish->description }}</p>
                        </div>
                    @endif

                    <!-- Info badges -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        @if($dish->preparation_time_minutes)
                            <div
                                class="flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                <span class="mr-2">⏱️</span>
                                {{ $dish->preparation_time_minutes }} min
                            </div>
                        @endif
                        @if($dish->delivery_method)
                            <div
                                class="flex items-center bg-purple-50 text-purple-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                <span class="mr-2">{{ $dish->delivery_method === 'delivery' ? '🛵' : ($dish->delivery_method === 'pickup' ? '🏃' : '🛵🏃') }}</span>
                                {{ $dish->delivery_method === 'delivery' ? 'Delivery' : ($dish->delivery_method === 'pickup' ? 'Retiro' : 'Delivery/Retiro') }}
                            </div>
                        @endif
                        @if($dish->is_schedulable)
                            <div
                                class="flex items-center bg-green-50 text-green-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                <span class="mr-2">📅</span> Se puede programar
                            </div>
                        @else
                            <div
                                class="flex items-center bg-amber-50 text-amber-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                <span class="mr-2">⚡</span> Solo pedido inmediato
                            </div>
                        @endif
                        @if($dish->available_days && count($dish->available_days) > 0 && count($dish->available_days) < 7)
                            @php
                                $dayNames = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
                                $availableDayNames = collect($dish->available_days)->map(fn($d) => $dayNames[$d] ?? $d)->implode(', ');
                            @endphp
                            <div
                                class="flex items-center bg-orange-50 text-orange-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                <span class="mr-2">📆</span> {{ $availableDayNames }}
                            </div>
                        @endif
                    </div>

                    <!-- Add to Cart Form -->
                    @if($dish->hasStock())
                        <form action="{{ route('cart.add', $dish->id) }}" method="POST"
                            class="bg-gradient-to-br from-gray-50 to-purple-50 rounded-2xl p-6 border-2 border-purple-100 shadow-sm">
                            @csrf
                            <input type="hidden" name="dish_id" value="{{ $dish->id }}">

                            <!-- Options -->
                            @if($dish->optionGroups->count() > 0)
                                <div class="mb-6 space-y-4">
                                    <h3 class="font-bold text-gray-700">🎛️ Opciones</h3>
                                    @foreach($dish->optionGroups as $group)
                                        <div class="bg-white rounded-xl p-4 shadow-sm">
                                            <p class="font-semibold text-gray-700 mb-3">
                                                {{ $group->name }}
                                                @if($group->is_required)
                                                    <span class="text-red-500 text-sm">*</span>
                                                @else
                                                    <span class="text-gray-400 text-xs">(Opcional)</span>
                                                @endif
                                            </p>
                                            <div class="space-y-2">
                                                @foreach($group->options as $option)
                                                    <label
                                                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-purple-50 transition">
                                                        <div class="flex items-center">
                                                            @if($group->max_selections === 1)
                                                                <input type="radio"
                                                                    name="options[{{ $group->id }}]"
                                                                    value="{{ $option->id }}"
                                                                    {{ $group->is_required && $loop->first ? 'checked' : '' }}
                                                                    class="w-4 h-4 text-purple-600 mr-3">
                                                            @else
                                                                <input type="checkbox"
                                                                    name="options[{{ $group->id }}][]"
                                                                    value="{{ $option->id }}"
                                                                    class="w-4 h-4 text-purple-600 rounded mr-3">
                                                            @endif
                                                            <span
                                                                class="font-medium text-gray-700">{{ $option->name }}</span>
                                                        </div>
                                                        @if($option->price > 0)
                                                            <span
                                                                class="text-purple-600 font-bold text-sm">+${{ number_format($option->price, 0) }}</span>
                                                        @else
                                                            <span class="text-green-600 text-xs font-semibold">Incluido</span>
                                                        @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Quantity -->
                            <div class="flex items-center justify-between mb-6">
                                <span class="font-bold text-gray-700">Cantidad</span>
                                <div class="flex items-center space-x-3">
                                    <button type="button" onclick="changeQty(-1)"
                                        class="w-10 h-10 bg-white border-2 border-gray-200 rounded-xl flex items-center justify-center text-lg font-bold text-gray-600 hover:bg-gray-100 transition">
                                        −
                                    </button>
                                    <input type="number" name="quantity" id="qty" value="1" min="1"
                                        max="{{ $dish->available_stock }}"
                                        class="w-16 text-center text-xl font-bold border-2 border-gray-200 rounded-xl py-2 focus:border-purple-500"
                                        readonly>
                                    <button type="button" onclick="changeQty(1)"
                                        class="w-10 h-10 bg-white border-2 border-gray-200 rounded-xl flex items-center justify-center text-lg font-bold text-gray-600 hover:bg-gray-100 transition">
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all flex items-center justify-center space-x-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                                </svg>
                                <span>Agregar al Carrito</span>
                            </button>
                        </form>
                    @else
                        <div
                            class="bg-gray-100 rounded-2xl p-8 text-center border-2 border-gray-200">
                            <p class="text-2xl mb-2">😔</p>
                            <p class="text-gray-600 font-semibold">Este plato no está disponible en este momento</p>
                            <a href="{{ route('marketplace.cook.profile', $dish->cook->id) }}"
                                class="inline-block mt-4 text-purple-600 font-semibold hover:text-pink-600 transition">
                                Ver otros platos de {{ $dish->cook->user->name }} →
                            </a>
                        </div>
                    @endif

                    <!-- Recent Reviews -->
                    @if($dish->cook->reviews->count() > 0)
                        <div class="mt-8">
                            <h3 class="font-bold text-gray-700 mb-4">⭐ Reviews de {{ $dish->cook->user->name }}</h3>
                            <div class="space-y-3">
                                @foreach($dish->cook->reviews->take(3) as $review)
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-semibold text-sm text-gray-700">
                                                    {{ $review->customer->name ?? 'Cliente' }}
                                                </span>
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span
                                                            class="text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                                    @endfor
                                                </div>
                                            </div>
                                            <span
                                                class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-sm text-gray-600 italic">"{{ $review->comment }}"</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if($dish->cook->reviews->count() > 3)
                                <a href="{{ route('marketplace.cook.profile', $dish->cook->id) }}"
                                    class="block text-center text-purple-600 font-semibold mt-3 hover:text-pink-600 transition">
                                    Ver todas las reviews →
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function changeQty(delta) {
                const input = document.getElementById('qty');
                let val = parseInt(input.value) + delta;
                if (val < 1) val = 1;
                if (val > {{ $dish->available_stock }}) val = {{ $dish->available_stock }};
                input.value = val;
            }
        </script>
    @endpush
@endsection
