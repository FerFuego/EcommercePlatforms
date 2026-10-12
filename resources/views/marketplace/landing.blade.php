@extends('layouts.app')

@section('title', 'Cocinarte | La comida casera de personas como vos')
@section('meta_description', 'Descubrí cocineros independientes de tu zona y pedí sus platos caseros recién hechos. Comida real hecha en casa con entrega o retiro.')
@section('canonical', route('home'))
@section('og_title', 'Cocinarte — La comida casera de personas como vos')
@section('og_image', asset('assets/home/hero_cook.jpg'))

@push('styles')
    @vite(['resources/css/pages/landing.css'])
@endpush

@section('content')

    @php
        // Cocineros destacados de la maqueta (con soporte para incorporar cocineros reales de la BD)
        $featuredCooks = [
            [
                'id' => 1,
                'name' => 'Doña Rosa',
                'specialty' => 'Pastas Frescas',
                'location' => 'Córdoba Capital · 3 km',
                'rating' => '4.9',
                'reviews_count' => 124,
                'photo' => asset('assets/home/cook_dona_rosa.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_lasagna.jpg'),
                    asset('assets/home/dish_mini_gnocchi.jpg'),
                    asset('assets/home/dish_mini_roast.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'pastas'])
            ],
            [
                'id' => 2,
                'name' => 'Leo Esquivel',
                'specialty' => 'Comida Italiana',
                'location' => 'Villa Allende · 7 km',
                'rating' => '4.8',
                'reviews_count' => 96,
                'photo' => asset('assets/home/cook_leo.jpg'),
                'dishes' => [
                    asset('assets/front/pasta_casera.png'),
                    asset('assets/home/dish_mini_lasagna.jpg'),
                    asset('assets/home/dish_mini_gnocchi.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'italiana'])
            ],
            [
                'id' => 3,
                'name' => 'Mariana López',
                'specialty' => 'Cocina Tradicional',
                'location' => 'Córdoba Capital · 4 km',
                'rating' => '4.9',
                'reviews_count' => 87,
                'photo' => asset('assets/home/cook_mariana.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_gnocchi.jpg'),
                    asset('assets/home/dish_mini_roast.jpg'),
                    asset('assets/front/pollo_curry_casero.png'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'tradicional'])
            ],
            [
                'id' => 4,
                'name' => 'Fernando Catalano',
                'specialty' => 'Cocina de Autor',
                'location' => 'Bell Ville · 6 km',
                'rating' => '4.8',
                'reviews_count' => 72,
                'photo' => asset('assets/home/cook_fernando.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_roast.jpg'),
                    asset('assets/front/ensalada_casera.png'),
                    asset('assets/front/torta_casera.png'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'autor'])
            ],
            [
                'id' => 5,
                'name' => 'Paula Nieva',
                'specialty' => 'Empanadas y Tartas Caseras',
                'location' => 'Bell Ville · 3 km',
                'rating' => '4.6',
                'reviews_count' => 64,
                'photo' => asset('assets/home/cook_paula.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_empanadas.jpg'),
                    asset('assets/home/dish_mini_milanesa.jpg'),
                    asset('assets/home/dish_mini_tart.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'empanadas'])
            ],
            [
                'id' => 6,
                'name' => 'Soledad Pérez',
                'specialty' => 'Guisos y Postres Caseros',
                'location' => 'Bell Ville · 2 km',
                'rating' => '4.7',
                'reviews_count' => 58,
                'photo' => asset('assets/home/cook_soledad.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_stew.jpg'),
                    asset('assets/home/dish_mini_flan.jpg'),
                    asset('assets/home/dish_mini_roast.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'guisos'])
            ],
        ];

        // Si existen cocineros reales aprobados en la BD, enriquecemos la lista
        if (isset($dbCooks) && $dbCooks->isNotEmpty()) {
            $realList = [];
            foreach ($dbCooks as $realCook) {
                $dishPhotos = [];
                foreach ($realCook->dishes as $d) {
                    if (!empty($d->image_path)) {
                        $dishPhotos[] = asset('storage/' . $d->image_path);
                    }
                }
                while (count($dishPhotos) < 3) {
                    $dishPhotos[] = asset('assets/home/dish_mini_lasagna.jpg');
                }

                $cookPhoto = asset('assets/home/cook_mariana.jpg');
                if (!empty($realCook->profile_photo_path)) {
                    $cookPhoto = asset('storage/' . $realCook->profile_photo_path);
                } elseif (!empty($realCook->user->profile_photo_path)) {
                    $cookPhoto = asset('uploads/' . $realCook->user->profile_photo_path);
                }

                $realList[] = [
                    'id' => $realCook->id,
                    'name' => $realCook->user->name ?? 'Cocinero Cocinarte',
                    'specialty' => $realCook->bio_specialty ?? 'Cocina Casera',
                    'location' => ($realCook->city ?? 'Tu zona') . ' · ' . ($realCook->neighborhood ?? 'Cerca tuyo'),
                    'rating' => number_format($realCook->reviews_avg_rating ?? 4.9, 1),
                    'reviews_count' => $realCook->reviews_count ?? 15,
                    'photo' => $cookPhoto,
                    'dishes' => array_slice($dishPhotos, 0, 3),
                    'url' => route('marketplace.cook.profile', $realCook->id)
                ];
            }
            if (!empty($realList)) {
                $featuredCooks = array_merge($realList, $featuredCooks);
            }
        }

        // Testimonios de comensales para el slider
        $testimonials = [
            [
                'name' => 'Laura G.',
                'location' => 'Córdoba Capital',
                'text' => 'La comida de Doña Rosa es espectacular. Todo casero, riquísimo y con mucho amor. Ya pedí varias veces y siempre es un placer.',
                'stars' => 5,
                'avatar' => asset('assets/home/cook_mariana.jpg')
            ],
            [
                'name' => 'Martín P.',
                'location' => 'Villa Allende',
                'text' => 'Increíble la calidad de las pastas de Leo. Se nota la masa fresca de verdad. Llegó caliente y en horario perfecto.',
                'stars' => 5,
                'avatar' => asset('assets/home/cook_leo.jpg')
            ],
            [
                'name' => 'Sofía M.',
                'location' => 'Córdoba Capital',
                'text' => 'Cocinamos poco por el trabajo y Cocinarte nos salvó las cenas. Comida sana, como la que hacía mi abuela.',
                'stars' => 5,
                'avatar' => asset('assets/home/cook_dona_rosa.jpg')
            ],
            [
                'name' => 'Gonzalo R.',
                'location' => 'Bell Ville',
                'text' => 'Pedir platos caseros recién hechos me cambió la semana. Los sabores son auténticos y la calidad es excelente.',
                'stars' => 5,
                'avatar' => asset('assets/home/cook_fernando.jpg')
            ],
        ];
    @endphp

    <div class="bg-[#faf8f5] text-gray-700 antialiased overflow-hidden">

        <!-- ========================================== -->
        <!-- 1. HERO SECTION (FULLWIDTH & RESPONSIVE)    -->
        <!-- ========================================== -->

        <!-- VERSIÓN DESKTOP / TABLET (Fullwidth con degradé horizontal hacia la imagen) -->
        <section
            class="hidden md:flex relative w-full overflow-hidden bg-[#faf8f5] min-h-[500px] lg:min-h-[560px] xl:min-h-[600px] flex items-center border-b border-orange-100/30">
            <!-- Imagen Fullwidth a la derecha -->
            <div class="absolute inset-y-0 right-0 w-full md:w-[68%] lg:w-[64%] xl:w-[60%] h-full z-0">
                <img src="{{ asset('assets/home/hero_cook.jpg') }}" alt="Cocinera preparando comida casera en Cocinarte"
                    class="w-full h-full object-cover object-[center_right] lg:object-center" fetchpriority="high">
                <!-- Fusión con degradé horizontal hacia la izquierda -->
                <div
                    class="absolute inset-y-0 left-0 w-44 lg:w-72 bg-gradient-to-r from-[#faf8f5] via-[#faf8f5]/85 to-transparent pointer-events-none">
                </div>
            </div>

            <!-- Overlay de fondo para asegurar legibilidad perfecta de los textos a la izquierda -->
            <div
                class="absolute inset-y-0 left-0 w-full md:w-[58%] lg:w-[52%] xl:w-[48%] bg-gradient-to-r from-[#faf8f5] via-[#faf8f5] to-transparent z-10 pointer-events-none">
            </div>

            <!-- Contenido textual alineado a la izquierda dentro del contenedor -->
            <div class="container flex items-center mx-auto px-4 sm:px-6 lg:px-8 relative z-20 py-12 lg:py-16">
                <div class="max-w-xl space-y-6 lg:space-y-7">
                    <h1
                        class="text-4xl lg:text-5xl xl:text-[3.4rem] font-black tracking-tight text-gray-700 leading-[1.12]">
                        <span class="text-gradient block">
                            La comida casera
                        </span>
                        <span class="text-gray-700 block mt-1">
                            de personas como vos.
                        </span>
                    </h1>

                    <p class="text-base lg:text-lg text-gray-600 leading-relaxed font-normal max-w-md">
                        Descubrí cocineros independientes de tu zona y pedí sus platos hechos en casa.
                    </p>

                    <!-- Botones Principales -->
                    <div class="flex items-center gap-4 pt-1">
                        <a href="{{ route('marketplace.catalog') }}"
                            class="group btn-gradient text-white px-7 py-3.5 rounded-full font-bold text-sm lg:text-base shadow-md glow-effect hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 inline-flex items-center justify-center whitespace-nowrap">
                            <span>Explorar cocineros</span>
                            <svg class="w-4 h-4 ml-2.5 transition-transform duration-200 group-hover:translate-x-1"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>

                        <a href="{{ route('register', ['role' => 'cook']) }}"
                            class="px-7 py-3.5 rounded-full font-bold text-sm lg:text-base bg-white hover:bg-rose-50/50 text-purple-950 border border-purple-300 shadow-sm hover:shadow transition-all duration-200 inline-flex items-center justify-center whitespace-nowrap">
                            Quiero cocinar
                        </a>
                    </div>

                    <!-- Micro-Badges de Confianza con Íconos -->
                    <div class="pt-2 flex items-center space-x-3 text-xs lg:text-sm font-medium text-gray-600">
                        <div class="inline-flex items-center space-x-1.5">
                            <span class="text-rose-500 font-bold">♡</span>
                            <span>Cocineros reales</span>
                        </div>
                        <span class="text-gray-300 font-bold">·</span>
                        <div class="inline-flex items-center space-x-1.5">
                            <span>Cocinas reales</span>
                        </div>
                        <span class="text-gray-300 font-bold">·</span>
                        <div class="inline-flex items-center space-x-1.5">
                            <span>Comida hecha en casa</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- VERSIÓN MOBILE (Títulos y botones más chicos en una sola línea arriba, y debajo degradé a la imagen) -->
        <section class="md:hidden relative w-full bg-[#faf8f5] pt-6 pb-2 overflow-hidden">
            <!-- Bloque superior de contenidos -->
            <div class="px-5 space-y-4 text-left">
                <h1 class="text-3xl font-black tracking-tight leading-[1.15]">
                    <span class="text-gradient block">
                        La comida casera
                    </span>
                    <span class="text-gray-950 block mt-0.5">
                        de personas como vos.
                    </span>
                </h1>

                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                    Descubrí cocineros independientes de tu zona y pedí sus platos hechos en casa.
                </p>

                <!-- Botones más chicos en una sola línea horizontal -->
                <div class="flex items-center gap-2 pt-1 w-full max-w-sm">
                    <a href="{{ route('marketplace.catalog') }}"
                        class="flex-1 btn-gradient text-white py-2.5 px-2.5 rounded-full font-bold text-[11px] sm:text-xs shadow-sm inline-flex items-center justify-center text-center whitespace-nowrap">
                        <span>Explorar cocineros</span>
                        <span class="ml-1 text-[11px]">→</span>
                    </a>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="flex-1 bg-white hover:bg-rose-50/50 text-purple-950 border border-purple-300 py-2.5 px-2.5 rounded-full font-bold text-[11px] sm:text-xs shadow-sm inline-flex items-center justify-center text-center whitespace-nowrap">
                        Quiero cocinar
                    </a>
                </div>

                <!-- Badges de Confianza compactos -->
                <div class="pt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] font-medium text-gray-500">
                    <div class="inline-flex items-center space-x-1">
                        <span class="text-rose-500 font-bold">♡</span>
                        <span>Cocineros reales</span>
                    </div>
                    <span class="text-gray-300">·</span>
                    <span>Cocinas reales</span>
                    <span class="text-gray-300">·</span>
                    <span>Comida hecha en casa</span>
                </div>
            </div>

            <!-- Imagen debajo con degradé superior e inferior -->
            <div class="relative w-full h-72 sm:h-80 mt-5 overflow-hidden">
                <img src="{{ asset('assets/home/hero_cook.jpg') }}" alt="Cocinera preparando comida casera en Cocinarte"
                    class="w-full h-full object-cover object-[center_right]" loading="eager">

                <!-- Degradé superior que funde la imagen con el contenido de arriba -->
                <div
                    class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-[#faf8f5] via-[#faf8f5]/70 to-transparent pointer-events-none">
                </div>

                <!-- Degradé inferior hacia la siguiente sección -->
                <div
                    class="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-[#faf8f5] to-transparent pointer-events-none">
                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- BANNER MARQUEE DE ALTO IMPACTO (MOVIMIENTO CONTINUO)         -->
        <!-- ============================================================ -->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-orange-500 via-rose-500 to-purple-600 text-white py-3.5 sm:py-4 shadow-lg shadow-rose-500/15 border-y border-white/20 select-none">
            <!-- Máscaras de degradé en los extremos para desvanecimiento elegante -->
            <div
                class="pointer-events-none absolute inset-y-0 left-0 w-10 sm:w-24 bg-gradient-to-r from-orange-500 via-orange-500/80 to-transparent z-10">
            </div>
            <div
                class="pointer-events-none absolute inset-y-0 right-0 w-10 sm:w-24 bg-gradient-to-l from-purple-600 via-purple-600/80 to-transparent z-10">
            </div>

            <div class="animate-marquee flex items-center gap-8 sm:gap-14 whitespace-nowrap">
                <!-- Bloque 1 de items -->
                <div class="flex items-center gap-8 sm:gap-14 shrink-0">
                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">✨</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Registro gratuito</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-white text-sm group-hover:scale-110 transition-transform">🚀</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Vendé con nosotros sin
                            costo</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-rose-200 text-sm group-hover:scale-110 transition-transform">♡</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Sé parte del cambio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">🧑‍🍳</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Cociná para tu barrio o ciudad
                            y convertilo en tu oficio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>
                </div>

                <!-- Bloque 2 duplicado idéntico para loop infinito perfecto -->
                <div class="flex items-center gap-8 sm:gap-14 shrink-0" aria-hidden="true">
                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">✨</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Registro gratuito</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-white text-sm group-hover:scale-110 transition-transform">🚀</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Vendé con nosotros sin
                            costo</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-rose-200 text-sm group-hover:scale-110 transition-transform">♡</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Sé parte del cambio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">🧑‍🍳</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Cociná para tu barrio o ciudad
                            y convertilo en tu oficio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>
                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 2. SECCIÓN: ASÍ FUNCIONA COCINARTE                           -->
        <!-- ============================================================ -->
        <section class="py-14 sm:py-20" id="como-funciona">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-[2.6rem] font-extrabold tracking-tight text-gray-700">
                        Así funciona <span class="text-gradient">Cocinarte</span>
                    </h2>
                </div>

                <!-- SVG Defs para Gradientes de Íconos y Flechas -->
                <svg class="absolute w-0 h-0 overflow-hidden pointer-events-none" aria-hidden="true">
                    <defs>
                        <linearGradient id="step-icon-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#f97316" />
                            <stop offset="50%" stop-color="#e11d48" />
                            <stop offset="100%" stop-color="#c026d3" />
                        </linearGradient>
                        <linearGradient id="step-arrow-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#e11d48" />
                            <stop offset="100%" stop-color="#9333ea" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- 3 Pasos con Layout Horizontal y Flechas Conectoras -->
                <div
                    class="max-w-5xl mx-auto flex flex-col md:flex-row items-start justify-between gap-8 md:gap-4 lg:gap-6 relative">

                    <!-- Paso 1 -->
                    <div class="flex-1 flex items-start gap-3.5 sm:gap-4 group">
                        <!-- Círculo Número 1 -->
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-tr from-orange-500 via-rose-500 to-pink-500 text-white font-extrabold text-base sm:text-lg flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0 mt-0.5 group-hover:scale-105 transition-transform duration-200">
                            1
                        </div>

                        <!-- Contenido: Icono + Título + Descripción -->
                        <div class="space-y-2.5 sm:space-y-3">
                            <div
                                class="w-12 h-12 flex items-center justify-start group-hover:scale-105 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#step-icon-grad)" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="w-11 h-11 sm:w-12 sm:h-12 drop-shadow-sm">
                                    <path d="M6 18h12v3H6z" />
                                    <path d="M6 18v-2" />
                                    <path d="M18 18v-2" />
                                    <path
                                        d="M6 16c-2 0-3.5-1.8-3-3.8C3.5 10 5.2 9 7 9.5 7.8 6.8 10.2 5 13 5c2.5 0 4.7 1.4 5.6 3.6 1.8-.3 3.4.8 3.9 2.5.6 2.2-.8 4.9-3.5 4.9" />
                                    <path d="M9 16v-5.5" />
                                    <path d="M12 16V11" />
                                    <path d="M15 16v-4.5" />
                                </svg>
                            </div>

                            <h3 class="font-extrabold text-gray-700 text-lg leading-tight">
                                Encontrá un cocinero
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed font-normal max-w-[240px]">
                                Explorá perfiles, leé sus historias y elegí a quién te inspire.
                            </p>
                        </div>
                    </div>

                    <!-- Flecha 1 -> 2 (Desktop) -->
                    <div class="hidden md:flex items-center justify-center pt-3 shrink-0 px-1 lg:px-2">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24">
                            <path d="M4 12h14m-5-5l5 5-5 5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Flecha 1 -> 2 (Mobile vertical) -->
                    <div class="flex md:hidden items-center justify-center w-full py-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <path d="M12 4v14m-5-5l5 5 5-5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Paso 2 -->
                    <div class="flex-1 flex items-start gap-3.5 sm:gap-4 group">
                        <!-- Círculo Número 2 -->
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-tr from-orange-500 via-rose-500 to-pink-500 text-white font-extrabold text-base sm:text-lg flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0 mt-0.5 group-hover:scale-105 transition-transform duration-200">
                            2
                        </div>

                        <!-- Contenido: Icono + Título + Descripción -->
                        <div class="space-y-2.5 sm:space-y-3">
                            <div
                                class="w-12 h-12 flex items-center justify-start group-hover:scale-105 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#step-icon-grad)" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="w-11 h-11 sm:w-12 sm:h-12 drop-shadow-sm">
                                    <path d="M2 10h20" />
                                    <path d="M3 10c0 5.5 4 9 9 9s9-3.5 9-9" />
                                    <path d="M9 19c0 1.5 1.5 2 3 2s3-.5 3-2" />
                                    <path d="M9 7c.5-1.5-.5-2.5 0-4" />
                                    <path d="M12 7c.5-1.5-.5-2.5 0-4" />
                                    <path d="M15 7c.5-1.5-.5-2.5 0-4" />
                                    <path d="M16 10l2.5-3" />
                                    <path d="M6 10c1 0 1.5-.5 2-.5s1 .5 2 .5 1.5-.5 2-.5 1 .5 2 .5" />
                                </svg>
                            </div>

                            <h3 class="font-extrabold text-gray-700 text-lg leading-tight">
                                Elegí su comida
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed font-normal max-w-[240px]">
                                Mirá su menú, sus fotos y hacé tu pedido en pocos clics.
                            </p>
                        </div>
                    </div>

                    <!-- Flecha 2 -> 3 (Desktop) -->
                    <div class="hidden md:flex items-center justify-center pt-3 shrink-0 px-1 lg:px-2">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24">
                            <path d="M4 12h14m-5-5l5 5-5 5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Flecha 2 -> 3 (Mobile vertical) -->
                    <div class="flex md:hidden items-center justify-center w-full py-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <path d="M12 4v14m-5-5l5 5 5-5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Paso 3 -->
                    <div class="flex-1 flex items-start gap-3.5 sm:gap-4 group">
                        <!-- Círculo Número 3 -->
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-tr from-orange-500 via-rose-500 to-pink-500 text-white font-extrabold text-base sm:text-lg flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0 mt-0.5 group-hover:scale-105 transition-transform duration-200">
                            3
                        </div>

                        <!-- Contenido: Icono + Título + Descripción -->
                        <div class="space-y-2.5 sm:space-y-3">
                            <div
                                class="w-12 h-12 flex items-center justify-start group-hover:scale-105 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#step-icon-grad)" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="w-11 h-11 sm:w-12 sm:h-12 drop-shadow-sm">
                                    <path d="M17 7.5V4h-2.2v1.7" />
                                    <path d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10.5z" />
                                    <path d="M9 22v-5a3 3 0 0 1 6 0v5" />
                                </svg>
                            </div>

                            <h3 class="font-extrabold text-gray-700 text-lg leading-tight">
                                Recibila o retirala
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed font-normal max-w-[240px]">
                                Coordiná la entrega o pasá a buscarla en la cocina del cocinero.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 3. SECCIÓN: CONOCÉ A QUIENES COCINAN PARA VOS (SLIDER/CARDS) -->
        <!-- ============================================================ -->
        <section class="py-14 sm:py-20 relative" id="cocineros" x-data="cooksSlider()" x-init="startAutoplay()"
            @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()" @touchstart.passive="stopAutoplay()"
            @touchend.passive="startAutoplay()">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Título y Subtítulo Centrados -->
                <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                    <h2 class="text-3xl sm:text-4xl lg:text-[2.6rem] font-extrabold tracking-tight">
                        <span class="text-gradient">
                            Conocé a quienes cocinan para vos
                        </span>
                    </h2>
                    <p class="mt-3 text-sm sm:text-base text-gray-600 font-normal">
                        Personas reales, con sus propias historias, en sus propias cocinas.
                    </p>
                </div>

                <!-- Controles de Navegación del Slider (Desktop y Mobile) -->
                <div class="flex items-center justify-end space-x-2 mb-4 pr-1">
                    <button @click="scrollSlider(-1); stopAutoplay(); startAutoplay()" type="button"
                        class="w-10 h-10 rounded-full bg-white hover:bg-orange-50 border border-gray-200 text-gray-700 hover:text-orange-600 flex items-center justify-center shadow-sm hover:shadow transition-all active:scale-95"
                        aria-label="Anterior cocinero">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="scrollSlider(1); stopAutoplay(); startAutoplay()" type="button"
                        class="w-10 h-10 rounded-full bg-white hover:bg-orange-50 border border-gray-200 text-gray-700 hover:text-orange-600 flex items-center justify-center shadow-sm hover:shadow transition-all active:scale-95"
                        aria-label="Siguiente cocinero">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- SLIDER CONTENEDOR (Scroll Snap nativo fluido + drag/swipe con Autoplay) -->
                <div x-ref="cardsContainer"
                    class="cook-slider flex gap-5 overflow-x-auto snap-x snap-mandatory py-4 px-1 -mx-4 sm:mx-0 px-4 sm:px-0">

                    @foreach($featuredCooks as $cook)
                        <div class="cook-card flex-none w-[270px] sm:w-[290px] lg:w-[calc(25%-15px)] snap-start group">
                            <div
                                class="bg-white rounded-3xl p-4 shadow-sm hover:shadow-xl border border-orange-100/60 hover:border-orange-200 transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1">

                                <!-- Foto del Cocinero + Badge "En tu zona" -->
                                <div class="relative w-full h-44 rounded-2xl overflow-hidden bg-gray-100 mb-3.5">
                                    <img src="{{ $cook['photo'] }}" alt="Cocinero {{ $cook['name'] }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                        loading="lazy">

                                    <!-- Badge "En tu zona" -->
                                    <span
                                        class="absolute top-2.5 right-2.5 bg-white/90 backdrop-blur-md text-amber-700 text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-sm flex items-center space-x-1 border border-orange-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>En tu zona</span>
                                    </span>
                                </div>

                                <!-- Información del Cocinero -->
                                <div class="space-y-1 mb-3">
                                    <div class="flex items-center justify-between">
                                        <h3
                                            class="font-bold text-gray-700 text-base group-hover:text-orange-600 transition-colors">
                                            {{ $cook['name'] }}
                                        </h3>
                                    </div>
                                    <p class="text-xs text-gray-500 font-medium">
                                        {{ $cook['specialty'] }}
                                    </p>

                                    <!-- Ubicación con Pin -->
                                    <div class="flex items-center text-xs text-purple-700/80 pt-0.5">
                                        <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 text-purple-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ $cook['location'] }}</span>
                                    </div>

                                    <!-- Calificación y Reviews -->
                                    <div class="flex items-center space-x-1.5 text-xs pt-1">
                                        <span class="text-amber-500 font-bold">★</span>
                                        <span class="font-bold text-gray-700">{{ $cook['rating'] }}</span>
                                        <span class="text-gray-400 font-normal">({{ $cook['reviews_count'] }})</span>
                                    </div>
                                </div>

                                <!-- 3 Fotos Miniatura de sus Platos -->
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100 mt-auto">
                                    @foreach($cook['dishes'] as $dishImg)
                                        <div class="w-full h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100/80">
                                            <img src="{{ $dishImg }}" alt="Plato de {{ $cook['name'] }}"
                                                class="w-full h-full object-cover hover:scale-110 transition-transform duration-300"
                                                loading="lazy">
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Link al perfil / catálogo -->
                                <a href="{{ $cook['url'] }}"
                                    class="mt-3 block text-center py-2 px-3 rounded-xl bg-orange-50/60 hover:bg-orange-100/80 text-orange-700 font-semibold text-xs transition-colors">
                                    Ver platos y menú
                                </a>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 4. SECCIÓN: CADA COCINERO TIENE SU PROPIA COCINA             -->
        <!-- ============================================================ -->
        <section class="py-12 sm:py-16" id="historias">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="">

                    <!-- Orbs decorativos traseros -->
                    <div
                        class="absolute -right-16 -top-16 w-80 h-80 bg-rose-200/30 rounded-full filter blur-3xl pointer-events-none">
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 relative z-10">

                        <!-- Lado Izquierdo: Video / Foto Grande de Cocina (60%) -->
                        <div class="lg:col-span-7">
                            <div class="relative group">
                                <div class="overflow-hidden rounded-2xl sm:rounded-3xl shadow-xl border-4 border-white">
                                    <!-- <img src="{{ asset('assets/home/kitchen_mariana.jpg') }}"
                                                                                                                                    alt="Cocinera Mariana en su cocina preparando ingredientes frescos"
                                                                                                                                    class="w-full h-[320px] sm:h-[420px] object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                                                                                                                                    loading="lazy"> -->
                                    <div
                                        class="aspect-video bg-gradient-to-br from-purple-200 via-pink-200 to-orange-200 rounded-2xl flex items-center justify-center text-8xl">
                                        <video class="w-full h-full rounded-2xl"
                                            poster="{{ asset('assets/front/cubierta.webp') }}" width="100%" height="auto"
                                            controls>
                                            <source src="{{ asset('assets/video/presentation.mp4') }}" type="video/mp4">
                                            Tu navegador no soporta la etiqueta de video.
                                        </video>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lado Derecho: Contenido, Card Flotante y Firma Manuscrita (40%) -->
                        <div class="lg:col-span-5 space-y-6">

                            <div class="space-y-3">
                                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                                    <span class="text-gradient">
                                        Cada cocinero tiene su propia cocina
                                    </span>
                                </h2>
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed font-normal">
                                    Detrás de cada plato hay una persona, su historia y su hogar. Cocinamos en nuestras
                                    casas, con ingredientes reales y todo el cariño del mundo.
                                </p>
                            </div>

                            <!-- Card Flotante de Mariana López -->
                            <div
                                class="bg-white rounded-2xl p-5 shadow-lg border border-orange-100/90 space-y-3 transform hover:-translate-y-0.5 transition-transform">
                                <div class="flex items-center space-x-3.5">
                                    <img src="{{ asset('assets/home/cook_mariana.jpg') }}" alt="Mariana López"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-orange-200">
                                    <div>
                                        <h4 class="font-bold text-gray-700 text-sm">Mariana López</h4>
                                        <div class="flex items-center text-xs text-purple-700 font-medium">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span>Córdoba Capital · 4 km</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs sm:text-sm text-gray-600 italic leading-relaxed">
                                    "Cocino porque me encanta y porque compartir la comida también es una forma de cuidar."
                                </p>

                                <!-- Miniaturas de platos de Mariana -->
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                                    <div class="h-16 rounded-xl overflow-hidden bg-gray-100">
                                        <img src="{{ asset('assets/home/dish_mini_gnocchi.jpg') }}" alt="Gnocchi caseros"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="h-16 rounded-xl overflow-hidden bg-gray-100">
                                        <img src="{{ asset('assets/home/dish_mini_roast.jpg') }}" alt="Pollo al horno"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="h-16 rounded-xl overflow-hidden bg-gray-100">
                                        <img src="{{ asset('assets/front/ensalada_casera.png') }}" alt="Ensalada fresca"
                                            class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </div>

                            <!-- Nota Manuscrita -->
                            <div class="pt-2 flex justify-end">
                                <p class="font-handwriting text-2xl sm:text-3xl text-purple-900 -rotate-2 select-none">
                                    Comida real, hecha en casa <span class="text-rose-500">♡</span>
                                </p>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 5. BANNER CTA COCINEROS: "¿COCINÁS PARA LOS TUYOS?"          -->
        <!-- ============================================================ -->
        <section class="py-10 sm:py-16" id="para-cocineros">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl sm:rounded-[2.5rem] bg-rose-500 text-white shadow-2xl">

                    <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">

                        <!-- Lado Izquierdo: Textos y Botón (degradé propio que termina en rose-500 sólido) -->
                        <div
                            class="lg:col-span-7 p-8 sm:p-12 lg:p-16 space-y-6 relative z-10 flex flex-col justify-center bg-gradient-to-b lg:bg-gradient-to-r from-orange-500 to-rose-500">

                            <!-- Icono Chef Outline -->
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4a3.5 3.5 0 00-3.4 2.7A3.5 3.5 0 005 10a3.5 3.5 0 002.5 3.37L7 18h10l-.5-4.63A3.5 3.5 0 0019 10a3.5 3.5 0 00-3.6-3.3A3.5 3.5 0 0012 4zM9 18h6v2H9v-2z" />
                                </svg>
                            </div>

                            <div class="space-y-2">
                                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight tracking-tight">
                                    ¿Cocinás para los tuyos?<br>
                                    Podés cocinar para tu barrio.
                                </h2>
                                <p class="text-sm sm:text-base text-white/90 leading-relaxed font-normal pt-2 max-w-lg">
                                    Elegí qué cocinar, cuándo cocinar y a qué precio.<br>
                                    Vos decidís, nosotros te ayudamos a llegar a más personas.
                                </p>
                            </div>

                            <div class="pt-4 flex flex-wrap items-center gap-6">
                                <a href="{{ route('register', ['role' => 'cook']) }}"
                                    class="bg-white hover:bg-rose-50 text-rose-600 px-8 py-4 rounded-full font-bold text-sm sm:text-base shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all inline-flex items-center space-x-2">
                                    <span>Quiero ser cocinero</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>

                                <!-- Frase manuscrita blanca -->
                                <p class="font-handwriting text-2xl sm:text-3xl text-white/95 leading-tight select-none">
                                    Tu cocina también importa <span class="text-rose-200">♡</span>
                                </p>
                            </div>

                        </div>

                        <!-- Lado Derecho: Foto Cocinera con delantal floreado -->
                        <div class="lg:col-span-5 relative min-h-[300px] sm:min-h-[380px] lg:-ml-px">
                            <img src="{{ asset('assets/home/banner_cook.jpg') }}"
                                alt="Cocinera sonriente preparando un guiso casero"
                                class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy">
                            <!-- Fusión: arranca en rose-500 sólido (mismo color final del box) y se vuelve transparente -->
                            <div
                                class="absolute inset-0 bg-gradient-to-b lg:bg-gradient-to-r from-rose-500 from-0% via-rose-500/50 via-25% to-transparent to-60% pointer-events-none">
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- PRE-INSCRIPCIONES: COCINEROS INDEPENDIENTES (ENERO 2027)    -->
        <!-- ============================================================ -->
        <section class="py-14 sm:py-20 border-b border-orange-100/50 relative overflow-hidden" id="pre-inscripciones">
            <!-- Fondos sutiles difuminados -->
            <div class="absolute top-0 right-0 w-80 sm:w-96 h-80 sm:h-96 bg-orange-100/50 rounded-full blur-3xl pointer-events-none -mr-24 -mt-24"
                aria-hidden="true"></div>
            <div class="absolute bottom-0 left-0 w-80 sm:w-96 h-80 sm:h-96 bg-rose-100/40 rounded-full blur-3xl pointer-events-none -ml-24 -mb-24"
                aria-hidden="true"></div>

            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <!-- Encabezado de la Sección -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14 pre-reveal">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 border border-orange-200 text-orange-700 text-xs sm:text-sm font-bold uppercase tracking-wider mb-4 shadow-sm">
                        <svg class="w-4 h-4 text-orange-500 animate-pulse" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Pre-inscripciones Abiertas · Lanzamiento Enero 2027</span>
                    </div>

                    <h2
                        class="text-3xl sm:text-4xl lg:text-[2.75rem] font-black tracking-tight text-gray-900 leading-[1.15]">
                        ¿Cocinás rico en casa y querés vender en tu barrio?<br>
                        <span class="text-gradient">Nosotros te armamos el perfil.</span>
                    </h2>

                    <p class="mt-4 text-sm sm:text-base text-gray-600 leading-relaxed font-normal max-w-2xl mx-auto">
                        El lanzamiento oficial de Cocinarte será en <strong>Enero de 2027</strong>. Hasta entonces,
                        recibimos a todos los cocineros y cocineras independientes con tiempo para preparar su perfil y sus
                        platos sin apuros. Si te cuesta la tecnología o tenés dudas de cómo empezar, <strong>estamos para
                            ayudarte</strong>.
                    </p>
                </div>

                <!-- Grilla Principal Bento (2 Columnas alineadas arriba) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start max-w-6xl mx-auto">

                    <!-- Columna Izquierda (lg:col-span-5): Fotos de Cocineros Argentinos Reales + Tarjeta de Lanzamiento de Alto Impacto -->
                    <div class="lg:col-span-5 flex flex-col space-y-5 sm:space-y-6 items-start w-full">

                        <!-- Contenedor con las dos fotos (Cocinera y Cocinero argentinos con suave zoom) -->
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 w-full pre-reveal pre-delay-1">
                            <!-- Foto Cocinera Argentina -->
                            <div
                                class="group relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-md border-2 border-orange-100/80 bg-orange-50 aspect-square pre-img-box cursor-pointer">
                                <img src="{{ asset('assets/home/cocinera_argentina.jpg') }}"
                                    alt="Cocinera argentina sonriente preparando empanadas en su cocina"
                                    class="w-full h-full object-cover" loading="lazy">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent flex flex-col justify-end p-3 sm:p-4 text-white">
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-bold text-orange-200 uppercase tracking-wider mb-0.5">
                                        <svg class="w-3.5 h-3.5 text-orange-300 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        Cocina casera
                                    </span>
                                    <p class="text-xs sm:text-sm font-bold leading-tight drop-shadow-sm">Mariana · Empanadas
                                        & Pastas</p>
                                </div>
                            </div>

                            <!-- Foto Cocinero Argentino -->
                            <div
                                class="group relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-md border-2 border-orange-100/80 bg-orange-50 aspect-square pre-img-box cursor-pointer">
                                <img src="{{ asset('assets/home/cocinero_argentino.jpg') }}"
                                    alt="Cocinero argentino preparando un guiso tradicional en su cocina"
                                    class="w-full h-full object-cover" loading="lazy">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent flex flex-col justify-end p-3 sm:p-4 text-white">
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-bold text-rose-200 uppercase tracking-wider mb-0.5">
                                        <svg class="w-3.5 h-3.5 text-rose-300 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343a7.975 7.975 0 012.344 5.657c0 2.12-.832 4.157-2.343 5.657z" />
                                        </svg>
                                        Sabores del barrio
                                    </span>
                                    <p class="text-xs sm:text-sm font-bold leading-tight drop-shadow-sm">Marcelo · Guisos &
                                        Carnes</p>
                                </div>
                            </div>
                        </div>

                        <!-- Bloque de Alto Impacto: Fecha de Lanzamiento Enero 2027 -->
                        <div
                            class="w-full pre-reveal pre-delay-2 pre-zoom-card relative rounded-3xl p-6 sm:p-7 bg-gradient-to-br from-orange-500 via-rose-500 to-pink-600 text-white shadow-xl shadow-orange-500/20 overflow-hidden group">
                            <!-- Resplandores y destellos decorativos -->
                            <div
                                class="absolute -top-12 -right-12 w-44 h-44 bg-white/20 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-700">
                            </div>
                            <div
                                class="absolute -bottom-10 -left-10 w-36 h-36 bg-amber-400/25 rounded-full blur-xl pointer-events-none">
                            </div>

                            <div class="relative z-10">
                                <!-- Top Tag con SVG Flat de Calendario -->
                                <div class="flex items-center justify-between gap-2 flex-wrap mb-4">
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-black uppercase tracking-wider shadow-sm">
                                        <svg class="w-3.5 h-3.5 text-amber-200" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Gran Apertura Oficial
                                    </span>
                                    <span
                                        class="text-[11px] font-bold text-white/90 bg-black/15 px-2.5 py-0.5 rounded-full backdrop-blur-sm">
                                        Etapa Pioneros
                                    </span>
                                </div>

                                <!-- Tipografía de Alto Impacto -->
                                <div class="mb-3">
                                    <span
                                        class="text-xs font-extrabold uppercase tracking-widest text-orange-100 block">Lanzamiento</span>
                                    <h3
                                        class="text-3xl sm:text-4xl font-black tracking-tight text-white drop-shadow leading-none mt-1">
                                        Enero de 2027
                                    </h3>
                                    <p class="text-sm font-semibold text-rose-100/95 mt-2 leading-snug">
                                        Tenés tiempo para preparar todo con calma y sin apuros.
                                    </p>
                                </div>

                                <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal mb-5">
                                    No hay presiones. Hasta el lanzamiento estamos sumando a la primera comunidad de
                                    cocineros pioneros. Vos decidís qué cocinar, qué días hacerlo y qué precio ponerle a tus
                                    platos.
                                </p>

                                <!-- Beneficios con Iconos Flat SVG -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-4 border-t border-white/20">
                                    <div
                                        class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl text-xs font-bold text-white">
                                        <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Sin costos de inicio</span>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl text-xs font-bold text-white">
                                        <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>100% independiente</span>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl text-xs font-bold text-white">
                                        <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Tu cocina, tus reglas</span>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl text-xs font-bold text-white">
                                        <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Asistencia 1 a 1</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Columna Derecha (lg:col-span-7): "Nosotros te creamos el perfil" con SVG Flat y Suave Zoom -->
                    <div
                        class="lg:col-span-7 bg-[#fffaf5] p-6 sm:p-8 lg:p-9 rounded-3xl border-2 border-orange-200/80 shadow-xl pre-zoom-card pre-reveal pre-delay-2 relative overflow-hidden">
                        <!-- Pill superior con SVG Flat -->
                        <div class="flex items-center justify-between gap-2 flex-wrap mb-4">
                            <span
                                class="inline-flex items-center gap-2 text-xs font-extrabold text-rose-600 bg-rose-50 px-3.5 py-1.5 rounded-full border border-rose-200 shadow-sm">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Ayuda y acompañamiento personal</span>
                            </span>
                            <span class="text-xs font-semibold text-gray-500">
                                ¿Dudas tecnológicas? Cero problema
                            </span>
                        </div>

                        <div>
                            <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-snug">
                                ¿Te cuesta crear tu perfil o te da miedo la tecnología?
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 mt-2 leading-relaxed">
                                No te preocupes por formularios ni configuraciones difíciles. Envianos un email a
                                <a href="mailto:info@cocinarte.app"
                                    class="font-bold text-orange-600 hover:text-orange-700 underline decoration-orange-300">info@cocinarte.app</a>
                                con estos datos mínimos y <strong>nosotros nos encargamos de armar y publicar tu perfil
                                    completo</strong>:
                            </p>

                            <!-- Lista de datos mínimos y necesarios con SVGs Flat e interactividad -->
                            <div class="mt-5 space-y-3">
                                <!-- Paso 1 -->
                                <div
                                    class="pre-step-item group flex items-start gap-3.5 bg-white p-3.5 sm:p-4 rounded-2xl border border-orange-100 shadow-sm cursor-default">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-orange-500 text-white font-bold text-[10px]">1</span>
                                            <strong class="text-gray-900 font-bold">Nombre y teléfono / WhatsApp</strong>
                                        </div>
                                        <p class="mt-0.5 text-gray-600">Para coordinar con vos y avisarte en el momento que
                                            tu perfil esté listo.</p>
                                    </div>
                                </div>

                                <!-- Paso 2 -->
                                <div
                                    class="pre-step-item group flex items-start gap-3.5 bg-white p-3.5 sm:p-4 rounded-2xl border border-orange-100 shadow-sm cursor-default">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-orange-500 text-white font-bold text-[10px]">2</span>
                                            <strong class="text-gray-900 font-bold">Tu dirección o barrio
                                                aproximado</strong>
                                        </div>
                                        <p class="mt-0.5 text-gray-600">La zona donde cocinás (para que los vecinos cercanos
                                            puedan descubrirte).</p>
                                    </div>
                                </div>

                                <!-- Paso 3 -->
                                <div
                                    class="pre-step-item group flex items-start gap-3.5 bg-white p-3.5 sm:p-4 rounded-2xl border border-orange-100 shadow-sm cursor-default">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-orange-500 text-white font-bold text-[10px]">3</span>
                                            <strong class="text-gray-900 font-bold">Foto tuya y foto de tu cocina</strong>
                                        </div>
                                        <p class="mt-0.5 text-gray-600">Dos fotos sacadas con el celular: tu sonrisa y el
                                            espacio donde cocinás con amor.</p>
                                    </div>
                                </div>

                                <!-- Paso 4 -->
                                <div
                                    class="pre-step-item group flex items-start gap-3.5 bg-white p-3.5 sm:p-4 rounded-2xl border border-orange-100 shadow-sm cursor-default">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 3v2m0 0c4.97 0 9 3.582 9 8H3c0-4.418 4.03-8 9-8zm-9 10v2a2 2 0 002 2h14a2 2 0 002-2v-2H3z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-orange-500 text-white font-bold text-[10px]">4</span>
                                            <strong class="text-gray-900 font-bold">Un plato estrella que quieras
                                                publicar</strong>
                                        </div>
                                        <p class="mt-0.5 text-gray-600">Nombre del plato, foto, descripción casera y el
                                            precio que quieras cobrarlo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bloque de Acción: Mailto Directo + Botón de Registro -->
                        <div
                            class="mt-6 pt-5 border-t border-orange-200/60 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="mailto:info@cocinarte.app?subject=Pre-inscripci%C3%B3n%20Cocinero%20Cocinarte&body=Hola%20Cocinarte!%0A%0AQuiero%20pre-inscribirme%20como%20cocinero/a%20para%20el%20lanzamiento%20de%20Enero%202027.%0A%0AMis%20datos:%0A-%20Nombre%20completo:%20%0A-%20WhatsApp%20de%20contacto:%20%0A-%20Barrio%20/%20Ciudad:%20%0A-%20Plato%20estrella%20(nombre%20y%20precio):%20%0A%0A(Adjunto%20foto%20de%20perfil,%20foto%20de%20cocina%20y%20foto%20del%20plato)%0A%0AGracias!"
                                class="btn-gradient text-white px-6 py-3.5 rounded-full font-bold text-sm shadow-md glow-effect hover:scale-[1.02] active:scale-[0.98] transition-all text-center inline-flex items-center justify-center gap-2.5">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Enviar mis datos a info@cocinarte.app</span>
                            </a>

                            <a href="{{ route('register', ['role' => 'cook']) }}"
                                class="group bg-white hover:bg-orange-50/70 text-gray-700 hover:text-orange-600 px-5 py-3.5 rounded-full font-semibold text-xs sm:text-sm border border-gray-300 hover:border-orange-200 text-center transition-all inline-flex items-center justify-center gap-1.5 hover:scale-[1.02]">
                                <span>O registrate vos mismo</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>

                        <!-- Nota de cercanía y calidez al pie -->
                        <p
                            class="text-xs text-gray-500 mt-3 text-center sm:text-left flex items-center justify-center sm:justify-start gap-1.5">
                            <svg class="w-4 h-4 text-rose-500 shrink-0 fill-rose-500/20" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span>Cualquier duda escribinos a <strong>info@cocinarte.app</strong>. ¡Estamos para ayudarte en
                                cada paso!</span>
                        </p>

                    </div>

                </div>

            </div>
        </section>

        <!-- ============================================================ -->
        <!-- 6. SECCIÓN INFERIOR: 3 COLUMNAS                              -->
        <!-- (Comensal / Preguntas Frecuentes / Testimonios)             -->
        <!-- ============================================================ -->
        <section class="py-12 sm:py-20" id="faq">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-stretch">

                    <!-- COLUMNA 1: ¿SOS COMENSAL? -->
                    <div
                        class="bg-[#fef9f5] rounded-3xl p-6 sm:p-7 border border-orange-100 flex flex-col justify-between shadow-sm">
                        <div>
                            <!-- Foto Plato en Manos -->
                            <div class="w-full h-44 rounded-2xl overflow-hidden mb-5 bg-gray-100 shadow-sm">
                                <img src="{{ asset('assets/home/dish_hands.jpg') }}"
                                    alt="Plato de pasta casera con albahaca"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                            </div>

                            <h3 class="text-xl font-bold text-rose-500">
                                ¿Sos comensal?
                            </h3>
                            <h4 class="text-base font-extrabold text-gray-700 mt-0.5 mb-2.5">
                                Disfrutá comida casera, cerca tuyo.
                            </h4>
                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                                Comida rica, hecha con ingredientes reales, por personas de tu barrio. Apoyás a cocineros
                                independientes y vivís una experiencia única.
                            </p>
                        </div>

                        <div class="pt-6">
                            <a href="{{ route('marketplace.catalog') }}"
                                class="w-full btn-gradient text-white py-3 px-5 rounded-full font-bold text-xs sm:text-sm text-center shadow-md glow-effect hover:-translate-y-0.5 transition-all inline-flex items-center justify-center space-x-2">
                                <span>Explorar cocineros</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- COLUMNA 2: PREGUNTAS FRECUENTES (Acordeón) -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm flex flex-col justify-between"
                        x-data="{ openFaq: null }">
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-5">
                                Preguntas frecuentes
                            </h3>

                            <div class="space-y-3">
                                <!-- FAQ 1 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 1 ? null : 1)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Cómo funciona Cocinarte?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 1 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 1" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Elegís un cocinero cerca tuyo, seleccionás sus platos caseros y decidís si querés
                                        envío a domicilio o retiro por su cocina. Pagás seguro por la web.
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 2 ? null : 2)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Cómo se paga?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 2 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 2" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Aceptamos MercadoPago con tarjetas de débito, crédito o dinero en cuenta,
                                        garantizando máxima seguridad en cada transacción.
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 3 ? null : 3)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Cuánto tarda la entrega o retiro?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 3 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 3" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Cada cocinero especifica el tiempo de elaboración (habitualmente entre 30 y 60
                                        minutos, o pedidos programados para el mediodía/noche).
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 4 ? null : 4)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Puedo pedir para varias personas?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 4 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 4" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        ¡Claro que sí! Podés seleccionar múltiples porciones de un mismo cocinero o armar un
                                        menú familiar.
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="pb-1">
                                    <button @click="openFaq = (openFaq === 5 ? null : 5)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Qué pasa si tengo un problema con mi pedido?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 5 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 5" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Contás con soporte directo por WhatsApp y nuestro equipo de atención para resolver
                                        cualquier inconveniente de inmediato.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA 3: LO QUE DICEN NUESTROS COMENSALES (Testimonios Slider con Autoplay y 4 items) -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm flex flex-col justify-between"
                        x-data="testimonialsSlider({{ Js::from($testimonials) }})" x-init="startAutoplay()"
                        @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()" @touchstart.passive="stopAutoplay()"
                        @touchend.passive="startAutoplay()">

                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-6">
                                Lo que dicen nuestros comensales
                            </h3>

                            <!-- Testimonio Activo con transición suave -->
                            <div class="space-y-4 min-h-[140px]">
                                <div class="flex items-center space-x-3.5">
                                    <img :src="items[current].avatar" :alt="items[current].name"
                                        class="w-11 h-11 rounded-full object-cover border-2 border-orange-200">
                                    <div>
                                        <h4 class="font-bold text-gray-700 text-sm" x-text="items[current].name"></h4>
                                        <p class="text-xs text-gray-400" x-text="items[current].location"></p>
                                    </div>
                                </div>

                                <p class="text-xs sm:text-sm text-gray-600 italic leading-relaxed min-h-[50px]"
                                    x-text="'&ldquo;' + items[current].text + '&rdquo;'">
                                </p>

                                <!-- Estrellas Doradas -->
                                <div class="flex items-center space-x-1 text-amber-400 text-sm pt-2">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dots de paginación del testimonio (4 items) -->
                        <div class="flex items-center justify-center space-x-2 pt-6">
                            <template x-for="(item, index) in items" :key="index">
                                <button @click="current = index; stopAutoplay(); startAutoplay()" type="button"
                                    class="h-2.5 rounded-full transition-all duration-300"
                                    :class="current === index ? 'bg-orange-500 w-6' : 'bg-gray-200 hover:bg-gray-300 w-2.5'"
                                    :aria-label="'Ver testimonio ' + (index + 1)">
                                </button>
                            </template>
                        </div>

                    </div>

                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 7 PROPUESTA DE VALOR: "VOS PONÉS EL SABOR..."             -->
        <!-- (Plataforma / Difusión / Crecimiento / Y mucho más)          -->
        <!-- ============================================================ -->
        <section class="vp-section" id="que-ofrecemos" aria-labelledby="vp-title">

            <!-- Fondo: orbes de color + grilla sutil -->
            <div class="vp-bg" aria-hidden="true">
                <span class="vp-orb vp-orb--1"></span>
                <span class="vp-orb vp-orb--2"></span>
                <span class="vp-orb vp-orb--3"></span>
                <span class="vp-grid-pattern"></span>
            </div>

            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Encabezado -->
                <header class="vp-head vp-reveal">
                    <span class="vp-eyebrow"><span class="vp-eyebrow-dot"></span>Lo que ponemos nosotros</span>
                    <h2 class="vp-title" id="vp-title">
                        Vos ponés el sabor.<br>
                        <span class="vp-title-grad">Nosotros, todo lo demás.</span>
                    </h2>
                    <p class="vp-lead">
                        Sumarte a Cocinarte es tener un equipo detrás de tu cocina: una plataforma confiable,
                        difusión permanente y herramientas para que tu proyecto crezca.
                    </p>
                </header>

                <div class="vp-bento">

                    <!-- CARD 1: Plataforma abierta y segura -->
                    <article class="vp-card vp-card--platform vp-reveal vp-delay-0">
                        <div class="vp-platform">
                            <div>
                                <div class="vp-card-head">
                                    <span class="vp-icon" aria-hidden="true">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z" />
                                            <path d="M9 12l2 2 4-4" />
                                        </svg>
                                    </span>
                                    <span class="vp-kicker">01 · Tu espacio</span>
                                </div>
                                <h3>Un espacio propio, abierto y seguro para vender lo que cocinás</h3>
                                <p class="vp-text">
                                    Publicá tu menú, recibí pedidos y cobrá con pagos protegidos. Vos decidís qué,
                                    cuándo y a qué precio; nosotros cuidamos que cada operación sea clara y confiable.
                                </p>
                                <ul class="vp-checks">
                                    <li>
                                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Pagos protegidos
                                    </li>
                                    <li>
                                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Tus precios, tus horarios
                                    </li>
                                    <li>
                                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Pedidos ordenados
                                    </li>
                                    <li>
                                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Horarios ordenados
                                    </li>
                                </ul>
                            </div>

                            <!-- Mock de notificaciones (decorativo) -->
                            <div class="vp-mock" aria-hidden="true">
                                <div class="vp-notif vp-notif--main">
                                    <img src="{{ asset('assets/home/dish_mini_lasagna.jpg') }}" alt="" loading="lazy">
                                    <div>
                                        <div class="vp-notif-label">Nuevo pedido</div>
                                        <div class="vp-notif-body">2× Lasagna casera</div>
                                    </div>
                                    <div class="vp-notif-end">
                                        <div class="vp-notif-price">$ 14.800</div>
                                        <span class="vp-pill-ok">✓ Pago aprobado</span>
                                    </div>
                                </div>

                                <div class="vp-notif vp-notif--glass vp-notif--right">
                                    <span class="vp-notif-ico vp-notif-ico--green">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="11" width="16" height="10" rx="2" />
                                            <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="vp-notif-label">Cobro protegido</div>
                                        <div class="vp-notif-body">Dinero acreditado</div>
                                    </div>
                                </div>

                                <div class="vp-notif vp-notif--glass vp-notif--left">
                                    <span class="vp-notif-ico vp-notif-ico--orange">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="9" />
                                            <path d="M12 7v5l3 2" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="vp-notif-label">Retiro confirmado</div>
                                        <div class="vp-notif-body">Hoy · 20:30 hs</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- CARD 2: Marketing y captación constante -->
                    <article class="vp-card vp-card--reach vp-reveal vp-delay-1">
                        <div class="vp-card-head">
                            <span class="vp-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1z" />
                                    <path d="M15.5 8.5a5 5 0 0 1 0 7" />
                                    <path d="M18.5 5.5a9 9 0 0 1 0 13" />
                                </svg>
                            </span>
                            <span class="vp-kicker">02 · Difusión</span>
                        </div>
                        <h3>Comensales nuevos llegando a tu cocina</h3>
                        <p class="vp-text">
                            Hacemos la difusión por vos: redes, buscadores y acciones en tu zona para que cada semana
                            más vecinos descubran lo que cocinás.
                        </p>

                        <!-- Radar de comensales (decorativo) -->
                        <div class="vp-radar" aria-hidden="true">
                            <span class="vp-radar-ring"></span>
                            <span class="vp-radar-ring"></span>
                            <span class="vp-radar-ring"></span>
                            <span class="vp-radar-core">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 21s-7-6.1-7-11.5a7 7 0 0 1 14 0C19 14.9 12 21 12 21z" />
                                    <circle cx="12" cy="9.5" r="2.5" />
                                </svg>
                            </span>
                            <span class="vp-avatar vp-avatar--1">MG</span>
                            <span class="vp-avatar vp-avatar--2">JP</span>
                            <span class="vp-avatar vp-avatar--3">LA</span>
                            <span class="vp-avatar vp-avatar--4">SR</span>
                            <span class="vp-avatar vp-avatar--5">+</span>
                        </div>
                    </article>

                    <!-- CARD 3: Opciones de crecimiento -->
                    <article class="vp-card vp-card--growth vp-reveal vp-delay-0">
                        <div class="vp-card-head">
                            <span class="vp-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 17l6-6 4 4 8-8" />
                                    <path d="M15 7h6v6" />
                                </svg>
                            </span>
                            <span class="vp-kicker">03 · Proyección</span>
                        </div>
                        <h3>Crecé a tu ritmo, hasta tener tu propia marca</h3>
                        <p class="vp-text">
                            Cada pedido suma reseñas y reputación. Con más visibilidad y nuevas herramientas, pasás de
                            cocinar para algunos vecinos a un emprendimiento gastronómico con nombre propio.
                        </p>

                        <!-- Gráfico de crecimiento (decorativo) -->
                        <div class="vp-chart" aria-hidden="true">
                            <svg viewBox="0 0 320 120">
                                <defs>
                                    <linearGradient id="vp-line-grad" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#fb923c" />
                                        <stop offset="55%" stop-color="#f43f5e" />
                                        <stop offset="100%" stop-color="#c084fc" />
                                    </linearGradient>
                                    <linearGradient id="vp-area-grad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#f43f5e" stop-opacity=".35" />
                                        <stop offset="100%" stop-color="#f43f5e" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                <path class="vp-chart-area" fill="url(#vp-area-grad)"
                                    d="M0,108 C40,104 60,96 90,91 C125,85 140,72 175,64 C210,56 230,42 260,31 C285,22 300,15 314,10 L314,120 L0,120 Z" />
                                <path class="vp-chart-line" pathLength="1" fill="none" stroke="url(#vp-line-grad)"
                                    stroke-width="3.5" stroke-linecap="round"
                                    d="M0,108 C40,104 60,96 90,91 C125,85 140,72 175,64 C210,56 230,42 260,31 C285,22 300,15 314,10" />
                                <circle class="vp-chart-dot-ring" cx="314" cy="10" r="5" fill="#c084fc" />
                                <circle cx="314" cy="10" r="5" fill="#fff" />
                            </svg>
                            <div class="vp-steps">
                                <span>Primeros pedidos</span>
                                <span>Clientes fieles</span>
                                <span>Tu propia marca</span>
                            </div>
                        </div>
                    </article>

                    <!-- CARD 4: Y mucho más -->
                    <article class="vp-card vp-card--more vp-reveal vp-delay-1">
                        <div class="vp-card-head">
                            <span class="vp-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3l1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3z" />
                                    <path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15z" />
                                </svg>
                            </span>
                            <span class="vp-kicker">04 · Siempre sumando</span>
                        </div>
                        <h3><span class="vp-title-grad">Y mucho más</span></h3>
                        <p class="vp-text">
                            Cocinarte evoluciona todo el tiempo con nuevas herramientas pensadas para tu cocina.
                        </p>

                        @php
                            $vpChipsTop = ['Perfil de cocinero propio', 'Panel de pedidos', 'Reseñas y calificaciones', 'Envío o retiro', 'Menú siempre actualizado', 'Soporte cercano'];
                            $vpChipsBottom = ['Comunidad de cocineros', 'Avisos al instante', 'Galería de tus platos', 'Cobros online', 'Perfil destacado', 'Estadísticas de ventas'];
                        @endphp

                        <!-- Lista accesible para lectores de pantalla -->
                        <ul class="sr-only">
                            @foreach (array_merge($vpChipsTop, $vpChipsBottom) as $chip)
                                <li>{{ $chip }}</li>
                            @endforeach
                        </ul>

                        <div class="vp-marquee" aria-hidden="true">
                            <div class="vp-marquee-track">
                                @foreach (array_merge($vpChipsTop, $vpChipsTop) as $chip)
                                    <span class="vp-chip">{{ $chip }}</span>
                                @endforeach
                            </div>
                            <div class="vp-marquee-track vp-marquee-track--rev">
                                @foreach (array_merge($vpChipsBottom, $vpChipsBottom) as $chip)
                                    <span class="vp-chip">{{ $chip }}</span>
                                @endforeach
                            </div>
                        </div>
                    </article>

                </div>

                <!-- CTA final -->
                <div class="vp-cta vp-reveal">
                    <a href="{{ route('register', ['role' => 'cook']) }}" class="vp-btn" id="vp-cta-register">
                        <span>Sumá tu cocina a Cocinarte</span>
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <p class="vp-cta-note">Empezá hoy y crecé a tu ritmo.</p>
                </div>

            </div>
        </section>

    </div>

@endsection

@push('meta')
    <script type="application/ld+json">
                    {
                        "@@context": "https://schema.org",
                        "@@graph": [
                        {
                            "@@type": "Organization",
                            "@@id": "{{ route('home') }}#organization",
                            "name": "{{ $globalSettings['site_name'] ?? 'Cocinarte' }}",
                            "url": "{{ route('home') }}",
                            "logo": {
                            "@@type": "ImageObject",
                            "url": "{{ asset('assets/front/logo-8.webp') }}",
                            "caption": "{{ $globalSettings['site_name'] ?? 'Cocinarte' }}"
                            }
                        },
                        {
                            "@@type": "WebSite",
                            "@@id": "{{ route('home') }}#website",
                            "url": "{{ route('home') }}",
                            "name": "{{ $globalSettings['site_name'] ?? 'Cocinarte' }}",
                            "description": "{{ $globalSettings['meta_description'] ?? 'La comida casera de personas como vos' }}",
                            "publisher": {
                            "@@id": "{{ route('home') }}#organization"
                            }
                        }
                        ]
                    }
                </script>
@endpush

@push('scripts')
    @vite(['resources/js/pages/landing.js'])
@endpush