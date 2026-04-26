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
                @include('cook.partials.quick-actions')
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

                <!-- Guía 1: Primeros pasos -->
                <div class="bg-white rounded-3xl shadow-xl p-8 overflow-hidden relative border-t-8 border-green-500">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <span class="text-9xl">🚀</span>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4 relative z-10">1. Primeros pasos para vender comida
                        desde casa</h2>
                    <p class="text-gray-600 mb-6 text-lg relative z-10">Empezar a vender comida puede asustar un poco, ¡pero
                        todos los grandes chefs empezaron en cocinas pequeñas! Lo más importante es empezar de forma
                        inteligente.</p>

                    <ul class="space-y-4 mb-6 relative z-10">
                        <li class="flex items-start">
                            <span class="text-green-500 text-xl mr-3 mt-1">✓</span>
                            <p class="text-gray-700"><strong>¿Qué cocinar al empezar?</strong> Empieza con lo que te sale
                                mejor y más rico. Si todos aman tus empanadas, ¡vende empanadas! No intentes hacer 10 platos
                                distintos el primer día.</p>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 text-xl mr-3 mt-1">✓</span>
                            <p class="text-gray-700"><strong>Menú pequeño = Menos estrés:</strong> Es preferible ofrecer 2 o
                                3 platos excelentes que 15 mediocres. Además, comprarás menos ingredientes distintos y
                                desperdiciarás menos.</p>
                        </li>
                    </ul>
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-xl border border-blue-200">
                        <strong>💡 Tip rápido:</strong> Prueba tus platos con amigos primero y pídeles críticas honestas, no
                        solo cumplidos.
                    </div>
                </div>

                <!-- Guía 2: Cuánto cobrar -->
                <div class="bg-white rounded-3xl shadow-xl p-8 overflow-hidden relative border-t-8 border-blue-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">2. Cómo saber cuánto cobrar (sin volverte loco)</h2>
                    <p class="text-gray-600 mb-6 text-lg">Poner precio es difícil. La regla de oro al empezar es simple: "No
                        pagues por trabajar". Tienes que cubrir tus gastos y ganar algo.</p>

                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <span class="text-blue-500 text-xl mr-3 mt-1">💰</span>
                            <p class="text-gray-700"><strong>La regla del "x3":</strong> Una forma sencilla de empezar es
                                sumar el costo de TODOS tus ingredientes y el envase, y multiplicar ese número por 3.
                                <br><em>Ejemplo: Si hacer una pizza te costó $1000 en ingredientes y la caja cuesta $200
                                    (Total $1200), tu precio de venta debería rondar los $3600.</em></p>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 text-xl mr-3 mt-1">👀</span>
                            <p class="text-gray-700"><strong>Mira a la competencia:</strong> Fíjate a cuánto venden platos
                                similares en tu barrio. Si tu "precio x3" es mucho más caro que el resto, pregúntate: ¿Mi
                                plato es más grande o de mejor calidad? Si es igual, quizás debas comprar ingredientes más
                                baratos.</p>
                        </li>
                    </ul>
                    <div class="bg-red-50 text-red-800 p-4 rounded-xl border border-red-200">
                        <strong>❌ Error común:</strong> Olvidarse de cobrar el envase, la bolsa, las servilletas y el tiempo
                        que estuviste cocinando. ¡Todo cuesta dinero!
                    </div>
                </div>

                <!-- Guía 3: Calcular porciones -->
                <div class="bg-white rounded-3xl shadow-xl p-8 overflow-hidden relative border-t-8 border-yellow-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">3. Cómo calcular porciones (Si no tienes balanza)</h2>
                    <p class="text-gray-600 mb-6 text-lg">Si bien una balanza es ideal, mientras ahorras para una puedes
                        usar referencias visuales para que todas tus porciones sean iguales.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-xl text-center">
                            <div class="text-4xl mb-2">✊</div>
                            <h4 class="font-bold text-gray-800">Un Puño</h4>
                            <p class="text-sm text-gray-600">Equivale a una porción de arroz, pasta o puré (aprox. 1 taza).
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl text-center">
                            <div class="text-4xl mb-2">✋</div>
                            <h4 class="font-bold text-gray-800">Palma de la Mano</h4>
                            <p class="text-sm text-gray-600">Es el tamaño ideal para una porción de carne, pollo o pescado.
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl text-center">
                            <div class="text-4xl mb-2">🥄</div>
                            <h4 class="font-bold text-gray-800">Cucharón</h4>
                            <p class="text-sm text-gray-600">Usa el mismo cucharón (de sopa) para servir todos los guisos o
                                salsas. Ejemplo: "Cada plato lleva 2 cucharones".</p>
                        </div>
                    </div>
                    <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl border border-yellow-200">
                        <strong>👑 Consejo de oro:</strong> Si sirves porciones de distinto tamaño, el cliente que reciba la
                        más pequeña se enojará, y con el de la más grande perderás dinero. ¡Estandariza!
                    </div>
                </div>

                <!-- Guía 4: Organización -->
                <div class="bg-white rounded-3xl shadow-xl p-8 overflow-hidden relative border-t-8 border-purple-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">4. Cómo organizarte en la cocina</h2>
                    <p class="text-gray-600 mb-6 text-lg">En la cocina profesional, la organización se llama <em>Mise en
                            place</em> (todo en su lugar). Es el secreto para no colapsar cuando entran muchos pedidos.</p>

                    <ul class="space-y-4 mb-6">
                        <li class="bg-gray-50 p-4 rounded-xl">
                            <strong class="text-purple-600">ANTES de cocinar:</strong> Pica toda la cebolla, lava todas las
                            verduras, saca toda la carne de la heladera. No cocines y piques al mismo tiempo.
                        </li>
                        <li class="bg-gray-50 p-4 rounded-xl">
                            <strong class="text-purple-600">DURANTE:</strong> "Limpia mientras cocinas". Si terminaste de
                            usar la tabla, lávala. No dejes una montaña de platos para el final.
                        </li>
                        <li class="bg-gray-50 p-4 rounded-xl">
                            <strong class="text-purple-600">DESPUÉS:</strong> Deja la cocina impecable para el día
                            siguiente. Una cocina limpia atrae ganas de trabajar; una sucia, desmotiva.
                        </li>
                    </ul>
                </div>

                <!-- Guía 5: Compras y Ahorro -->
                <div class="bg-white rounded-3xl shadow-xl p-8 overflow-hidden relative border-t-8 border-teal-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">5. Compras: Cómo no desperdiciar dinero</h2>

                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <span class="text-teal-500 text-xl mr-3 mt-1">🛒</span>
                                    <p class="text-gray-700"><strong>No compres de más al principio:</strong> Es preferible
                                        quedarte sin comida y decirle a un cliente "Se agotó por hoy" a tirar kilos de
                                        mercadería que se echó a perder.</p>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-teal-500 text-xl mr-3 mt-1">🍅</span>
                                    <p class="text-gray-700"><strong>Ingredientes de temporada:</strong> En invierno el
                                        tomate es carísimo. Adapta tu menú. Haz sopas, guisos. En verano, haz ensaladas
                                        frescas.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="flex-1 bg-teal-50 p-6 rounded-2xl">
                            <h4 class="font-bold text-teal-800 mb-2">Errores Comunes de Principiantes:</h4>
                            <ul class="list-disc list-inside text-teal-700 text-sm space-y-2">
                                <li>Comprar al por mayor sin tener espacio en la heladera.</li>
                                <li>Creer que "más barato" es mejor (el queso barato no se derrite, la carne barata es
                                    dura).</li>
                                <li>Hacer las compras sin una lista escrita.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Guía 6: Presentación -->
                <div class="bg-white rounded-3xl shadow-xl p-8 overflow-hidden relative border-t-8 border-pink-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">6. Cómo presentar para vender más</h2>
                    <p class="text-gray-600 mb-6 text-lg">La gente come primero con los ojos. Si tu comida se ve como un
                        revuelto gris en una caja blanca, nadie le sacará fotos para Instagram.</p>

                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <span class="text-pink-500 text-xl mr-3 mt-1">🎨</span>
                            <p class="text-gray-700"><strong>Usa contrastes de colores:</strong> Un poco de perejil picado
                                encima de las pastas, unas tiritas de morrón rojo sobre el arroz. El color da sensación de
                                frescura.</p>
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 text-xl mr-3 mt-1">📦</span>
                            <p class="text-gray-700"><strong>El envase importa mucho:</strong> Asegúrate de que no se
                                derrame. Un sticker con tu logo o un "¡Gracias por tu compra!" escrito a mano con marcador
                                cambia toda la experiencia del cliente.</p>
                        </li>
                    </ul>
                </div>

                <!-- Guía 7: Mejorar -->
                <div class="bg-white rounded-3xl shadow-xl p-8 overflow-hidden relative border-t-8 border-indigo-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">7. Cómo mejorar mes a mes</h2>
                    <p class="text-gray-600 mb-6 text-lg">Nunca dejes de escuchar a tus clientes, ellos te dirán qué camino
                        tomar.</p>

                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <span class="text-indigo-500 text-xl mr-3 mt-1">💬</span>
                            <p class="text-gray-700"><strong>Pide feedback:</strong> Al día siguiente, envíales un WhatsApp
                                preguntando: "¿Qué tal estuvo la comida ayer?". Si hay quejas, no te enojes, agradéceles. Es
                                una oportunidad para mejorar.</p>
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-500 text-xl mr-3 mt-1">📈</span>
                            <p class="text-gray-700"><strong>Revisa tus Estadísticas:</strong> Usa tu panel de Cocinarte. Si
                                un plato no se vende en un mes, ¡quítalo del menú! Reemplázalo por algo nuevo.</p>
                        </li>
                    </ul>
                </div>

                <!-- Guía 8: Checklist Final -->
                <div class="bg-gray-900 rounded-3xl shadow-xl p-8 overflow-hidden relative text-white">
                    <h2 class="text-3xl font-bold mb-6 text-orange-400">✅ Checklist Final: Antes de entregar el pedido</h2>
                    <p class="text-gray-300 mb-6">Repasa mentalmente esta lista rápida cada vez que embolses un pedido:</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-3 bg-gray-800 p-4 rounded-xl border border-gray-700">
                            <input type="checkbox" checked disabled
                                class="w-6 h-6 text-orange-500 rounded focus:ring-orange-500 bg-gray-700 border-gray-600">
                            <span class="font-medium">¿Está a la temperatura correcta?</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-gray-800 p-4 rounded-xl border border-gray-700">
                            <input type="checkbox" checked disabled
                                class="w-6 h-6 text-orange-500 rounded focus:ring-orange-500 bg-gray-700 border-gray-600">
                            <span class="font-medium">¿Agregué las servilletas y cubiertos?</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-gray-800 p-4 rounded-xl border border-gray-700">
                            <input type="checkbox" checked disabled
                                class="w-6 h-6 text-orange-500 rounded focus:ring-orange-500 bg-gray-700 border-gray-600">
                            <span class="font-medium">¿El envase está bien cerrado y sin manchas por fuera?</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-gray-800 p-4 rounded-xl border border-gray-700">
                            <input type="checkbox" checked disabled
                                class="w-6 h-6 text-orange-500 rounded focus:ring-orange-500 bg-gray-700 border-gray-600">
                            <span class="font-medium">¿Puse el aderezo o pan que prometí?</span>
                        </div>
                    </div>

                    <p class="mt-8 text-center text-xl font-bold italic text-gray-400">"¡Si todo tiene un tick, ese pedido
                        está listo para enamorar a un cliente!"</p>
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
                                        <strong>500g</strong>
                                    </li>
                                    <li class="flex justify-between border-b border-gray-100 pb-1"><span>Cebolla:</span>
                                        <strong>100g</strong>
                                    </li>
                                    <li class="flex justify-between border-b border-gray-100 pb-1"><span>Ajo:</span>
                                        <strong>10g</strong>
                                    </li>
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