<div x-data="{ open: false }" class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden transition-all duration-200">
    @if(auth()->user()->is_suspended)
        <div class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
        </div>
    @endif

    <!-- Mobile toggle header -->
    <button 
        type="button"
        @click="open = !open" 
        class="w-full flex items-center justify-between text-left focus:outline-none lg:cursor-default"
        :aria-expanded="open"
    >
        <div class="flex items-center space-x-2">
            <svg class="w-6 h-6 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-800">
                Acciones Rápidas
            </h3>
        </div>
        <div class="flex items-center space-x-2 lg:hidden">
            <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full border border-orange-200" x-text="open ? 'Ocultar' : 'Ver menú'">
                Ver menú
            </span>
            <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    <!-- Nav Links -->
    <div 
        class="mt-4 hidden lg:block space-y-3 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none' : '' }}"
        :class="{ '!block': open }"
    >
        <a href="{{ route('cook.dashboard') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Dashboard
        </a>
        <a href="{{ route('cook.orders.index') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Ver Pedidos
        </a>
        <a href="{{ route('cook.prep.index') }}"
            class="block bg-gradient-to-r from-purple-600 to-indigo-700 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            👨‍🍳 Hoja de Producción
        </a>
        <a href="{{ route('cook.dishes.index') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Mis Platos
        </a>
        <a href="{{ route('cook.dishes.create') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Nuevo Plato
        </a>
        <a href="{{ route('cook.tutorials') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Ayuda y Tutoriales
        </a>
        <a href="{{ route('cook.analytics') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-center text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Estadísticas Avanzadas
        </a>
        <a href="{{ route('cook.broadcasts.index') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-center text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Marketing / Ofertas
        </a>
        <a href="{{ route('cook.subscription.index') }}"
            class="block bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Mi Suscripción
        </a>
        <a href="{{ route('cook.profile.edit') }}"
            class="block bg-gradient-to-r from-gray-500 to-gray-700 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Configuración
        </a>
    </div>
</div>
