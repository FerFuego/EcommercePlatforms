<div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden">
    @if(auth()->user()->is_suspended)
        <div class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
        </div>
    @endif
    <h3 class="text-xl font-bold mb-4">
        Acciones Rápidas
    </h3>
    <div class="space-y-3 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none' : '' }}">
        <a href="{{ route('cook.dashboard') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Dashboard
        </a>
        <a href="{{ route('cook.orders.index') }}"
            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Ver Pedidos
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
            class="block bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center mt-3">
            Mi Suscripción
        </a>
        <a href="{{ route('cook.profile.edit') }}"
            class="block bg-gradient-to-r from-gray-500 to-gray-700 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
            Configuración
        </a>
    </div>
</div>
