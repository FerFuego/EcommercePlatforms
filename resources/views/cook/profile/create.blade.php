@extends('layouts.app')

@section('title', 'Registrarse como Cocinero')

@section('content')
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold mb-4">
                <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                    Únete como Cocinero
                </span>
            </h1>
            <p class="text-xl text-gray-600">Comparte tu pasión por cocinar y genera ingresos</p>
        </div>
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

        <form action="{{ route('cook.profile.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
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
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white mr-3">3</span>
                    Documentación
                </h2>

                <div class="space-y-6">
                    <!-- DNI Photo -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Foto de DNI/Documento *</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="dni_photo"
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gradient-to-br from-gray-50 to-blue-50 hover:from-blue-50 hover:to-indigo-50 transition-all">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                        </path>
                                    </svg>
                                    <p class="text-sm text-gray-500"><span class="font-semibold">Subir DNI</span></p>
                                </div>
                                <input id="dni_photo" name="dni_photo" type="file" class="hidden" accept="image/*" required>
                            </label>
                        </div>
                        @error('dni_photo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kitchen Photos -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Fotos de tu Cocina * (mínimo
                            3)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="kitchen_photos"
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gradient-to-br from-gray-50 to-purple-50 hover:from-purple-50 hover:to-pink-50 transition-all">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-sm text-gray-500"><span class="font-semibold">Subir Fotos</span>
                                        (múltiples)</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG (MAX. 2MB cada una)</p>
                                </div>
                                <input id="kitchen_photos" name="kitchen_photos[]" type="file" class="hidden"
                                    accept="image/*" multiple required>
                            </label>
                        </div>
                        @error('kitchen_photos')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">Fotos claras de tu espacio de cocina, utensilios, y lugar de
                            trabajo</p>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span
                        class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white mr-3">4</span>
                    Datos de Pago
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">CBU/CVU o Alias *</label>
                        <input type="text" name="payment_details" required value="{{ old('payment_details') }}"
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

            // Inicializar mapa al cargar el DOM
            document.addEventListener('DOMContentLoaded', initMap);
        </script>
    @endpush

@endsection