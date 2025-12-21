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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Latitud *</label>
                            <input type="number" name="location_lat" step="0.0001" required
                                value="{{ old('location_lat') }}"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                placeholder="-32.174">
                            @error('location_lat')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Longitud *</label>
                            <input type="number" name="location_lng" step="0.0001" required
                                value="{{ old('location_lng') }}"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                placeholder="-63.294">
                            @error('location_lng')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="button" onclick="detectLocation()"
                        class="bg-blue-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-600 transition">
                        📍 Detectar Mi Ubicación Automáticamente
                    </button>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Radio de Cobertura (km) *</label>
                        <input type="number" name="coverage_radius_km" value="{{ old('coverage_radius_km', 10) }}" required
                            min="1" max="50"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
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
                        <p class="text-xs text-gray-500 mt-1">Recibirás tus pagos automáticamente en esta cuenta (menos 12%
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
            function detectLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        document.querySelector('[name="location_lat"]').value = position.coords.latitude.toFixed(4);
                        document.querySelector('[name="location_lng"]').value = position.coords.longitude.toFixed(4);
                        alert('✅ Ubicación detectada correctamente');
                    }, error => {
                        alert('❌ No se pudo detectar la ubicación. Por favor ingresa las coordenadas manualmente.');
                    });
                } else {
                    alert('❌ Tu navegador no soporta geolocalización');
                }
            }
        </script>
    @endpush

@endsection