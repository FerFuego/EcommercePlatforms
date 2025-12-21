@extends('layouts.app')

@section('title', 'Crear Perfil - Repartidor')

@section('content')
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h1 class="text-3xl font-bold mb-2 bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                Crear Perfil de Repartidor
            </h1>
            <p class="text-gray-600 mb-8">Completa tu perfil para comenzar a recibir pedidos</p>

            <form action="{{ route('delivery-driver.profile.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- DNI -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">Documento de Identidad</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Número de DNI *</label>
                            <input type="text" name="dni_number" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
                                value="{{ old('dni_number') }}">
                            @error('dni_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto del DNI *</label>
                            <input type="file" name="dni_photo" required accept="image/*"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition">
                            @error('dni_photo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Profile Photo -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto de Perfil</label>
                    <input type="file" name="profile_photo" accept="image/*"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition">
                    @error('profile_photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vehicle -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">Información del Vehículo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Vehículo *</label>
                            <select name="vehicle_type" required id="vehicle_type"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                                <option value="">Seleccionar...</option>
                                <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>🚲 Bicicleta
                                </option>
                                <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>🏍️
                                    Moto</option>
                                <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>🚗 Auto</option>
                            </select>
                            @error('vehicle_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div id="plate_container" style="display: none;">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Patente</label>
                            <input type="text" name="vehicle_plate"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('vehicle_plate') }}">
                            @error('vehicle_plate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Foto del Vehículo</label>
                        <input type="file" name="vehicle_photo" accept="image/*"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition">
                        @error('vehicle_photo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Coverage Area -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">Área de Cobertura</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Latitud *</label>
                            <input type="number" name="location_lat" required step="0.0001" id="location_lat"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('location_lat', '-32.4') }}">
                            @error('location_lat')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Longitud *</label>
                            <input type="number" name="location_lng" required step="0.0001" id="location_lng"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('location_lng', '-63.2') }}">
                            @error('location_lng')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Radio (km) *</label>
                            <input type="number" name="coverage_radius_km" required min="1" max="50"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('coverage_radius_km', 5) }}">
                            @error('coverage_radius_km')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <button type="button" onclick="getMyLocation()"
                        class="mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                        📍 Usar mi ubicación actual
                    </button>
                </div>

                <!-- Bank Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">Información Bancaria (Opcional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Banco</label>
                            <input type="text" name="bank_name"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('bank_name') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Número de Cuenta</label>
                            <input type="text" name="account_number"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('account_number') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Cuenta</label>
                            <select name="account_type"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition">
                                <option value="">Seleccionar...</option>
                                <option value="checking">Cuenta Corriente</option>
                                <option value="savings">Caja de Ahorro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">CBU/CVU</label>
                            <input type="text" name="cbu_cvu"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('cbu_cvu') }}">
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-cyan-600 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                        Crear Perfil
                    </button>
                    <a href="{{ route('home') }}"
                        class="px-8 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('vehicle_type').addEventListener('change', function () {
                const plateContainer = document.getElementById('plate_container');
                if (this.value === 'motorcycle' || this.value === 'car') {
                    plateContainer.style.display = 'block';
                } else {
                    plateContainer.style.display = 'none';
                }
            });

            function getMyLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        document.getElementById('location_lat').value = position.coords.latitude.toFixed(4);
                        document.getElementById('location_lng').value = position.coords.longitude.toFixed(4);
                        alert('Ubicación obtenida exitosamente');
                    }, function (error) {
                        alert('Error al obtener ubicación: ' + error.message);
                    });
                } else {
                    alert('Tu navegador no soporta geolocalización');
                }
            }
        </script>
    @endpush
@endsection