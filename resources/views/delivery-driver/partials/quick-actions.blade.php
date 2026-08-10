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
            <svg class="w-6 h-6 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-800">
                Acciones Rápidas
            </h3>
        </div>
        <div class="flex items-center space-x-2 lg:hidden">
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200" x-text="open ? 'Ocultar' : 'Ver menú'">
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
        @if($driver->is_approved)
            <a href="{{ route('delivery-driver.dashboard') }}"
                class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                Dashboard
            </a>
            <a href="{{ route('delivery-driver.orders.available') }}"
                class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                Ver Pedidos Disponibles
            </a>
            <a href="{{ route('delivery-driver.orders.index') }}"
                class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                Mis Entregas
            </a>
            <a href="{{ route('delivery-driver.earnings') }}"
                class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                Ver Ganancias
            </a>
        @endif
        <a href="{{ route('delivery-driver.profile.edit') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Editar Perfil
        </a>
    </div>
</div>
