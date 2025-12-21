@extends('layouts.app')

@section('title', 'Editar Perfil - Repartidor')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6 bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
            Editar Perfil de Repartidor
        </h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden">
                @if(auth()->user()->is_suspended)
                    <div
                        class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
                    </div>
                @endif

                <h3 class="text-xl font-bold mb-4">Acciones Rápidas</h3>
                <div class="space-y-3 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none' : '' }}">
                    @if($driver->is_approved)
                        <a href="{{ route('delivery-driver.dashboard') }}"
                            class="block bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            🚴 Dashboard
                        </a>
                        <a href="{{ route('delivery-driver.orders.available') }}"
                            class="block bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            🗺️ Ver Pedidos Disponibles
                        </a>
                        <a href="{{ route('delivery-driver.orders.index') }}"
                            class="block bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            📦 Mis Entregas
                        </a>
                        <a href="{{ route('delivery-driver.earnings') }}"
                            class="block bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            💰 Ver Ganancias
                        </a>
                    @endif
                    <a href="{{ route('delivery-driver.profile.edit') }}"
                        class="block bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                        ⚙️ Editar Perfil
                    </a>
                </div>
            </div>

            <div class="g:col-span-2 col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-8">

                    <p class="text-gray-600 mb-8">Actualiza tu información</p>

                    @if(auth()->user()->is_suspended)
                        <div class="bg-red-500 text-white px-6 py-4 rounded-2xl shadow-lg mb-8">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h3 class="font-bold text-lg">Cuenta Suspendida</h3>
                                    <p>Tu cuenta está suspendida. Puedes editar tu perfil, pero no podrás realizar otras
                                        acciones hasta
                                        contactar a soporte.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('delivery-driver.profile.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto de Perfil</label>
                            @if($driver->profile_photo)
                                <img src="{{ Storage::url($driver->profile_photo) }}" alt="Profile"
                                    class="w-32 h-32 object-cover rounded-full mb-3">
                            @endif
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
                                        <option value="bicycle" {{ old('vehicle_type', $driver->vehicle_type) == 'bicycle' ? 'selected' : '' }}>🚲 Bicicleta
                                        </option>
                                        <option value="motorcycle" {{ old('vehicle_type', $driver->vehicle_type) == 'motorcycle' ? 'selected' : '' }}>🏍️
                                            Moto</option>
                                        <option value="car" {{ old('vehicle_type', $driver->vehicle_type) == 'car' ? 'selected' : '' }}>🚗 Auto</option>
                                    </select>
                                    @error('vehicle_type')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div id="plate_container"
                                    style="display: {{ in_array($driver->vehicle_type, ['motorcycle', 'car']) ? 'block' : 'none' }};">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Patente</label>
                                    <input type="text" name="vehicle_plate"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                        value="{{ old('vehicle_plate', $driver->vehicle_plate) }}">
                                    @error('vehicle_plate')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto del Vehículo</label>
                                @if($driver->vehicle_photo)
                                    <img src="{{ Storage::url($driver->vehicle_photo) }}" alt="Vehicle"
                                        class="w-48 h-32 object-cover rounded-lg mb-3">
                                @endif
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
                                        value="{{ old('location_lat', $driver->location_lat) }}">
                                    @error('location_lat')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Longitud *</label>
                                    <input type="number" name="location_lng" required step="0.0001" id="location_lng"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                        value="{{ old('location_lng', $driver->location_lng) }}">
                                    @error('location_lng')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Radio (km) *</label>
                                    <input type="number" name="coverage_radius_km" required min="1" max="50"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                        value="{{ old('coverage_radius_km', $driver->coverage_radius_km) }}">
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
                                        value="{{ old('bank_name', $driver->bank_name) }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Número de Cuenta</label>
                                    <input type="text" name="account_number"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                        value="{{ old('account_number', $driver->account_number) }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Cuenta</label>
                                    <select name="account_type"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition">
                                        <option value="">Seleccionar...</option>
                                        <option value="checking" {{ old('account_type', $driver->account_type) == 'checking' ? 'selected' : '' }}>Cuenta Corriente</option>
                                        <option value="savings" {{ old('account_type', $driver->account_type) == 'savings' ? 'selected' : '' }}>Caja de Ahorro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">CBU/CVU</label>
                                    <input type="text" name="cbu_cvu"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                        value="{{ old('cbu_cvu', $driver->cbu_cvu) }}">
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-500 to-cyan-600 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                Actualizar Perfil
                            </button>
                            <a href="{{ route('delivery-driver.dashboard') }}"
                                class="px-8 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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