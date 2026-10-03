@extends('layouts.app')

@section('title', 'Registrarse como Cocinero')

@push('styles')
    <!-- Leaflet CSS para mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold mb-4">
                <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                    Únete como Cocinero
                </span>
            </h1>
            <p class="text-xl text-gray-600">Comparte tu pasión por cocinar y genera ingresos</p>
        @if(auth()->check() && auth()->user()->last_rejection_reason)
            <div class="bg-red-50 border-2 border-red-300 text-red-900 p-6 mb-8 rounded-2xl shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-red-100 rounded-xl text-red-600 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-red-900 mb-1">❌ Tu solicitud anterior fue rechazada</h3>
                        <p class="text-sm text-red-700 font-semibold mb-2">Motivo expresado por el administrador:</p>
                        <div class="bg-white/80 p-4 rounded-xl border border-red-200 text-red-900 font-medium italic text-base mb-3 shadow-inner">
                            "{{ auth()->user()->last_rejection_reason }}"
                        </div>
                        <p class="text-xs text-red-600">Puedes modificar tu información y volver a enviar la postulación a continuación.</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-r-xl shadow-md" role="alert">
                <div class="flex items-center mb-2">
                    <svg class="h-5 w-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="font-bold">Hubo algunos problemas con tu solicitud:</p>
                </div>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Progress Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-black/60 z-50 hidden flex-col items-center justify-center backdrop-blur-sm transition-all duration-300">
            <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4">
                <div class="w-16 h-16 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mb-4"></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Enviando solicitud...</h3>
                <p class="text-gray-500 text-center mb-4">Estamos subiendo tus fotos y datos. Esto puede tardar unos segundos.</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                    <div id="progressBar" class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
                <p id="progressText" class="text-sm font-semibold text-purple-600">0%</p>
            </div>
        </div>

        <form id="profileForm" action="{{ route('cook.profile.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 relative">
            @csrf

            <!-- Personal Info -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span
                        class="w-10 h-10 bg-gradient-to-br from-orange-500 to-pink-600 rounded-full flex items-center justify-center text-white mr-3">1</span>
                    Información Personal
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cuéntanos sobre ti *</label>
                        <textarea name="bio" rows="4" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                            placeholder="Describe tu experiencia cocinando, tu estilo de cocina, especialidades...">{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Mínimo 100 caracteres</p>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span
                        class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white mr-3">2</span>
                    Ubicación de tu Cocina
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Dirección de la Cocina *</label>
                        <input type="text" name="address" id="address" required
                            value="{{ old('address', auth()->user()->address) }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                            placeholder="Ej: Av. Principal 123, Bell Ville">
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" name="location_lat" id="location_lat" value="{{ old('location_lat') }}">
                        <input type="hidden" name="location_lng" id="location_lng" value="{{ old('location_lng') }}">
                    </div>

                    <div id="map" class="h-[300px] w-full rounded-xl shadow-md border-2 border-gray-100 z-0"></div>

                    <button type="button" onclick="detectLocation()"
                        class="w-full bg-blue-50 text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-100 transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        📍 Detectar Mi Ubicación y Dirección
                    </button>
                    <p class="text-xs text-gray-500 text-center">Arrastra el marcador en el mapa para ajustar tu ubicación
                        exacta</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Radio de Cobertura (km) *</label>
                            <input type="number" name="coverage_radius_km" value="{{ old('coverage_radius_km', 10) }}"
                                required min="1" max="50"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Horario Apertura</label>
                            <input type="time" name="opening_time" value="{{ old('opening_time') }}"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                            @error('opening_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Horario Cierre</label>
                            <input type="time" name="closing_time" value="{{ old('closing_time') }}"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                            @error('closing_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white mr-3 shadow-md">3</span>
                    Documentación
                </h2>

                <div class="space-y-8">
                    <!-- DNI Photo -->
                    <div class="bg-slate-50/80 p-5 sm:p-6 rounded-2xl border-2 border-gray-100 hover:border-blue-200 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <div>
                                <label class="block text-base font-bold text-gray-800">Foto de tu DNI / Documento *</label>
                                <p class="text-xs text-gray-500">Toma una foto nítida de tu documento de identidad de frente</p>
                            </div>
                            <span id="dni_status_badge" class="hidden text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 w-fit">
                                ✓ Foto lista
                            </span>
                        </div>

                        <!-- Botones directos de captura mobile -->
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <button type="button" onclick="triggerDniCamera()"
                                class="flex items-center justify-center gap-2 py-3 px-3 sm:px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold shadow hover:shadow-md active:scale-95 transition text-xs sm:text-sm">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>📸 Usar Cámara</span>
                            </button>

                            <button type="button" onclick="triggerDniGallery()"
                                class="flex items-center justify-center gap-2 py-3 px-3 sm:px-4 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 active:scale-95 transition text-xs sm:text-sm">
                                <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>📁 Elegir de Galería</span>
                            </button>
                        </div>

                        <!-- Dropzone & Preview -->
                        <div id="dni_dropzone" onclick="triggerDniCamera()"
                            class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-white hover:bg-blue-50/30 transition-all overflow-hidden relative">
                            <div id="dni_placeholder" class="flex flex-col items-center justify-center p-4 text-center">
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">Toca aquí para abrir la cámara o seleccionar foto</p>
                                <p class="text-xs text-gray-400 mt-1">Formato JPG, PNG o WEBP (se optimizará al instante)</p>
                            </div>
                            <img id="dni_preview" class="hidden absolute inset-0 w-full h-full object-contain bg-slate-900/5 p-2" alt="DNI Preview" />
                        </div>

                        <!-- Información y Botón Eliminar -->
                        <div id="dni_info_row" class="hidden items-center justify-between mt-3 pt-2 border-t border-gray-200">
                            <span id="dni_file_size" class="text-xs text-gray-500 font-medium"></span>
                            <button type="button" class="text-xs text-red-600 font-bold hover:text-red-800 flex items-center gap-1" onclick="removeDNI()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar foto
                            </button>
                        </div>

                        <!-- Inputs nativos ocultos -->
                        <input id="dni_photo_camera" type="file" class="hidden" accept="image/*" capture="environment" onchange="handleDniInput(this)">
                        <input id="dni_photo_gallery" type="file" class="hidden" accept="image/*" onchange="handleDniInput(this)">
                        <input id="dni_photo" name="dni_photo" type="file" class="hidden" accept="image/*">

                        @error('dni_photo')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kitchen Photos -->
                    <div class="bg-slate-50/80 p-5 sm:p-6 rounded-2xl border-2 border-gray-100 hover:border-purple-200 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <div>
                                <label class="block text-base font-bold text-gray-800">Fotos de tu Cocina * (mínimo 3, máximo 5)</label>
                                <p class="text-xs text-gray-500">Muestra tu espacio de preparación, utensilios y limpieza</p>
                            </div>
                            <span id="kitchen_count_badge" class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 w-fit">
                                ⚠️ 0 de 3 fotos mínimas
                            </span>
                        </div>

                        <!-- Botones de Acción Mobile -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <button type="button" onclick="triggerKitchenCamera()" id="btn_kitchen_camera"
                                class="flex items-center justify-center gap-2 py-3 px-4 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white rounded-xl font-bold shadow hover:shadow-md active:scale-95 transition text-sm">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>📸 Tomar Foto con Cámara</span>
                            </button>

                            <button type="button" onclick="triggerKitchenGallery()" id="btn_kitchen_gallery"
                                class="flex items-center justify-center gap-2 py-3 px-4 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 active:scale-95 transition text-sm">
                                <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>📁 Subir de la Galería</span>
                            </button>
                        </div>

                        <!-- Tips para Mobile -->
                        <div class="bg-purple-50/80 border border-purple-100 text-purple-900 text-xs p-3 rounded-xl mb-4 flex items-center gap-2">
                            <span class="text-base shrink-0">💡</span>
                            <span><strong>Consejo mobile:</strong> Toca <strong>"Tomar Foto con Cámara"</strong> para capturar una foto tras otra en segundos hasta completar las 3 requeridas.</span>
                        </div>

                        <!-- Previsualizaciones de fotos de cocina -->
                        <div id="kitchen_preview_container" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-2"></div>

                        <!-- Inputs nativos ocultos -->
                        <input id="kitchen_camera_input" type="file" class="hidden" accept="image/*" capture="environment" onchange="handleKitchenInput(this)">
                        <input id="kitchen_gallery_input" type="file" class="hidden" accept="image/*" multiple onchange="handleKitchenInput(this)">
                        <input id="kitchen_photos" name="kitchen_photos[]" type="file" class="hidden" accept="image/*" multiple>

                        @error('kitchen_photos')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="hidden bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span
                        class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white mr-3">4</span>
                    Datos de Pago
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">CBU/CVU o Alias</label>
                        <input type="text" name="payment_details" value="{{ old('payment_details') }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                            placeholder="0000003100012345678901 o alias">
                        @error('payment_details')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Recibirás tus pagos automáticamente en esta cuenta (menos {{ $globalSettings['commission_rate'] ?? 15 }}%
                            de comisión)</p>
                    </div>
                </div>
            </div>

            <!-- Terms -->
            <div class="bg-gradient-to-r from-orange-50 to-pink-50 rounded-2xl shadow-lg p-8">
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" name="terms" required class="w-6 h-6 text-purple-600 rounded mt-1">
                    <span class="text-gray-700">
                        Acepto los <a href="#" class="text-purple-600 font-semibold hover:text-pink-600">términos y
                            condiciones</a>
                        y confirmo que la información proporcionada es correcta. Entiendo que mi perfil será revisado antes
                        de ser aprobado.
                    </span>
                </label>
                @error('terms')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex items-center space-x-4">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-5 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all">
                    Enviar Solicitud
                </button>
            </div>

            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-sm text-gray-700">
                    <span class="font-semibold">📋 Próximo paso:</span> Nuestro equipo revisará tu solicitud en 24-48 horas
                    y te notificaremos por email
                </p>
            </div>
        </form>
    </div>

    @push('scripts')
        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            let map, marker;

            function initMap() {
                // Posición inicial: Bell Ville, Córdoba (o una por defecto)
                const defaultLat = -32.6471;
                const defaultLng = -63.0347;

                const lat = parseFloat(document.getElementById('location_lat').value) || defaultLat;
                const lng = parseFloat(document.getElementById('location_lng').value) || defaultLng;

                map = L.map('map').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', function (event) {
                    const position = marker.getLatLng();
                    updateLocationData(position.lat, position.lng);
                });

                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    updateLocationData(e.latlng.lat, e.latlng.lng);
                });
            }

            function updateLocationData(lat, lng) {
                document.getElementById('location_lat').value = lat.toFixed(4);
                document.getElementById('location_lng').value = lng.toFixed(4);

                // Reverse Geocoding con Nominatim
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name) {
                            // Intentamos obtener una dirección más limpia (Calle y Número si están disponibles)
                            const addr = data.address;
                            let cleanAddress = '';

                            if (addr.road) {
                                cleanAddress = addr.road;
                                if (addr.house_number) cleanAddress += ' ' + addr.house_number;
                                if (addr.city || addr.town || addr.village) {
                                    cleanAddress += ', ' + (addr.city || addr.town || addr.village);
                                }
                            } else {
                                cleanAddress = data.display_name;
                            }

                            document.getElementById('address').value = cleanAddress;
                        }
                    })
                    .catch(error => console.error('Error in reverse geocoding:', error));
            }

            function detectLocation() {
                if (navigator.geolocation) {
                    const btn = event.currentTarget;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '⌛ Detectando...';
                    btn.disabled = true;

                    navigator.geolocation.getCurrentPosition(position => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        marker.setLatLng([lat, lng]);
                        map.setView([lat, lng], 16);
                        updateLocationData(lat, lng);

                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        alert('✅ Ubicación detectada correctamente');
                    }, error => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        alert('❌ No se pudo detectar la ubicación. Por favor selecciona tu posición en el mapa.');
                    });
                } else {
                    alert('❌ Tu navegador no soporta geolocalización');
                }
            }

            let geocodeTimeout;

            function geocodeAddressInput(addressText) {
                if (!addressText || addressText.trim().length < 4) return Promise.resolve(false);
                return fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(addressText)}&limit=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            document.getElementById('location_lat').value = lat.toFixed(4);
                            document.getElementById('location_lng').value = lng.toFixed(4);
                            if (map && marker) {
                                marker.setLatLng([lat, lng]);
                                map.setView([lat, lng], 15);
                            }
                            return true;
                        }
                        return false;
                    })
                    .catch(err => {
                        console.error('Error in forward geocoding:', err);
                        return false;
                    });
            }

            // Inicializar mapa al cargar el DOM
            document.addEventListener('DOMContentLoaded', function() {
                initMap();

                const addrInput = document.getElementById('address');
                if (addrInput) {
                    addrInput.addEventListener('change', function() {
                        geocodeAddressInput(this.value);
                    });
                    addrInput.addEventListener('input', function() {
                        clearTimeout(geocodeTimeout);
                        const val = this.value;
                        geocodeTimeout = setTimeout(() => {
                            geocodeAddressInput(val);
                        }, 1000);
                    });
                }
            });

            // ==========================================
            // MANEJO DE IMÁGENES MOBILE (CÁMARA / GALERÍA)
            // ==========================================

            let currentDniFile = null;
            let selectedKitchenFiles = [];

            // Utilidad para formatear tamaño de archivos
            function formatBytes(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            // Utilidad para comprimir imágenes en el cliente (evita errores de tamaño y acelera la carga)
            async function compressImage(file, maxWidth = 1600, maxHeight = 1600, quality = 0.82) {
                if (!file || !file.type.startsWith('image/')) {
                    return file;
                }
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const img = new Image();
                        img.onload = () => {
                            let width = img.naturalWidth || img.width;
                            let height = img.naturalHeight || img.height;

                            if (width > height) {
                                if (width > maxWidth) {
                                    height = Math.round((height * maxWidth) / width);
                                    width = maxWidth;
                                }
                            } else {
                                if (height > maxHeight) {
                                    width = Math.round((width * maxHeight) / height);
                                    height = maxHeight;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                if (blob) {
                                    const cleanName = (file.name || 'foto').replace(/\.[^/.]+$/, "") + ".jpg";
                                    const compressedFile = new File([blob], cleanName, {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });
                                    resolve(compressedFile);
                                } else {
                                    resolve(file);
                                }
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = () => resolve(file);
                        img.src = event.target.result;
                    };
                    reader.onerror = () => resolve(file);
                    reader.readAsDataURL(file);
                });
            }

            // --- MANEJO DNI ---
            function triggerDniCamera() {
                const camInput = document.getElementById('dni_photo_camera');
                if (camInput) camInput.click();
            }

            function triggerDniGallery() {
                const galInput = document.getElementById('dni_photo_gallery');
                if (galInput) galInput.click();
            }

            async function handleDniInput(input) {
                if (!input.files || !input.files[0]) return;
                const rawFile = input.files[0];

                const preview = document.getElementById('dni_preview');
                const placeholder = document.getElementById('dni_placeholder');
                const badge = document.getElementById('dni_status_badge');
                const infoRow = document.getElementById('dni_info_row');
                const sizeLabel = document.getElementById('dni_file_size');

                // Asignar inmediatamente el archivo para asegurar que nunca esté vacío
                currentDniFile = rawFile;

                // Mostrar estado optimizando
                if (sizeLabel) sizeLabel.innerText = '⌛ Optimizando foto...';
                if (infoRow) {
                    infoRow.classList.remove('hidden');
                    infoRow.classList.add('flex');
                }

                // Mostrar preview inmediato
                try {
                    preview.src = URL.createObjectURL(rawFile);
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    badge.classList.remove('hidden');
                } catch(e) {}

                try {
                    const compressed = await compressImage(rawFile, 1600, 1600, 0.82);
                    currentDniFile = compressed;

                    try {
                        preview.src = URL.createObjectURL(compressed);
                    } catch(e) {}

                    // Intentar actualizar input del formulario con DataTransfer si el navegador lo soporta
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(compressed);
                        document.getElementById('dni_photo').files = dt.files;
                    } catch(dtErr) {
                        // DataTransfer no siempre está disponible o writable en navegadores móviles (iOS Safari). No interrumpe.
                    }

                    if (sizeLabel) sizeLabel.innerText = `Foto lista (${formatBytes(compressed.size)} - optimizada)`;
                } catch (e) {
                    console.warn('Error comprimiendo DNI, manteniendo archivo original:', e);
                    currentDniFile = rawFile;
                    if (sizeLabel) sizeLabel.innerText = `Foto lista (${formatBytes(rawFile.size)})`;
                } finally {
                    input.value = ''; // Permite volver a seleccionar el mismo archivo si es necesario
                }
            }

            function removeDNI() {
                currentDniFile = null;
                try {
                    const dt = new DataTransfer();
                    document.getElementById('dni_photo').files = dt.files;
                } catch(e) {}
                try {
                    document.getElementById('dni_photo').value = '';
                } catch(e) {}

                const preview = document.getElementById('dni_preview');
                const placeholder = document.getElementById('dni_placeholder');
                const badge = document.getElementById('dni_status_badge');
                const infoRow = document.getElementById('dni_info_row');

                preview.src = '';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                badge.classList.add('hidden');
                infoRow.classList.add('hidden');
                infoRow.classList.remove('flex');
            }

            // --- MANEJO FOTOS DE COCINA (Mínimo 3, Máximo 5) ---
            function triggerKitchenCamera() {
                if (selectedKitchenFiles.length >= 5) {
                    alert('Has alcanzado el límite máximo de 5 fotos de cocina.');
                    return;
                }
                const camInput = document.getElementById('kitchen_camera_input');
                if (camInput) camInput.click();
            }

            function triggerKitchenGallery() {
                if (selectedKitchenFiles.length >= 5) {
                    alert('Has alcanzado el límite máximo de 5 fotos de cocina.');
                    return;
                }
                const galInput = document.getElementById('kitchen_gallery_input');
                if (galInput) galInput.click();
            }

            async function handleKitchenInput(input) {
                if (!input.files || input.files.length === 0) return;

                const badge = document.getElementById('kitchen_count_badge');
                const originalBadgeText = badge.innerHTML;
                badge.innerHTML = '⌛ Optimizando imágenes...';

                const files = Array.from(input.files);
                for (const file of files) {
                    if (selectedKitchenFiles.length >= 5) {
                        alert('Se pueden agregar un máximo de 5 fotos.');
                        break;
                    }
                    try {
                        const compressed = await compressImage(file, 1600, 1600, 0.82);
                        selectedKitchenFiles.push(compressed);
                    } catch (err) {
                        console.error('Error comprimiendo foto:', err);
                        selectedKitchenFiles.push(file);
                    }
                }

                input.value = ''; // Permite reutilizar la cámara o galería inmediatamente
                syncKitchenInput();
                renderKitchenPreviews();
            }

            function syncKitchenInput() {
                try {
                    const dt = new DataTransfer();
                    selectedKitchenFiles.forEach(f => dt.items.add(f));
                    document.getElementById('kitchen_photos').files = dt.files;
                } catch(e) {
                    // DataTransfer es opcional; la inyección explícita en FormData garantiza la subida
                }
            }

            function removeKitchenPhotoAt(index) {
                selectedKitchenFiles.splice(index, 1);
                syncKitchenInput();
                renderKitchenPreviews();
            }

            function renderKitchenPreviews() {
                const container = document.getElementById('kitchen_preview_container');
                const badge = document.getElementById('kitchen_count_badge');
                const btnCamera = document.getElementById('btn_kitchen_camera');
                const btnGallery = document.getElementById('btn_kitchen_gallery');

                container.innerHTML = '';

                selectedKitchenFiles.forEach((file, idx) => {
                    const card = document.createElement('div');
                    card.className = 'relative rounded-2xl overflow-hidden shadow-sm border border-gray-200 aspect-square group bg-gray-100';

                    const objUrl = URL.createObjectURL(file);
                    card.innerHTML = `
                        <img src="${objUrl}" class="w-full h-full object-cover" alt="Cocina ${idx + 1}" />
                        <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-xs text-white text-[11px] font-bold px-2 py-0.5 rounded-full">
                            Foto ${idx + 1}
                        </div>
                        <button type="button" onclick="removeKitchenPhotoAt(${idx})"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full shadow-lg transition active:scale-90"
                            title="Eliminar foto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <div class="absolute bottom-2 left-2 right-2 bg-black/60 backdrop-blur-xs text-white text-[10px] text-center font-medium py-0.5 rounded-md truncate px-1">
                            ${formatBytes(file.size)}
                        </div>
                    `;
                    container.appendChild(card);
                });

                // Actualizar contador y estado del badge
                const count = selectedKitchenFiles.length;
                if (count === 0) {
                    badge.className = 'inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 w-fit';
                    badge.innerHTML = '⚠️ 0 de 3 fotos mínimas';
                } else if (count < 3) {
                    badge.className = 'inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 w-fit';
                    badge.innerHTML = `⚠️ ${count} de 3 fotos (faltan ${3 - count})`;
                } else if (count < 5) {
                    badge.className = 'inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 w-fit';
                    badge.innerHTML = `✓ ${count} fotos listas (mínimo cumplido)`;
                } else {
                    badge.className = 'inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-full bg-purple-100 text-purple-800 w-fit';
                    badge.innerHTML = `🎉 5 de 5 fotos (máximo alcanzado)`;
                }

                // Deshabilitar botones si se llegó al máximo de 5
                if (count >= 5) {
                    if (btnCamera) btnCamera.classList.add('opacity-50', 'cursor-not-allowed');
                    if (btnGallery) btnGallery.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    if (btnCamera) btnCamera.classList.remove('opacity-50', 'cursor-not-allowed');
                    if (btnGallery) btnGallery.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            // --- Envío del Formulario con Barra de Progreso ---
            const form = document.getElementById('profileForm');
            const overlay = document.getElementById('loadingOverlay');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const dniInput = document.getElementById('dni_photo');
                const hasDni = currentDniFile || (dniInput && dniInput.files && dniInput.files.length > 0);
                if (!hasDni) {
                    alert('⚠️ Por favor toma o selecciona la foto de tu DNI.');
                    window.scrollTo({ top: document.getElementById('dni_dropzone').offsetTop - 120, behavior: 'smooth' });
                    return;
                }
                if (!selectedKitchenFiles || selectedKitchenFiles.length < 3) {
                    alert(`⚠️ Debes agregar al menos 3 fotos de tu cocina (actualmente tienes ${selectedKitchenFiles ? selectedKitchenFiles.length : 0}).`);
                    window.scrollTo({ top: document.getElementById('kitchen_count_badge').offsetTop - 120, behavior: 'smooth' });
                    return;
                }

                // Si latitud o longitud están vacías, geolocalizar la dirección ingresada antes de enviar
                const latVal = document.getElementById('location_lat').value;
                const lngVal = document.getElementById('location_lng').value;
                if (!latVal || !lngVal || parseFloat(latVal) === 0 || parseFloat(lngVal) === 0) {
                    const addrText = document.getElementById('address').value;
                    if (addrText) {
                        await geocodeAddressInput(addrText);
                    }
                }

                // Sincronizar inputs nativos si el navegador lo permite
                syncKitchenInput();

                // Mostrar overlay de progreso
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                progressBar.style.width = '10%';
                progressText.innerText = 'Subiendo solicitud y fotos... 10%';

                const formData = new FormData(form);

                // Inyección explícita garantizada en FormData (elimina dependencia de DataTransfer en mobile)
                const dniToSend = currentDniFile || (dniInput && dniInput.files && dniInput.files[0]);
                if (dniToSend) {
                    formData.set('dni_photo', dniToSend, dniToSend.name || 'dni.jpg');
                }

                formData.delete('kitchen_photos[]');
                selectedKitchenFiles.forEach((file, idx) => {
                    formData.append('kitchen_photos[]', file, file.name || `cocina_${idx + 1}.jpg`);
                });

                const xhr = new XMLHttpRequest();
                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = function(event) {
                    if (event.lengthComputable) {
                        const percentComplete = Math.round((event.loaded / event.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressText.innerText = `Subiendo fotos y datos: ${percentComplete}%`;
                    }
                };

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        progressBar.style.width = '100%';
                        progressText.innerText = '¡Completado con éxito!';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                window.location.href = "{{ route('cook.dashboard') }}";
                            }
                        } catch(e) {
                            window.location.href = "{{ route('cook.dashboard') }}";
                        }
                    } else if (xhr.status === 422) {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                        try {
                            const response = JSON.parse(xhr.responseText);
                            let errors = '';
                            for (let field in response.errors) {
                                errors += `• ${response.errors[field][0]}\n`;
                            }
                            alert('Corrige los siguientes errores en el formulario:\n\n' + errors);
                        } catch (e) {
                            alert('Errores de validación en los datos ingresados. Por favor revisa el formulario.');
                        }
                    } else {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                        alert('Ocurrió un error al enviar el formulario. Por favor verifica tu conexión y vuelve a intentarlo.');
                    }
                };

                xhr.onerror = function() {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                    alert('Error de red al intentar enviar el formulario. Por favor verifica tu conexión.');
                };

                xhr.send(formData);
            });
        </script>
    @endpush

@endsection