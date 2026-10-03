@extends('layouts.app')

@section('title', 'Editar Perfil - Repartidor')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6 bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
            Editar Perfil de Repartidor
        </h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                @include('delivery-driver.partials.quick-actions')
            </div>

            <div class="lg:col-span-2">
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
                                    <p class="text-xs text-red-600">Por favor, corrige tus datos y vuelve a guardar para enviar tu postulación nuevamente.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('delivery-driver.profile.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo -->
                        <div class="mb-6 p-4 bg-slate-50/80 rounded-2xl border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto de Perfil</label>
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-blue-400 bg-gray-100 shrink-0">
                                    <img id="edit_profile_preview" src="{{ $driver->profile_photo ? asset('uploads/' . $driver->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" alt="Profile"
                                        class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" onclick="document.getElementById('profile_camera_edit').click()"
                                            class="py-2 px-3 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow hover:bg-indigo-700 active:scale-95 transition">
                                            🤳 Tomar Selfie
                                        </button>
                                        <button type="button" onclick="document.getElementById('profile_gallery_edit').click()"
                                            class="py-2 px-3 bg-white border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 active:scale-95 transition">
                                            📁 Galería
                                        </button>
                                    </div>
                                    <span id="profile_edit_size" class="text-[11px] text-gray-500 mt-1 block"></span>
                                </div>
                            </div>
                            <input id="profile_camera_edit" type="file" class="hidden" accept="image/*" capture="user" onchange="handleEditProfilePhoto(this)">
                            <input id="profile_gallery_edit" type="file" class="hidden" accept="image/*" onchange="handleEditProfilePhoto(this)">
                            <input id="profile_photo" name="profile_photo" type="file" class="hidden" accept="image/*">
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
                            <div class="mt-4 p-4 bg-slate-50/80 rounded-2xl border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto del Vehículo</label>
                                <div class="flex items-center gap-4 mb-3">
                                    @if($driver->vehicle_photo)
                                        <img id="edit_vehicle_preview" src="{{ asset('uploads/' . $driver->vehicle_photo) }}" alt="Vehicle"
                                            class="w-24 h-20 object-cover rounded-xl border-2 border-blue-400 shrink-0">
                                    @else
                                        <img id="edit_vehicle_preview" class="hidden w-24 h-20 object-cover rounded-xl border-2 border-blue-400 shrink-0">
                                    @endif
                                    <div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" onclick="document.getElementById('vehicle_camera_edit').click()"
                                                class="py-2 px-3 bg-cyan-600 text-white rounded-xl text-xs font-bold shadow hover:bg-cyan-700 active:scale-95 transition">
                                                📸 Usar Cámara
                                            </button>
                                            <button type="button" onclick="document.getElementById('vehicle_gallery_edit').click()"
                                                class="py-2 px-3 bg-white border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 active:scale-95 transition">
                                                📁 Galería
                                            </button>
                                        </div>
                                        <span id="vehicle_edit_size" class="text-[11px] text-gray-500 mt-1 block"></span>
                                    </div>
                                </div>
                                <input id="vehicle_camera_edit" type="file" class="hidden" accept="image/*" capture="environment" onchange="handleEditVehiclePhoto(this)">
                                <input id="vehicle_gallery_edit" type="file" class="hidden" accept="image/*" onchange="handleEditVehiclePhoto(this)">
                                <input id="vehicle_photo" name="vehicle_photo" type="file" class="hidden" accept="image/*">
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

            // ==========================================
            // COMPRESIÓN E IMÁGENES
            // ==========================================
            function formatBytes(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            async function compressDriverEditImage(file, maxWidth = 1600, maxHeight = 1600, quality = 0.82) {
                if (!file || !file.type.startsWith('image/')) return file;
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
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
                                    resolve(new File([blob], cleanName, { type: 'image/jpeg', lastModified: Date.now() }));
                                } else {
                                    resolve(file);
                                }
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = () => resolve(file);
                        img.src = e.target.result;
                    };
                    reader.onerror = () => resolve(file);
                    reader.readAsDataURL(file);
                });
            }

            async function handleEditProfilePhoto(input) {
                if (!input.files || !input.files[0]) return;
                const file = input.files[0];
                const preview = document.getElementById('edit_profile_preview');
                const sizeLabel = document.getElementById('profile_edit_size');
                if (sizeLabel) sizeLabel.innerText = '⌛ Optimizando...';

                try {
                    const compressed = await compressDriverEditImage(file, 1200, 1200, 0.82);
                    const dt = new DataTransfer();
                    dt.items.add(compressed);
                    document.getElementById('profile_photo').files = dt.files;
                    preview.src = URL.createObjectURL(compressed);
                    if (sizeLabel) sizeLabel.innerText = `Listo (${formatBytes(compressed.size)})`;
                } catch(e) {
                    console.error('Error optimizando foto de perfil:', e);
                } finally {
                    input.value = '';
                }
            }

            async function handleEditVehiclePhoto(input) {
                if (!input.files || !input.files[0]) return;
                const file = input.files[0];
                const preview = document.getElementById('edit_vehicle_preview');
                const sizeLabel = document.getElementById('vehicle_edit_size');
                if (sizeLabel) sizeLabel.innerText = '⌛ Optimizando...';

                try {
                    const compressed = await compressDriverEditImage(file, 1600, 1600, 0.82);
                    const dt = new DataTransfer();
                    dt.items.add(compressed);
                    document.getElementById('vehicle_photo').files = dt.files;
                    preview.src = URL.createObjectURL(compressed);
                    preview.classList.remove('hidden');
                    if (sizeLabel) sizeLabel.innerText = `Listo (${formatBytes(compressed.size)})`;
                } catch(e) {
                    console.error('Error optimizando foto de vehículo:', e);
                } finally {
                    input.value = '';
                }
            }
        </script>
    @endpush
@endsection