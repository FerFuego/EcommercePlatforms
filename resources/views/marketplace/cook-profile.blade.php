@extends('layouts.app')

@section('title', $cook->user->name . ' - Cocinero')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <!-- Cover Header -->
        <div
            class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 rounded-2xl p-12 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full filter blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full filter blur-3xl"></div>
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-center gap-6 md:gap-8">
                @if($cook->user->profile_photo_path)
                    <img src="{{ asset('storage/' . $cook->user->profile_photo_path) }}" alt="{{ $cook->user->name }}"
                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-2xl">
                @else
                    <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center text-6xl shadow-2xl">
                        👨‍🍳
                    </div>
                @endif
                <div class="text-white">
                    <h1 class="text-4xl font-bold mb-2">{{ $cook->user->name }}</h1>
                    <div class="flex items-center space-x-4 mb-3">
                        <div class="flex items-center">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < floor($cook->rating_avg))
                                    <svg class="w-6 h-6 text-yellow-300 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-white/50 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @endif
                            @endfor
                            <span class="ml-2 text-xl font-bold">{{ number_format($cook->rating_avg, 1) }}</span>
                            <span class="ml-1 text-white/80">({{ $cook->rating_count }} reviews)</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6 text-white/90">
                        <span>📍 {{ $cook->coverage_radius_km }} km de cobertura</span>
                        <span>🍽️ {{ $cook->dishes->count() }} platos</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Bio -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2
                        class="text-2xl font-bold mb-4 bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">
                        Sobre {{ $cook->user->name }}
                    </h2>
                    <p class="text-gray-700 leading-relaxed">{{ $cook->bio }}</p>
                </div>

                <!-- Kitchen Photos -->
                @if($cook->kitchen_photos && count($cook->kitchen_photos) > 0)
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6">Mi Cocina</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="gallery">
                            @foreach($cook->kitchen_photos as $index => $photo)
                                <div class="relative group cursor-pointer overflow-hidden rounded-xl"
                                    onclick="openLightbox({{ $index }})">
                                    <img src="{{ Storage::url($photo) }}" alt="Cocina"
                                        class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all duration-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Lightbox Modal -->
                    <div id="lightbox"
                        class="fixed inset-0 z-50 hidden bg-black bg-opacity-90 flex items-center justify-center opacity-0 transition-opacity duration-300">
                        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>

                        <button onclick="prevImage()" class="absolute left-4 text-white hover:text-gray-300 z-50 p-2">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>

                        <img id="lightbox-img" src=""
                            class="max-h-[90vh] max-w-[90vw] object-contain transform scale-95 transition-transform duration-300">

                        <button onclick="nextImage()" class="absolute right-4 text-white hover:text-gray-300 z-50 p-2">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <script>
                        const photos = @json(array_map(fn($p) => Storage::url($p), $cook->kitchen_photos));
                        let currentIndex = 0;
                        const lightbox = document.getElementById('lightbox');
                        const lightboxImg = document.getElementById('lightbox-img');

                        function openLightbox(index) {
                            currentIndex = index;
                            updateLightboxImage();
                            lightbox.classList.remove('hidden');
                            // Small delay to allow display:block to apply before opacity transition
                            setTimeout(() => {
                                lightbox.classList.remove('opacity-0');
                                lightboxImg.classList.remove('scale-95');
                                lightboxImg.classList.add('scale-100');
                            }, 10);
                            document.body.style.overflow = 'hidden';
                        }

                        function closeLightbox() {
                            lightbox.classList.add('opacity-0');
                            lightboxImg.classList.remove('scale-100');
                            lightboxImg.classList.add('scale-95');
                            setTimeout(() => {
                                lightbox.classList.add('hidden');
                                document.body.style.overflow = 'auto';
                            }, 300);
                        }

                        function updateLightboxImage() {
                            lightboxImg.src = photos[currentIndex];
                        }

                        function nextImage() {
                            currentIndex = (currentIndex + 1) % photos.length;
                            updateLightboxImage();
                        }

                        function prevImage() {
                            currentIndex = (currentIndex - 1 + photos.length) % photos.length;
                            updateLightboxImage();
                        }

                        // Close on background click
                        lightbox.addEventListener('click', (e) => {
                            if (e.target === lightbox) closeLightbox();
                        });

                        // Keyboard navigation
                        document.addEventListener('keydown', (e) => {
                            if (!lightbox.classList.contains('hidden')) {
                                if (e.key === 'Escape') closeLightbox();
                                if (e.key === 'ArrowRight') nextImage();
                                if (e.key === 'ArrowLeft') prevImage();
                            }
                        });
                    </script>
                @endif

                <!-- Menu -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">
                        Menú Disponible
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($cook->dishes as $dish)
                            <div
                                class="group bg-gradient-to-br from-gray-50 to-pink-50 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all">
                                @if($dish->photo_url)
                                    <img src="{{ Storage::url($dish->photo_url) }}" alt="{{ $dish->name }}"
                                        class="w-full h-48 object-cover group-hover:scale-110 transition-transform">
                                @else
                                    <div
                                        class="w-full h-48 bg-gradient-to-br from-orange-300 to-pink-400 flex items-center justify-center text-6xl">
                                        🍲
                                    </div>
                                @endif

                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $dish->name }}</h3>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $dish->description }}</p>

                                    <div class="flex items-center justify-between mb-4">
                                        <span
                                            class="text-2xl font-bold text-pink-600">${{ number_format($dish->price, 0) }}</span>
                                        <span class="text-sm text-gray-500">
                                            @if($dish->available_stock > 0)
                                                ✅ {{ $dish->available_stock }} disponibles
                                            @else
                                                ❌ Agotado
                                            @endif
                                        </span>
                                    </div>

                                    @if($dish->diet_tags && count($dish->diet_tags) > 0)
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($dish->diet_tags as $tag)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">
                                                    {{ ucfirst($tag) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($dish->available_stock > 0 && $dish->is_active)
                                        <form action="{{ route('cart.add', $dish->id) }}" method="POST">
                                            @csrf
                                            <div class="flex items-center space-x-2">
                                                <input type="number" name="quantity" value="1" min="1"
                                                    max="{{ $dish->available_stock }}"
                                                    class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-purple-500">
                                                <button type="submit"
                                                    class="flex-1 bg-gradient-to-r from-orange-500 to-pink-600 text-white px-4 py-2 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                                                    Ordenar
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-12">
                                <div class="text-6xl mb-4">🍽️</div>
                                <p class="text-gray-500">Aún no hay platos disponibles</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Reviews -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Reseñas</h2>

                    <div class="space-y-6">
                        @forelse($cook->reviews->take(5) as $review)
                            <div class="border-b border-gray-100 pb-6 last:border-0">
                                <div class="flex items-start space-x-4">
                                    @if($review->customer->profile_photo_path)
                                        <img src="{{ asset('storage/' . $review->customer->profile_photo_path) }}"
                                            alt="{{ $review->customer->name }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-purple-100">
                                    @else
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ substr($review->customer->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-gray-800">{{ $review->customer->name }}</h4>
                                            <div class="flex items-center">
                                                @for($i = 0; $i < $review->rating; $i++)
                                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
                                        <p class="text-xs text-gray-400 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="text-5xl mb-3">⭐</div>
                                <p class="text-gray-500">Aún no hay reseñas</p>
                            </div>
                        @endforelse
                    </div>

                    @if($cook->reviews->count() > 5)
                        <a href="{{ route('reviews.cook', $cook->id) }}"
                            class="block text-center mt-6 text-purple-600 font-semibold hover:text-pink-600 transition">
                            Ver Todas las Reseñas →
                        </a>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-24">
                    <h3 class="text-2xl font-bold mb-6">Información</h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $cook->user->email }}
                        </div>
                        @if($cook->user->phone)
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $cook->user->phone }}
                            </div>
                        @endif
                        <div class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $cook->user->address }}
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl p-4 mb-6">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">⏱️ Tiempo de preparación:</span>
                            Típicamente {{ $cook->dishes->avg('preparation_time_minutes') ?? 30 }} minutos
                        </p>
                    </div>

                    <a href="{{ route('marketplace.catalog') }}"
                        class="block w-full bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-xl font-semibold text-center shadow-lg hover:shadow-xl transition-all">
                        ← Volver al Catálogo
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection