@foreach($cooks as $cook)
    @php
        $isPremium = $cook->hasFeature('premium_badge');
    @endphp
    <div class="cook-card bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg overflow-hidden hover:shadow-2xl transform hover:-translate-y-1.5 transition-all duration-300 {{ $isPremium ? 'ring-2 ring-yellow-400 ring-offset-2' : '' }}"
        data-cook-id="{{ $cook->id }}" data-lat="{{ $cook->location_lat }}" data-lng="{{ $cook->location_lng }}">

        <!-- Cook Header -->
        <div
            class="p-4 sm:p-6 text-white relative {{ $isPremium ? 'bg-gradient-to-r from-yellow-500 to-orange-500' : 'bg-gradient-to-r from-orange-400 to-pink-500' }}">
            <!-- Favorite Heart Icon -->
            @auth
                @if(auth()->user()->isCustomer())
                    <button onclick="toggleFavorite(event, {{ $cook->id }})" id="fav-btn-{{ $cook->id }}"
                        class="absolute top-3 right-3 sm:top-4 sm:right-4 p-1.5 sm:p-2 rounded-full bg-white/20 hover:bg-white/40 transition-all backdrop-blur-sm z-10 group">
                        <svg id="heart-icon-{{ $cook->id }}"
                            class="w-5 h-5 sm:w-6 sm:h-6 transition-colors {{ auth()->user()->isFavorite($cook->id) ? 'text-red-500 fill-current' : 'text-white fill-none group-hover:text-red-200' }}"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                        </svg>
                    </button>
                @endif
            @endauth

            @if($cook->hasFeature('premium_badge'))
                <div
                    class="absolute top-3 left-3 sm:top-4 sm:left-4 bg-yellow-400 text-yellow-900 text-[10px] sm:text-xs font-bold px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full shadow-md flex items-center z-10 transform -rotate-2">
                    <svg class="w-3 h-3 mr-1 fill-current" viewBox="0 0 20 20">
                        <path
                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                    </svg>
                    Premium
                </div>
            @endif

            <div class="flex items-center space-x-3 sm:space-x-4">
                @if($cook->user->profile_photo_path)
                    <img src="{{ asset('uploads/' . $cook->user->profile_photo_path) }}" alt="{{ $cook->user->name }}"
                        class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-white shadow-md">
                @else
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 bg-white rounded-full flex items-center justify-center text-xl sm:text-3xl font-bold text-orange-600 shadow-md">
                        {{ strtoupper(substr($cook->user->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-base sm:text-xl truncate">{{ $cook->user->name }}</h3>
                    @if($cook->rating_count > 0)
                        <div class="flex items-center mt-0.5 sm:mt-1">
                            <span class="text-yellow-300"><svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mb-0.5 text-yellow-300 fill-current"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 .587l3.668 7.568L24 9.748l-6 5.848L19.335 24 12 19.897 4.665 24 6 15.596 0 9.748l8.332-1.593z" />
                                </svg></span>
                            <span class="ml-1 text-xs sm:text-sm font-semibold">{{ number_format($cook->rating_avg, 1) }}</span>
                            <span class="text-orange-100 text-xs ml-1">({{ $cook->rating_count }})</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cook Body -->
        <div class="p-4 sm:p-6">
            <!-- Bio -->
            @if($cook->bio)
                <p class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4 line-clamp-2">{{ $cook->bio }}</p>
            @endif

            <!-- Distance Badge (will be updated via JS) -->
            <div class="distance-badge mb-3 sm:mb-4 hidden">
                <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-2.5 sm:p-3">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        <span class="text-xs sm:text-sm font-semibold text-gray-700">
                            <span class="distance-value"></span> km
                        </span>
                    </div>
                    <div class="delivery-fee-badge text-[11px] sm:text-xs font-bold px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full">
                        <!-- Will be filled by JS -->
                    </div>
                </div>
            </div>

            <!-- Dishes Preview -->
            @if($cook->dishes->count() > 0)
                <div class="mb-3 sm:mb-4">
                    <p class="text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">Platos Destacados:</p>
                    <div class="space-y-1.5 sm:space-y-2">
                        @foreach($cook->dishes->take(2) as $dish)
                            <div class="flex flex-col text-right">
                                <div class="flex justify-between items-center text-xs sm:text-sm bg-gray-50 rounded-lg p-2">
                                    <span class="text-gray-700 truncate pr-2">{{ $dish->name }}</span>
                                    <span class="font-bold text-orange-600 shrink-0">${{ number_format($dish->price, 0, ',', '.') }}</span>
                                </div>
                                @if($dish->isLowStock())
                                    <span class="text-[9px] font-bold text-red-600 animate-pulse mt-0.5 pr-1">
                                        🔥 ¡Solo quedan {{ $dish->available_stock }}!
                                    </span>
                                @endif
                            </div>
                        @endforeach
                        @if($cook->dishes->count() > 2)
                            <p class="text-[11px] sm:text-xs text-gray-500 text-center pt-0.5">
                                +{{ $cook->dishes->count() - 2 }} platos más
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col space-y-2">
                <a href="{{ route('marketplace.cook.profile', $cook) }}"
                    class="block w-full bg-gray-50 text-gray-700 px-4 py-2.5 sm:py-3 rounded-xl font-semibold text-center hover:bg-gray-100 text-xs sm:text-sm transition-all border border-gray-200">
                    Ver Menú Completo
                </a>

                @guest
                    <button type="button" onclick="showLoginModal()"
                        class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white px-4 py-2.5 sm:py-3 rounded-xl font-semibold text-center hover:shadow-lg transform active:scale-95 text-xs sm:text-sm transition-all">
                        Ordenar Ahora
                    </button>
                @else
                    <a href="{{ route('marketplace.cook.profile', $cook) }}"
                        class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white px-4 py-2.5 sm:py-3 rounded-xl font-semibold text-center hover:shadow-lg transform active:scale-95 text-xs sm:text-sm transition-all">
                        Ordenar Ahora
                    </a>
                @endguest
            </div>
        </div>
    </div>
@endforeach