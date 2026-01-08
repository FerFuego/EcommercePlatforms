<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $globalSettings['site_name'] ?? 'Cocinarte') -
        {{ $globalSettings['meta_title'] ?? 'Comida Casera' }}
    </title>
    <meta name="description" content="{{ $globalSettings['meta_description'] ?? 'La mejor comida casera.' }}">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/chatbot.css', 'resources/js/chatbot.js'])

    <script>
        window.isUserAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
    </script>


    <!-- Leaflet CSS para mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gradient-to-br from-orange-50 via-pink-50 to-purple-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white/90 backdrop-blur-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('assets/front/logo-8.webp') }}" alt="Logo" class="h-16 w-100">
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center space-x-8">

                    @auth
                        @if(auth()->user()->isCook())
                            <a href="{{ route('cook.dashboard') }}"
                                class="text-gray-700 hover:text-purple-600 font-medium transition-colors">
                                Mi Cocina
                            </a>
                        @endif

                        @if(auth()->user()->isDeliveryDriver())
                            <a href="{{ route('delivery-driver.dashboard') }}"
                                class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                🚴 Mi Panel
                            </a>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                Admin
                            </a>
                        @endif


                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('marketplace.catalog') }}"
                                class="text-gray-700 hover:text-orange-600 font-medium transition-colors">
                                Explorar
                            </a>
                            <a href="{{ route('orders.my') }}"
                                class="text-gray-700 hover:text-pink-600 font-medium transition-colors flex items-center">
                                <span>Mis Pedidos</span>
                                @php
                                    $pendingCount = auth()->user()->orders()->whereIn('status', ['awaiting_cook_acceptance', 'preparing'])->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span
                                        class="ml-2 bg-gradient-to-r from-orange-500 to-pink-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingCount }}</span>
                                @endif
                            </a>

                            <a href="{{ route('favorites.index') }}"
                                class="text-gray-700 hover:text-red-600 font-medium transition-colors">
                                Favoritos
                            </a>

                            <a href="{{ route('cart.index') }}"
                                class="relative text-gray-700 hover:text-orange-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                @php
                                    $cart = session()->get('cart', []);
                                @endphp
                                @if(count($cart) > 0)
                                    <span
                                        class="absolute -top-2 -right-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ count($cart) }}</span>
                                @endif
                            </a>
                        @endif


                        <!-- User Menu -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-purple-600">
                                @if (auth()->user()->profile_photo_path)
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-purple-200"
                                        src="{{ asset('uploads/' . auth()->user()->profile_photo_path) }}"
                                        alt="{{ auth()->user()->name }}" />
                                @else
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </button>
                            <div
                                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-2">
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="block px-4 py-2 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-pink-50 font-semibold text-purple-600">
                                            Panel de Admin
                                        </a>
                                    @elseif(auth()->user()->isCook())
                                        <a href="{{ route('cook.dashboard') }}"
                                            class="block px-4 py-2 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-pink-50 font-semibold text-purple-600">
                                            Panel de Cocinero
                                        </a>
                                    @elseif(auth()->user()->isDeliveryDriver())
                                        <a href="{{ route('delivery-driver.dashboard') }}"
                                            class="block px-4 py-2 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-pink-50 font-semibold text-blue-600">Panel
                                            de Repartidor
                                        </a>
                                    @endif

                                    @if(auth()->user()->isCustomer())
                                        <a href="{{ route('favorites.index') }}"
                                            class="block px-4 py-2 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-pink-50">
                                            Mis Favoritos
                                        </a>
                                    @endif

                                    <a href="{{ route('profile.edit') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-pink-50">Mi
                                        Perfil</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-pink-50">
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-purple-600 font-medium transition-colors">
                            Ingresar
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            Registrarse
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" type="button"
                        class="text-gray-700 hover:text-purple-600 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
            class="hidden md:hidden bg-white border-t border-gray-100 shadow-xl overflow-hidden transition-all duration-300">
            <div class="px-4 pt-2 pb-6 space-y-1">
                @auth
                    @if(auth()->user()->isCook())
                        <a href="{{ route('cook.dashboard') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-xl font-medium transition-colors">
                            👨‍🍳 Mi Cocina
                        </a>
                    @endif

                    @if(auth()->user()->isDeliveryDriver())
                        <a href="{{ route('delivery-driver.dashboard') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-xl font-medium transition-colors">
                            🚴 Mi Panel
                        </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-xl font-medium transition-colors">
                            🛡️ Admin
                        </a>
                    @endif

                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('marketplace.catalog') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-orange-50 rounded-xl font-medium transition-colors">
                            🔍 Explorar
                        </a>
                        <a href="{{ route('orders.my') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-pink-50 rounded-xl font-medium transition-colors flex items-center justify-between">
                            <span>📦 Mis Pedidos</span>
                            @if($pendingCount > 0)
                                <span
                                    class="bg-gradient-to-r from-orange-500 to-pink-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('cart.index') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-orange-50 rounded-xl font-medium transition-colors flex items-center justify-between">
                            <span>🛒 Carrito</span>
                            @if(count($cart) > 0)
                                <span
                                    class="bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ count($cart) }}</span>
                            @endif
                        </a>
                    @endif

                    <div class="border-t border-gray-100 my-2 pt-2">
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors">
                            👤 Mi Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left block px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl font-medium transition-colors">
                                🚪 Cerrar Sesión
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-3 pt-2">
                        <a href="{{ route('marketplace.catalog') }}"
                            class="block px-4 py-3 text-center text-gray-700 font-bold hover:bg-gray-50 rounded-xl transition-all">
                            Explorar
                        </a>
                        <a href="{{ route('login') }}"
                            class="block px-4 py-3 text-center text-gray-700 font-bold hover:bg-gray-50 rounded-xl transition-all">
                            Ingresar
                        </a>
                        <a href="{{ route('register') }}"
                            class="block px-4 py-4 text-center bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white rounded-xl font-bold shadow-lg">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div
                class="bg-gradient-to-r from-green-400 to-emerald-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div
                class="bg-gradient-to-r from-red-400 to-pink-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-900 via-purple-900 to-pink-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="col-span-2 mr-20 pr-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('assets/front/logo-w.webp') }}" alt="Cocinarte Logo" class="h-16 w-100 mb-2">
                    </a>
                    <p class="text-gray-300">Conectando cocineros caseros con comensales que buscan autenticidad y
                        sabor.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Para Clientes</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="{{ route('marketplace.catalog') }}"
                                class="hover:text-orange-400 transition">Explorar Cocineros</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition">Cómo Funciona</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Para Cocineros</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="{{ route('register') }}" class="hover:text-pink-400 transition">Registrarse</a>
                        </li>
                        <li><a href="#" class="hover:text-pink-400 transition">Beneficios</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Para Repartidores</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="{{ route('register') }}" class="hover:text-pink-400 transition">Registrarse</a>
                        </li>
                        <li><a href="#" class="hover:text-pink-400 transition">Beneficios</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contacto</h4>
                    <p class="text-gray-300">Bell Ville, Córdoba</p>
                    <p class="text-gray-300">info@cocinarte.app</p>
                </div>
            </div>
            <div class="border-t border-white mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Cocinarte. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Cart Button -->
    <a href="{{ route('cart.index') }}" id="floating-cart-btn"
        class="fixed bottom-8 right-8 bg-gradient-to-r from-orange-500 to-pink-600 text-white p-3 rounded-full shadow-2xl hover:scale-110 transition-transform z-50 {{ count(session('cart', [])) > 0 ? 'flex' : 'hidden' }} items-center justify-center">
        <div class="relative">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span id="floating-cart-count"
                class="absolute -top-2 -right-2 bg-white text-pink-600 text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-pink-100">
                {{ count(session('cart', [])) }}
            </span>
        </div>
    </a>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-24 right-8 z-[100] space-y-3 pointer-events-none"></div>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuBtn = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            const floatingBtn = document.getElementById('floating-cart-btn');
            const floatingCount = document.getElementById('floating-cart-count');

            // Intercept all forms that post to cart.add
            document.body.addEventListener('submit', function (e) {
                if (e.target.tagName === 'FORM' && e.target.action.includes('cart/add')) {
                    e.preventDefault();

                    const form = e.target;
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Agregando...
                    `;

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update floating button
                                floatingCount.textContent = data.cart_count;
                                floatingBtn.classList.remove('hidden');
                                floatingBtn.classList.add('flex');

                                // Animation
                                floatingBtn.classList.add('scale-125');
                                setTimeout(() => floatingBtn.classList.remove('scale-125'), 200);

                                // Show toast
                                window.showToast(data.message || 'Producto agregado correctamente', 'success');
                            } else {
                                window.showToast(data.message || 'Error al agregar producto', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.showToast('Error de conexión', 'error');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                }
            });

            // Toast Function
            window.showToast = function (message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');

                const bgColor = type === 'success' ? 'bg-white' : 'bg-red-50';
                const textColor = type === 'success' ? 'text-gray-800' : 'text-red-800';
                const icon = type === 'success' ? '✅' : '❌';

                toast.className = `transform translate-x-full transition-all duration-300 ease-out flex items-center p-4 min-w-[300px] ${bgColor} rounded-2xl shadow-xl pointer-events-auto z-[100] mt-2`;
                toast.innerHTML = `
                    <span class="text-2xl mr-3">${icon}</span>
                    <div class="flex-1">
                        <p class="font-bold text-sm ${textColor}">${message}</p>
                    </div>
                `;

                container.appendChild(toast);

                // Entrance animation
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full');
                });

                // Removal
                setTimeout(() => {
                    toast.classList.add('opacity-0', '-translate-y-2');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            };

            // Toggle Favorite Function
            window.toggleFavorite = function (event, cookId) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                fetch(`/favorites/toggle/${cookId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const icons = document.querySelectorAll(`#heart-icon-${cookId}`);
                        icons.forEach(icon => {
                            if (data.status === 'added') {
                                icon.classList.remove('text-white', 'fill-none');
                                icon.classList.add('text-red-500', 'fill-current');
                            } else {
                                icon.classList.remove('text-red-500', 'fill-current');
                                icon.classList.add('text-white', 'fill-none');
                            }
                        });

                        window.showToast(data.message);
                    })
                    .catch(error => {
                        console.error('Error toggling favorite:', error);
                        window.showToast('Error al procesar favoritos', 'error');
                    });
            };
        });
    </script>

    @stack('scripts')

    @include('partials.chatbot')

    <!-- Login Required Modal -->
    <div id="login-required-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300"
            onclick="hideLoginModal()"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
            id="login-modal-content">
            <div class="p-8 text-center">
                <!-- Logo -->
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('assets/front/logo-8.webp') }}" alt="Cocinarte" class="h-20 w-auto">
                </div>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Casi listo para ordenar!</h3>
                <p class="text-gray-600 mb-8">Para disfrutar de los mejores sabores caseros, primero debes ingresar a tu
                    cuenta o registrarte.</p>

                <div class="space-y-3">
                    <a href="{{ route('login') }}"
                        class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}"
                        class="block w-full bg-gray-50 text-gray-700 py-4 rounded-2xl font-bold text-lg border-2 border-gray-100 hover:bg-gray-100 transition-all">
                        Crear una Cuenta
                    </a>
                </div>

                <button onclick="hideLoginModal()"
                    class="mt-6 text-sm text-gray-400 hover:text-gray-600 font-medium transition-colors">
                    Quizás más tarde
                </button>
            </div>
        </div>
    </div>

    <script>
        function showLoginModal() {
            const modal = document.getElementById('login-required-modal');
            const content = document.getElementById('login-modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function hideLoginModal() {
            const modal = document.getElementById('login-required-modal');
            const content = document.getElementById('login-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    </script>
    <!-- Toast de Notificaciones Push -->
    <div x-data="{ 
            notifications: [], 
            add(detail) {
                const id = Date.now();
                this.notifications.push({ id, ...detail });
                setTimeout(() => this.remove(id), 5000);
            },
            remove(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }
         }" x-on:push-notification.window="add($event.detail)"
        class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm w-full pointer-events-none">

        <template x-for="n in notifications" :key="n.id">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                class="bg-white dark:bg-gray-800 border border-orange-500 rounded-lg shadow-xl p-4 pointer-events-auto flex items-start gap-4 cursor-pointer"
                x-on:click="if(n.data?.url) window.location.href = n.data.url">

                <div class="flex-shrink-0" x-show="n.icon">
                    <img :src="n.icon" class="w-10 h-10 rounded-full border border-gray-200" alt="">
                </div>

                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 dark:text-white text-sm" x-text="n.title"></h4>
                    <p class="text-gray-600 dark:text-gray-400 text-xs mt-1" x-text="n.body"></p>
                </div>

                <button x-on:click.stop="remove(n.id)" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

</body>

</html>