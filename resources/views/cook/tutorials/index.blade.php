@extends('layouts.app')

@section('title', 'Ayuda y Tutoriales - Cocinero')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    <span
                        class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Ayuda y Tutoriales
                    </span>
                </h1>
                <p class="text-gray-600">Guías prácticas para profesionalizar tu cocina en casa</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions (Sidebar) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden sticky top-28">
                    @if(auth()->user()->is_suspended)
                        <div
                            class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
                            <span
                                class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
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
                        <a href="{{ route('cook.profile.edit') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Configuración
                        </a>
                        <a href="{{ route('cook.tutorials') }}"
                            class="block bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Ayuda y Tutoriales
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Hero Section -->
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                    <div class="h-64 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=2070&auto=format&fit=crop"
                            alt="Cocina profesional" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8">
                            <span
                                class="bg-pink-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">Guía
                                Maestra</span>
                            <h2 class="text-3xl font-bold text-white">Guía de cómo cocinar en tu casa para vender</h2>
                        </div>
                    </div>

                    <div class="p-8 prose prose-orange max-w-none">
                        <p class="text-lg text-gray-600 leading-relaxed mb-6">Convertir tu cocina casera en un pequeño
                            negocio de comida es un paso emocionante. Sin embargo, cocinar para clientes requiere estándares
                            de calidad, higiene y organización mucho más altos que cocinar para la familia. Aquí te dejamos
                            las claves del éxito.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <!-- Card 1 -->
                            <div class="bg-orange-50 rounded-2xl p-6 border-l-4 border-orange-500">
                                <div class="text-3xl mb-3">🍅</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Selección de Ingredientes Frescos</h3>
                                <p class="text-sm text-gray-700">El sabor comienza en el mercado. Selecciona verduras firmes
                                    y de colores vivos. Evita carnes pálidas o con exceso de líquido en su empaque. Compra
                                    localmente cuando sea posible; los ingredientes de temporada no solo son más baratos,
                                    sino que tienen mejor sabor y textura.</p>
                            </div>

                            <!-- Card 2 -->
                            <div class="bg-red-50 rounded-2xl p-6 border-l-4 border-red-500">
                                <div class="text-3xl mb-3">🦠</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Contaminación Cruzada</h3>
                                <p class="text-sm text-gray-700">Nunca utilices la misma tabla de cortar o cuchillo para
                                    carnes crudas y vegetales (a menos que los laves y desinfectes entre usos). Lo ideal es
                                    tener tablas de diferentes colores: roja para carnes rojas crudas, amarilla para pollo
                                    crudo, y verde para verduras listas para consumir.</p>
                            </div>

                            <!-- Card 3 -->
                            <div class="bg-blue-50 rounded-2xl p-6 border-l-4 border-blue-500">
                                <div class="text-3xl mb-3">🧼</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Higiene y Utensilios</h3>
                                <p class="text-sm text-gray-700">El lavado de manos debe ser constante: antes de cocinar,
                                    después de ir al baño, de tocar basura o de manipular alimentos crudos. Mantén el
                                    cabello recogido. Desinfecta tus mesadas con una solución de agua y lavandina (1
                                    cucharada por litro de agua) antes y después de cada turno de cocina.</p>
                            </div>

                            <!-- Card 4 -->
                            <div class="bg-purple-50 rounded-2xl p-6 border-l-4 border-purple-500">
                                <div class="text-3xl mb-3">📸</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Presentación de los Platos</h3>
                                <p class="text-sm text-gray-700">¡La comida entra por los ojos! Un cliente que recibe un
                                    plato desordenado asumirá que sabe mal. Utiliza recipientes adecuados al tamaño de la
                                    porción para que la comida no se mueva durante el delivery. Limpia los bordes del envase
                                    antes de cerrarlo y añade toques de color (como hierbas frescas) al final.</p>
                            </div>
                        </div>

                        <!-- Temperatura y Preservación -->
                        <div class="bg-gray-50 rounded-3xl p-8 mb-8 border border-gray-100 shadow-inner">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <span class="text-3xl mr-3">🌡️</span> Preservación y Temperaturas
                            </h3>
                            <p class="text-gray-600 mb-4">La zona de peligro para los alimentos está entre los <strong>5°C y
                                    los 60°C</strong>. En este rango, las bacterias se multiplican rápidamente.</p>

                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700"><strong>Alimentos Calientes:</strong> Deben cocinarse y
                                        mantenerse por encima de los 65°C hasta el momento de empaquetar. Las carnes
                                        (especialmente el pollo y cerdo) deben alcanzar una temperatura interna de
                                        74°C.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700"><strong>Alimentos Fríos:</strong> Deben mantenerse en
                                        refrigeración a 4°C o menos. Si preparas ensaladas o postres, no los dejes a
                                        temperatura ambiente mientras cocinas el resto del menú.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-orange-500 mr-2 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700"><strong>Enfriamiento rápido:</strong> Nunca pongas ollas
                                        hirviendo directamente en la heladera, pero tampoco dejes la comida afuera
                                        enfriándose por más de 2 horas. Divídela en recipientes pequeños para que se enfríe
                                        rápidamente a temperatura ambiente y luego refrigera.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Calculadora de Ingredientes -->
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1590779033100-9f60a05a013d?q=80&w=2070&auto=format&fit=crop"
                            alt="Cálculo de porciones" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-purple-900/90 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8">
                            <h2 class="text-3xl font-bold text-white">Cómo calcular ingredientes y porciones</h2>
                        </div>
                    </div>

                    <div class="p-8">
                        <p class="text-gray-600 mb-6">Uno de los mayores retos al pasar de cocinar para la familia a cocinar
                            para vender es saber cómo escalar las recetas. Aquí tienes una regla de oro: <strong>Usa gramos,
                                no tazas ni cucharas.</strong> Comprar una balanza digital de cocina es la mejor inversión
                            que puedes hacer.</p>

                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200 mb-6">
                            <h4 class="font-bold text-lg text-gray-800 mb-4">Fórmula del Factor de Conversión</h4>
                            <p class="text-sm text-gray-700 mb-4">Para adaptar cualquier receta a un nuevo número de
                                porciones, usa esta fórmula matemática básica:</p>
                            <div
                                class="bg-white p-4 rounded-xl text-center font-mono text-lg font-bold text-purple-700 shadow-sm border border-gray-200 mb-4">
                                Porciones que DESEAS ÷ Porciones que RINDE = Factor de Conversión
                            </div>
                            <p class="text-sm text-gray-700">Luego, multiplicas cada ingrediente por el Factor de
                                Conversión.</p>
                        </div>

                        <h4 class="font-bold text-lg text-gray-800 mb-4">Ejemplo Práctico: Salsa de Tomate</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Receta Original -->
                            <div class="bg-white border-2 border-gray-100 rounded-xl p-5">
                                <div
                                    class="bg-gray-100 text-gray-600 font-bold px-3 py-1 rounded-lg text-sm inline-block mb-3">
                                    Receta Original (Rinde 4)</div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex justify-between border-b border-gray-100 pb-1"><span>Tomates:</span>
                                        <strong>500g</strong></li>
                                    <li class="flex justify-between border-b border-gray-100 pb-1"><span>Cebolla:</span>
                                        <strong>100g</strong></li>
                                    <li class="flex justify-between border-b border-gray-100 pb-1"><span>Ajo:</span>
                                        <strong>10g</strong></li>
                                    <li class="flex justify-between"><span>Aceite:</span> <strong>30ml</strong></li>
                                </ul>
                            </div>

                            <!-- Receta Escalada -->
                            <div class="bg-purple-50 border-2 border-purple-100 rounded-xl p-5 relative">
                                <div
                                    class="absolute -left-5 top-1/2 transform -translate-y-1/2 bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-xl shadow-lg md:block hidden">
                                    →</div>
                                <div
                                    class="bg-purple-500 text-white font-bold px-3 py-1 rounded-lg text-sm inline-block mb-3">
                                    Receta Escalada (Deseas 10)</div>
                                <p class="text-xs text-purple-700 font-semibold mb-3">Factor: 10 ÷ 4 = 2.5</p>
                                <ul class="space-y-2 text-sm text-gray-800">
                                    <li class="flex justify-between border-b border-purple-200 pb-1"><span>Tomates (500 x
                                            2.5):</span> <strong>1250g (1.25kg)</strong></li>
                                    <li class="flex justify-between border-b border-purple-200 pb-1"><span>Cebolla (100 x
                                            2.5):</span> <strong>250g</strong></li>
                                    <li class="flex justify-between border-b border-purple-200 pb-1"><span>Ajo (10 x
                                            2.5):</span> <strong>25g</strong></li>
                                    <li class="flex justify-between"><span>Aceite (30 x 2.5):</span> <strong>75ml</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-8 bg-yellow-50 rounded-xl p-5 border border-yellow-200 flex items-start">
                            <span class="text-2xl mr-3">⚠️</span>
                            <div>
                                <h5 class="font-bold text-yellow-800">Cuidado con las especias y la sal</h5>
                                <p class="text-sm text-yellow-700">El factor de conversión matemático funciona perfecto para
                                    ingredientes base (harina, vegetales, proteínas), pero <strong>NO siempre funciona de
                                        forma lineal para la sal, hierbas y condimentos picantes</strong>. Si vas a
                                    multiplicar una receta por 5, no multipliques la sal por 5 de inmediato; agrega el 70%
                                    de la sal requerida y corrige el sazón al final.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection