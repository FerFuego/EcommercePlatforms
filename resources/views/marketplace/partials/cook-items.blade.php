@foreach($cooks as $cook)
    <div class="cook-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300"
        data-cook-id="{{ $cook->id }}" data-lat="{{ $cook->location_lat }}" data-lng="{{ $cook->location_lng }}">

        <!-- Cook Header -->
        <div class="bg-gradient-to-r from-orange-400 to-pink-500 p-6 text-white relative">
            <!-- Favorite Heart Icon -->
            @auth
                @if(auth()->user()->isCustomer())
                    <button onclick="toggleFavorite(event, {{ $cook->id }})" id="fav-btn-{{ $cook->id }}"
                        class="absolute top-4 right-4 p-2 rounded-full bg-white/20 hover:bg-white/40 transition-all backdrop-blur-sm z-10 group">
                        <svg id="heart-icon-{{ $cook->id }}"
                            class="w-6 h-6 transition-colors {{ auth()->user()->isFavorite($cook->id) ? 'text-red-500 fill-current' : 'text-white fill-none group-hover:text-red-200' }}"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                        </svg>
                    </button>
                @endif
            @endauth

            <div class="flex items-center space-x-4">
                @if($cook->user->profile_photo_path)
                    <img src="{{ asset('uploads/' . $cook->user->profile_photo_path) }}" alt="{{ $cook->user->name }}"
                        class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-lg">
                @else
                    <div
                        class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl font-bold text-orange-600 shadow-lg">
                        {{ strtoupper(substr($cook->user->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1">
                    <h3 class="font-bold text-xl">{{ $cook->user->name }}</h3>
                    @if($cook->rating_count > 0)
                        <div class="flex items-center mt-1">
                            <span class="text-yellow-300"><svg class="w-4 h-4 mb-1 text-yellow-300 fill-current"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 .587l3.668 7.568L24 9.748l-6 5.848L19.335 24 12 19.897 4.665 24 6 15.596 0 9.748l8.332-1.593z" />
                                </svg></span>
                            <span class="ml-1">{{ number_format($cook->rating_avg, 1) }}</span>
                            <span class="text-orange-100 text-sm ml-1">({{ $cook->rating_count }})</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cook Body -->
        <div class="p-6">
            <!-- Bio -->
            @if($cook->bio)
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $cook->bio }}</p>
            @endif

            <!-- Distance Badge (will be updated via JS) -->
            <div class="distance-badge mb-4 hidden">
                <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">
                            <span class="distance-value"></span> km
                        </span>
                    </div>
                    <div class="delivery-fee-badge text-xs font-bold px-3 py-1 rounded-full">
                        <!-- Will be filled by JS -->
                    </div>
                </div>
            </div>

            <!-- Dishes Preview -->
            @if($cook->dishes->count() > 0)
                <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Platos Destacados:</p>
                    <div class="space-y-2">
                        @foreach($cook->dishes->take(2) as $dish)
                            <div class="flex justify-between items-center text-sm bg-gray-50 rounded-lg p-2">
                                <span class="text-gray-700">{{ $dish->name }}</span>
                                <span class="font-bold text-orange-600">${{ number_format($dish->price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        @if($cook->dishes->count() > 2)
                            <p class="text-xs text-gray-500 text-center">
                                +{{ $cook->dishes->count() - 2 }} platos más
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col space-y-2">
                <a href="{{ route('marketplace.cook.profile', $cook) }}"
                    class="block w-full bg-gray-50 text-gray-700 px-4 py-3 rounded-xl font-semibold text-center hover:bg-gray-100 transition-all border-2 border-gray-100">
                    Ver Menú Completo
                </a>

                @guest
                    <button type="button" onclick="showLoginModal()"
                        class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white px-4 py-3 rounded-xl font-semibold text-center hover:shadow-lg transform hover:scale-105 transition-all">
                        Ordenar Ahora
                    </button>
                @else
                    <a href="{{ route('marketplace.cook.profile', $cook) }}"
                        class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white px-4 py-3 rounded-xl font-semibold text-center hover:shadow-lg transform hover:scale-105 transition-all">
                        Ordenar Ahora
                    </a>
                @endguest
            </div>
        </div>
    </div>
@endforeach