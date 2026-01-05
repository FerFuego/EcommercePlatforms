@extends('layouts.app')

@section('title', 'Cocinarte - Comida Casera de Cocineros Locales')

@section('content')
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-pink-500 to-purple-600 opacity-10"></div>
        <div class="container mx-auto px-4 py-20 relative">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight">
                        <span
                            class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                            Comida Casera
                        </span>
                        <br>
                        <span class="text-gray-800">Hecha con Amor</span>
                    </h1>

                    <p class="text-xl text-gray-600 leading-relaxed">
                        Descubre cocineros independientes cerca de ti que preparan comida casera deliciosa.
                        Apoya talento local y disfruta sabores auténticos.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}"
                            class="bg-white border-2 border-orange-500 text-orange-600 px-8 py-4 rounded-2xl font-bold text-lg shadow-lg hover:bg-purple-50 transition-all inline-flex items-center justify-center">
                            Soy Cocinero
                        </a>

                        <a href="{{ route('marketplace.catalog') }}"
                            class="group bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all inline-flex items-center justify-center">
                            Explorar Cocineros
                            <svg class="w-6 h-6 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-8">
                        <div class="text-center">
                            <div
                                class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-pink-600 bg-clip-text text-transparent">
                                150+</div>
                            <div class="text-sm text-gray-600">Cocineros</div>
                        </div>
                        <div class="text-center">
                            <div
                                class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                                2,500+</div>
                            <div class="text-sm text-gray-600">Pedidos</div>
                        </div>
                        <div class="text-center">
                            <div
                                class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-orange-600 bg-clip-text text-transparent">
                                4.9⭐</div>
                            <div class="text-sm text-gray-600">Rating</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative">
                    <div class="relative z-10 grid grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div
                                class="bg-white p-4 rounded-3xl shadow-2xl transform hover:scale-105 transition-transform hover-card">
                                <div class="w-full h-48 bg-gray-100 rounded-2xl overflow-hidden">
                                    <img src="{{ asset('assets/front/pasta_casera.png') }}" alt="Pasta Casera"
                                        class="w-full h-full object-cover">
                                </div>
                                <p class="mt-3 font-semibold text-gray-800">Pasta Casera</p>
                                <p class="text-sm text-gray-600">$1,200</p>
                            </div>
                            <div
                                class="bg-white p-4 rounded-3xl shadow-2xl transform hover:scale-105 transition-transform hover-card">
                                <div class="w-full h-48 bg-gray-100 rounded-2xl overflow-hidden">
                                    <img src="{{ asset('assets/front/ensalada_casera.png') }}" alt="Ensalada Fresca"
                                        class="w-full h-full object-cover">
                                </div>
                                <p class="mt-3 font-semibold text-gray-800">Ensalada Fresca</p>
                                <p class="text-sm text-gray-600">$800</p>
                            </div>
                        </div>
                        <div class="space-y-4 pt-8">
                            <div
                                class="bg-white p-4 rounded-3xl shadow-2xl transform hover:scale-105 transition-transform hover-card">
                                <div class="w-full h-48 bg-gray-100 rounded-2xl overflow-hidden">
                                    <img src="{{ asset('assets/front/pollo_curry_casero.png') }}" alt="Curry de Pollo"
                                        class="w-full h-full object-cover">
                                </div>
                                <p class="mt-3 font-semibold text-gray-800">Curry de Pollo</p>
                                <p class="text-sm text-gray-600">$1,500</p>
                            </div>
                            <div
                                class="bg-white p-4 rounded-3xl shadow-2xl transform hover:scale-105 transition-transform hover-card">
                                <div class="w-full h-48 bg-gray-100 rounded-2xl overflow-hidden">
                                    <img src="{{ asset('assets/front/torta_casera.png') }}" alt="Torta Casera"
                                        class="w-full h-full object-cover">
                                </div>
                                <p class="mt-3 font-semibold text-gray-800">Torta Casera</p>
                                <p class="text-sm text-gray-600">$2,000</p>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative elements -->
                    <div
                        class="absolute -top-10 -right-10 w-72 h-72 bg-gradient-to-br from-orange-300 to-pink-400 rounded-full filter blur-3xl opacity-30 animate-pulse">
                    </div>
                    <div
                        class="absolute -bottom-10 -left-10 w-72 h-72 bg-gradient-to-br from-purple-300 to-pink-400 rounded-full filter blur-3xl opacity-30 animate-pulse delay-1000">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">
                    <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                        ¿Cómo Funciona?
                    </span>
                </h2>
                <p class="text-xl text-gray-600">Simple, rápido y delicioso</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="relative">
                    <div
                        class="bg-gradient-to-br from-orange-50 to-pink-50 p-8 rounded-3xl shadow-xl hover:shadow-2xl transition-shadow">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-orange-400 to-pink-500 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6 shadow-lg">
                            1
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Explora</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Descubre cocineros cerca de ti en nuestro mapa interactivo. Filtra por tipo de comida, dietas
                            especiales y precios.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div
                        class="bg-gradient-to-br from-pink-50 to-purple-50 p-8 rounded-3xl shadow-xl hover:shadow-2xl transition-shadow">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6 shadow-lg">
                            2
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Ordena</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Elige tus platos favoritos, selecciona retiro o delivery, y paga de forma segura con
                            MercadoPago.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div
                        class="bg-gradient-to-br from-purple-50 to-orange-50 p-8 rounded-3xl shadow-xl hover:shadow-2xl transition-shadow">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-purple-600 to-orange-500 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6 shadow-lg">
                            3
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Disfruta</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Recibe tu comida casera preparada con amor. Califica tu experiencia y apoya a cocineros locales.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- For Cooks Section -->
    <div class="py-20 bg-gradient-to-br from-purple-100 via-pink-100 to-orange-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl lg:text-51xl font-bold mb-6">
                        <span
                            class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                            ¿Eres Cocinero?
                        </span>
                    </h2>
                    <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                        Convierte tu pasión por cocinar en un ingreso extra. Únete a nuestra comunidad de cocineros y llega
                        a clientes que valoran la autenticidad.
                    </p>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 border-2 border-orange-500 bg-transparent rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="#f97316" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Flexibilidad Total</h4>
                                <p class="text-gray-600">Cocina cuando quieras y decide tus precios</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 border-2 border-orange-500 bg-transparent rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="#f97316" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Comisión Baja</h4>
                                <p class="text-gray-600">Solo {{ $globalSettings['commission_rate'] ?? 15 }}% por venta, tú te quedas con el resto</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 border-2 border-orange-500 bg-transparent rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="#f97316" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Comunidad Activa</h4>
                                <p class="text-gray-600">Clientes que buscan calidad y autenticidad</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('cook.profile.create') }}"
                        class="group bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all inline-flex items-center justify-center">
                        Comenzar Ahora
                        <svg class="ml-2 w-6 h-6 transition-transform group-hover:translate-x-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6">
                            </path>
                        </svg>
                    </a>
                </div>

                <div class="relative">
                    <div class="bg-white p-2 rounded-3xl shadow-2xl">
                        <div
                            class="aspect-video bg-gradient-to-br from-purple-200 via-pink-200 to-orange-200 rounded-2xl flex items-center justify-center text-8xl">
                            <video class="w-full h-full rounded-2xl" poster="{{ asset('assets/front/cubierta.webp') }}"
                                width="100%" height="auto" controls>
                                <source src="{{ asset('assets/video/presentation.mp4') }}" type="video/mp4">
                                Tu navegador no soporta la etiqueta de video.
                            </video>
                        </div>
                    </div>
                    <div
                        class="absolute -bottom-6 -right-6 w-48 h-48 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full filter blur-3xl opacity-40">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Final -->
    <div class="py-20 bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                ¿Listo para Probar Sabores Auténticos?
            </h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Únete a miles de personas que ya disfrutan de comida casera preparada con pasión por cocineros locales.
            </p>
            <a href="{{ route('marketplace.catalog') }}"
                class="bg-white border-2 border-orange-500 text-orange-600 px-8 py-4 rounded-2xl font-bold text-lg shadow-lg hover:bg-purple-50 transition-all inline-flex items-center justify-center">
                Explorar Ahora
            </a>
        </div>
    </div>

    <!-- For Drivers Section -->
    <div class="py-20 bg-gradient-to-br from-purple-100 via-pink-100 to-orange-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="relative order-2 lg:order-1">
                    <div class="bg-gray-50 p-2 rounded-3xl shadow-2xl relative z-10">
                        <div
                            class="aspect-video bg-gradient-to-br from-blue-200 via-indigo-200 to-cyan-200 rounded-2xl flex items-center justify-center text-8xl">
                            <video class="w-full h-full rounded-2xl"
                                poster="{{ asset('assets/front/delivery_poster.webp') }}" width="100%" height="auto"
                                controls>
                                <source src="{{ asset('assets/video/cocinarte-repartidores.mp4') }}" type="video/mp4">
                                Tu navegador no soporta la etiqueta de video.
                            </video>
                        </div>
                    </div>
                    <div
                        class="absolute -top-6 -left-6 w-48 h-48 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-full filter blur-3xl opacity-30">
                    </div>
                </div>

                <div class="order-1 lg:order-2">
                    <h2 class="text-4xl lg:text-51xl font-bold mb-6">
                        <span
                            class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                            ¿Quieres ser Repartidor?
                        </span>
                    </h2>
                    <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                        Únete a nuestra red de repartidores y ayuda a llevar el sabor de lo casero a cada hogar. Maneja tus
                        tiempos y genera ingresos con total libertad.
                    </p>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 border-2 border-orange-500 bg-transparent rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Ganancias Directas</h4>
                                <p class="text-gray-600">El 100% del costo de envío es para ti. Sin comisiones extras.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 border-2 border-orange-500 bg-transparent rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Flexibilidad Total</h4>
                                <p class="text-gray-600">Trabaja cuando quieras. Tú decides tu disponibilidad y zonas.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 border-2 border-orange-500 bg-transparent rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Pagos Seguros</h4>
                                <p class="text-gray-600">Recibe tus pagos semanalmente de forma automática y transparente.
                                </p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}"
                        class="group bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all inline-flex items-center justify-center">
                        ¡Quiero Empezar!
                        <svg class="ml-2 w-6 h-6 transition-transform group-hover:translate-x-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-20 bg-white" x-data="{ activeTab: 'customers' }">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">
                    <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Preguntas Frecuentes
                    </span>
                </h2>
                <p class="text-xl text-gray-600">Todo lo que necesitas saber para empezar</p>
            </div>

            <!-- Tabs -->
            <div class="flex justify-center mb-12">
                <div class="bg-gray-100 p-1 rounded-xl inline-flex flex-wrap justify-center gap-1">
                    <button @click="activeTab = 'customers'"
                        :class="{ 'bg-white text-orange-600 shadow-sm': activeTab === 'customers', 'text-gray-500 hover:text-gray-700': activeTab !== 'customers' }"
                        class="px-8 py-3 rounded-lg font-bold transition-all duration-200">
                        Para Comensales
                    </button>
                    <button @click="activeTab = 'cooks'"
                        :class="{ 'bg-white text-purple-600 shadow-sm': activeTab === 'cooks', 'text-gray-500 hover:text-gray-700': activeTab !== 'cooks' }"
                        class="px-8 py-3 rounded-lg font-bold transition-all duration-200">
                        Para Cocineros
                    </button>
                    <button @click="activeTab = 'drivers'"
                        :class="{ 'bg-white text-blue-600 shadow-sm': activeTab === 'drivers', 'text-gray-500 hover:text-gray-700': activeTab !== 'drivers' }"
                        class="px-8 py-3 rounded-lg font-bold transition-all duration-200">
                        Para Repartidores
                    </button>
                </div>
            </div>

            <!-- Customers FAQ -->
            <div x-show="activeTab === 'customers'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" class="max-w-3xl mx-auto space-y-6">

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-orange-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div
                                class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center text-orange-600">
                                🛒
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Cómo realizo un pedido?</h3>
                            <p class="text-gray-600">Es muy simple: ingresa tu dirección para ver cocineros cercanos, elige
                                tus
                                platos favoritos y selecciona si prefieres delivery o retiro. El pago se realiza de forma
                                segura a
                                través de la plataforma.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-orange-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center text-pink-600">
                                🛡️
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Es seguro comer comida casera?</h3>
                            <p class="text-gray-600">Absolutamente. Todos nuestros cocineros pasan por un proceso de
                                verificación y
                                validación de identidad. Además, el sistema de reseñas te permite ver la experiencia de
                                otros
                                comensales.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-orange-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div
                                class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-purple-600">
                                💳
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Qué métodos de pago aceptan?</h3>
                            <p class="text-gray-600">Aceptamos todas las tarjetas de crédito, débito y dinero en cuenta a
                                través de
                                MercadoPago, garantizando la seguridad de tu transacción.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cooks FAQ -->
            <div x-show="activeTab === 'cooks'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" class="max-w-3xl mx-auto space-y-6"
                style="display: none;">

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-purple-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                💲
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Cómo y cuándo cobro mis ventas?</h3>
                            <p class="text-gray-600">Los pagos se procesan a través de MercadoPago. El dinero de tus ventas
                                se acredita en tu cuenta una vez que el pedido ha sido entregado y confirmado. Puedes
                                retirar tu dinero a tu cuenta bancaria cuando quieras.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-purple-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                📊
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Cómo funcionan las comisiones?</h3>
                            <p class="text-gray-600">Cocinarte cobra una comisión del <strong>{{ $globalSettings['commission_rate'] ?? 15 }}%</strong> sobre el valor
                                total de cada venta realizada. Esta comisión cubre los costos de procesamiento de pagos,
                                mantenimiento de la plataforma y publicidad para traerte más clientes.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-purple-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div
                                class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                                ⭐
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Por qué son importantes las reseñas?</h3>
                            <p class="text-gray-600">El sistema de reputación es clave. Los cocineros con mejores
                                calificaciones (4 y 5 estrellas) aparecen primero en los resultados de búsqueda y generan
                                más confianza. ¡Ofrece un excelente servicio y comida deliciosa para destacar!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Drivers FAQ -->
            <div x-show="activeTab === 'drivers'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" class="max-w-3xl mx-auto space-y-6"
                style="display: none;">

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-blue-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                🚲
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Cómo me registro como repartidor?</h3>
                            <p class="text-gray-600">Es muy simple. Haz clic en "Registrarse" y selecciona el rol de
                                repartidor. Deberás completar tu perfil, subir una foto de tu ID y documentación de tu
                                vehículo. Una vez validado por nuestro equipo, ¡podrás empezar a recibir pedidos!</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-blue-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div
                                class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                                💰
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Cuánto puedo ganar y cómo cobro?</h3>
                            <p class="text-gray-600">Tus ganancias dependen de la cantidad de entregas que realices. El
                                costo
                                del envío es para ti íntegramente. Cobras a través de MercadoPago una vez que el pedido es
                                marcado como entregado.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 hover:bg-blue-50 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1 mr-4">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center text-red-600">
                                📋
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-2">¿Cuáles son las normas de la plataforma?</h3>
                            <p class="text-gray-600">La puntualidad y el buen trato son fundamentales. Debes contar con un
                                bolso térmico para garantizar la temperatura de los alimentos y seguir las indicaciones de
                                higiene necesarias para el transporte de comida.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    @if(isset($reviews) && $reviews->count() > 0)
        <div class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">
                        <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                            Lo que dicen nuestros comensales
                        </span>
                    </h2>
                    <p class="text-xl text-gray-600">Experiencias reales de sabores auténticos</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($reviews as $review)
                        <div
                            class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow h-full flex flex-col justify-between">
                            <div>
                                <div class="flex items-center mb-4">
                                    @if($review->customer->profile_photo_path)
                                        <img src="{{ asset('uploads/' . $review->customer->profile_photo_path) }}"
                                            alt="{{ $review->customer->name }}"
                                            class="w-12 h-12 rounded-full object-cover mr-4 border-2 border-orange-100">
                                    @else
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-orange-100 to-pink-100 rounded-full flex items-center justify-center mr-4 text-orange-600 font-bold text-lg">
                                            {{ substr($review->customer->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-bold text-gray-800">{{ $review->customer->name }}</h4>
                                        <div class="text-yellow-400 text-sm">
                                            @for($i = 0; $i < 5; $i++)
                                                @if($i < $review->rating) ★ @else ☆ @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <p class="text-gray-600 italic mb-4">"{{ $review->comment }}"</p>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-4">
                                    <span class="text-xs text-gray-500">
                                        Pidió a <a href="{{ route('marketplace.cook.profile', $review->cook_id) }}"
                                            class="text-purple-600 font-semibold hover:underline">{{ $review->cook->user->name }}</a>
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection