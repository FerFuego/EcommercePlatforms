<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Cocinarte')</title>
    <meta name="facebook-domain-verification" content="g272z4he3on44wlxw6z3kgt5b51w4u" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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

    <!-- Google reCAPTCHA v3 -->
    @if(config('services.recaptcha.site_key') && \App\Models\Setting::get('recaptcha_enabled', '0') == '1')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            window.getRecaptchaToken = function (action = 'admin_action') {
                return new Promise((resolve, reject) => {
                    if (typeof grecaptcha === 'undefined') {
                        reject('reCAPTCHA no está cargado');
                        return;
                    }
                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: action })
                            .then(function (token) {
                                resolve(token);
                            });
                    });
                });
            };
        </script>
    @else
        <script>
            window.getRecaptchaToken = function (action = 'admin_action') {
                return Promise.resolve('bypass');
            };
        </script>
    @endif
</head>

<body class="bg-gray-50 min-h-screen font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-200">
            <!-- Logo -->
            <div class="flex items-center justify-center h-20 border-b border-gray-200">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('assets/front/logo-8.webp') }}" alt="Cocinarte Logo" class="h-12 w-auto mb-2">
                    <span
                        class="text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Admin</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

                @php
                    $navPendingCooks = \App\Models\Cook::with('user')->where('is_approved', false)->latest()->take(10)->get();
                    $navPendingDrivers = \App\Models\DeliveryDriver::with('user')->where('is_approved', false)->latest()->take(10)->get();
                    $navTotalPending = \App\Models\Cook::where('is_approved', false)->count() + \App\Models\DeliveryDriver::where('is_approved', false)->count();
                @endphp

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                {{-- Usuarios --}}
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="font-medium">Usuarios</span>
                    </div>
                    @if($navTotalPending > 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $navTotalPending }}
                        </span>
                    @endif
                </a>

                {{-- Pedidos --}}
                <a href="{{ route('admin.orders.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.orders.*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"" 
                        fill=" none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <span class="font-medium">Pedidos</span>
                </a>

                {{-- Estadisticas --}}
                <a href="{{ route('admin.statistics') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.statistics') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.statistics') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <span class="font-medium">Estadisticas</span>
                </a>

                {{-- Configuración --}}
                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.settings.*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                        </path>
                    </svg>
                    <span class="font-medium">Configuración</span>
                </a>

                {{-- Suscripciones --}}
                <div class="mb-1">
                    <a href="{{ route('admin.subscription-plans.index') }}"
                        class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.subscription-plans.*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="font-medium">Planes de Suscripción</span>
                    </a>
                </div>

                <div class="mb-1">
                    <a href="{{ route('admin.subscription-payments.index') }}"
                        class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.subscription-payments.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.subscription-payments.*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">Pagos y Recaudación</span>
                    </a>
                </div>

                {{-- Feedback --}}
                <div class="mb-1">
                    <a href="{{ route('admin.feedback.index') }}"
                        class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.feedback.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-6 h-6 mr-3 {{ request()->routeIs('admin.feedback.*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span class="font-medium">Feedback</span>
                        @php
                            $newFeedbackCount = \App\Models\Feedback::where('status', 'new')->count();
                        @endphp
                        @if($newFeedbackCount > 0)
                            <span
                                class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $newFeedbackCount }}</span>
                        @endif
                    </a>
                </div>

                {{-- More links can be added here --}}
            </div>

            <!-- User Profile & Logout -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center mb-4">
                    @if (auth()->user()->profile_photo_path)
                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                            src="{{ asset('uploads/' . auth()->user()->profile_photo_path) }}"
                            alt="{{ auth()->user()->name }}" />
                    @else
                        <div
                            class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Administrador</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Admin Topbar Header -->
            <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between shadow-sm z-30">
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="md:hidden text-gray-500 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-sm font-bold text-gray-700 hidden sm:inline-block">Panel Administrador</span>
                </div>

                <!-- Moderation & Revision Combo Dropdown -->
                <div class="flex items-center space-x-4">
                    <div x-data="{ open: false, tab: 'all' }" class="relative">
                        <!-- Trigger Combo Button -->
                        <button 
                            @click="open = !open" 
                            type="button" 
                            class="relative flex items-center space-x-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 border border-gray-200 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-sm"
                            :class="{ 'ring-2 ring-purple-500 border-purple-500 bg-purple-50/50': open }"
                        >
                            <div class="relative">
                                <svg class="w-5 h-5 {{ $navTotalPending > 0 ? 'text-amber-500 animate-pulse' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($navTotalPending > 0)
                                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                    </span>
                                @endif
                            </div>
                            <span class="text-gray-700 hidden sm:inline">Moderación / Revisión</span>
                            @if($navTotalPending > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
                                    {{ $navTotalPending }}
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    0 pendientes
                                </span>
                            @endif
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu Panel -->
                        <div 
                            x-show="open" 
                            @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 transform -translate-y-2"
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
                            style="display: none;"
                        >
                            <!-- Dropdown Header -->
                            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-4 py-3 text-white flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <h4 class="font-bold text-sm">Usuarios en Moderación</h4>
                                </div>
                                <span class="bg-white/20 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">
                                    {{ $navTotalPending }} pendientes
                                </span>
                            </div>

                            <!-- Tabs Filter -->
                            <div class="flex border-b border-gray-100 bg-gray-50 p-1.5 space-x-1 text-xs font-semibold text-gray-600">
                                <button type="button" @click="tab = 'all'" :class="{ 'bg-white text-purple-700 shadow-sm': tab === 'all' }" class="flex-1 py-1.5 rounded-lg transition text-center">
                                    Todos ({{ $navTotalPending }})
                                </button>
                                <button type="button" @click="tab = 'cooks'" :class="{ 'bg-white text-orange-600 shadow-sm': tab === 'cooks' }" class="flex-1 py-1.5 rounded-lg transition text-center">
                                    👨‍🍳 Cocineros ({{ $navPendingCooks->count() }})
                                </button>
                                <button type="button" @click="tab = 'drivers'" :class="{ 'bg-white text-blue-600 shadow-sm': tab === 'drivers' }" class="flex-1 py-1.5 rounded-lg transition text-center">
                                    🛵 Repartidores ({{ $navPendingDrivers->count() }})
                                </button>
                            </div>

                            <!-- List Content -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                                @if($navTotalPending === 0)
                                    <div class="p-6 text-center text-gray-500">
                                        <div class="text-3xl mb-2">🎉</div>
                                        <p class="font-semibold text-sm">¡Sin pendientes de revisión!</p>
                                        <p class="text-xs text-gray-400 mt-1">Todos los usuarios están al día y procesados.</p>
                                    </div>
                                @else
                                    <!-- Pending Cooks -->
                                    @foreach($navPendingCooks as $cook)
                                        <div x-show="tab === 'all' || tab === 'cooks'" class="p-3.5 hover:bg-gray-50 transition flex items-center justify-between">
                                            <div class="flex items-center space-x-3 min-w-0">
                                                <div class="shrink-0">
                                                    @if($cook->user && $cook->user->profile_photo_path)
                                                        <img src="{{ asset('uploads/' . $cook->user->profile_photo_path) }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                                    @else
                                                        <div class="w-9 h-9 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold text-sm">
                                                            {{ substr($cook->user->name ?? 'C', 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $cook->user->name ?? 'Cocinero' }}</p>
                                                    <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                        <span class="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded font-semibold">Cocinero</span>
                                                        <span>• {{ $cook->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('admin.users.index', ['status' => 'pending_cooks', 'search' => $cook->user->name ?? '']) }}" 
                                               class="ml-2 px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold transition shrink-0">
                                                Revisar
                                            </a>
                                        </div>
                                    @endforeach

                                    <!-- Pending Drivers -->
                                    @foreach($navPendingDrivers as $driver)
                                        <div x-show="tab === 'all' || tab === 'drivers'" class="p-3.5 hover:bg-gray-50 transition flex items-center justify-between">
                                            <div class="flex items-center space-x-3 min-w-0">
                                                <div class="shrink-0">
                                                    @if($driver->profile_photo)
                                                        <img src="{{ asset('uploads/' . $driver->profile_photo) }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                                    @elseif($driver->user && $driver->user->profile_photo_path)
                                                        <img src="{{ asset('uploads/' . $driver->user->profile_photo_path) }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                                    @else
                                                        <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">
                                                            {{ substr($driver->user->name ?? 'R', 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $driver->user->name ?? 'Repartidor' }}</p>
                                                    <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                        <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-semibold">Repartidor</span>
                                                        <span>• {{ $driver->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('admin.users.index', ['status' => 'pending_drivers', 'search' => $driver->user->name ?? '']) }}" 
                                               class="ml-2 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition shrink-0">
                                                Revisar
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Dropdown Footer -->
                            <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                                <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="text-xs font-bold text-purple-600 hover:text-purple-800 transition">
                                    Ver todos en Gestión de Usuarios →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Mobile Menu Dropdown (Simplified for now) -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-200">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.users.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">Usuarios</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">Cerrar
                            Sesión</button>
                    </form>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
</body>

</html>