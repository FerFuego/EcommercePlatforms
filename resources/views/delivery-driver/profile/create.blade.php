@extends('layouts.app')

@section('title', 'Crear Perfil - Repartidor')

@push('styles')
    <!-- Leaflet CSS para mapa interactivo -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="text-center mb-10">
            <h1 class="text-4xl sm:text-5xl font-bold mb-3">
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 bg-clip-text text-transparent">
                    Únete como Repartidor
                </span>
            </h1>
            <p class="text-lg text-gray-600">Lleva comida casera y genera ingresos con tus propios horarios</p>
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
                        <p class="text-xs text-red-600">Puedes corregir o actualizar tus datos y volver a enviar la postulación a continuación.</p>
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
                    <p class="font-bold">Revisa los siguientes campos:</p>
                </div>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Overlay de Progreso de Subida -->
        <div id="loadingOverlay" class="fixed inset-0 bg-black/60 z-50 hidden flex-col items-center justify-center backdrop-blur-sm transition-all duration-300">
            <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4">
                <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Enviando solicitud...</h3>
                <p class="text-gray-500 text-center mb-4 text-sm">Estamos subiendo tus fotos optimizadas y datos.</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                    <div id="progressBar" class="bg-gradient-to-r from-blue-500 via-indigo-500 to-cyan-500 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
                <p id="progressText" class="text-sm font-semibold text-blue-600">0%</p>
            </div>
        </div>

        <form id="driverProfileForm" action="{{ route('delivery-driver.profile.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- 1. Documento e Identidad -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white mr-3 shadow-md">1</span>
                    Documentación de Identidad
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Número de DNI / Cédula *</label>
                        <input type="text" name="dni_number" id="dni_number" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
                            placeholder="Ej: 38123456"
                            value="{{ old('dni_number') }}">
                        @error('dni_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto del DNI -->
                    <div class="bg-slate-50/80 p-5 rounded-2xl border-2 border-gray-100 hover:border-blue-200 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <div>
                                <label class="block text-base font-bold text-gray-800">Foto del DNI de frente *</label>
                                <p class="text-xs text-gray-500">Tómale una foto clara con tu celular o selecciónala de tu galería</p>
                            </div>
                            <span id="dni_status_badge" class="hidden text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 w-fit">
                                ✓ Foto lista
                            </span>
                        </div>

                        <!-- Botones directos de captura mobile -->
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <button type="button" onclick="triggerDriverDniCamera()"
                                class="flex items-center justify-center gap-2 py-3 px-3 sm:px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold shadow hover:shadow-md active:scale-95 transition text-xs sm:text-sm">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>📸 Usar Cámara</span>
                            </button>

                            <button type="button" onclick="triggerDriverDniGallery()"
                                class="flex items-center justify-center gap-2 py-3 px-3 sm:px-4 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 active:scale-95 transition text-xs sm:text-sm">
                                <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>📁 Elegir de Galería</span>
                            </button>
                        </div>

                        <!-- Dropzone & Preview -->
                        <div id="dni_dropzone" onclick="triggerDriverDniCamera()"
                            class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-white hover:bg-blue-50/30 transition-all overflow-hidden relative">
                            <div id="dni_placeholder" class="flex flex-col items-center justify-center p-4 text-center">
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">Toca aquí para abrir la cámara o seleccionar foto</p>
                                <p class="text-xs text-gray-400 mt-1">Formato JPG, PNG o WEBP</p>
                            </div>
                            <img id="dni_preview" class="hidden absolute inset-0 w-full h-full object-contain bg-slate-900/5 p-2" alt="DNI Preview" />
                        </div>

                        <!-- Fila info y eliminar -->
                        <div id="dni_info_row" class="hidden items-center justify-between mt-3 pt-2 border-t border-gray-200">
                            <span id="dni_file_size" class="text-xs text-gray-500 font-medium"></span>
                            <button type="button" class="text-xs text-red-600 font-bold hover:text-red-800 flex items-center gap-1" onclick="removeDriverDni()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar foto
                            </button>
                        </div>

                        <!-- Inputs nativos ocultos -->
                        <input id="dni_camera_input" type="file" class="hidden" accept="image/*" capture="environment" onchange="handleDriverDniInput(this)">
                        <input id="dni_gallery_input" type="file" class="hidden" accept="image/*" onchange="handleDriverDniInput(this)">
                        <input id="dni_photo" name="dni_photo" type="file" class="hidden" accept="image/*">

                        @error('dni_photo')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 2. Foto de Perfil (Selfie) -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white mr-3 shadow-md">2</span>
                    Foto de Perfil (Opcional)
                </h2>

                <div class="bg-slate-50/80 p-5 rounded-2xl border-2 border-gray-100 hover:border-indigo-200 transition-colors">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <!-- Avatar Preview / Placeholder -->
                        <div class="relative w-28 h-28 rounded-full border-4 border-white shadow-md overflow-hidden bg-gradient-to-tr from-blue-100 to-indigo-100 shrink-0 flex items-center justify-center">
                            <img id="profile_photo_preview" class="hidden w-full h-full object-cover" alt="Perfil">
                            <div id="profile_photo_placeholder" class="text-indigo-400">
                                <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Controles de captura para Selfie o Galería -->
                        <div class="flex-1 w-full text-center sm:text-left">
                            <p class="text-sm font-bold text-gray-800 mb-1">Tu foto o selfie</p>
                            <p class="text-xs text-gray-500 mb-3">Ayuda a los cocineros y clientes a identificarte al entregar los pedidos.</p>

                            <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                                <button type="button" onclick="triggerProfileCamera()"
                                    class="py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow flex items-center gap-1.5 transition active:scale-95">
                                    <span>🤳 Tomar Selfie</span>
                                </button>
                                <button type="button" onclick="triggerProfileGallery()"
                                    class="py-2 px-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition active:scale-95">
                                    <span>📁 Galería</span>
                                </button>
                                <button type="button" id="btn_remove_profile" onclick="removeProfilePhoto()"
                                    class="hidden py-2 px-3 text-red-600 hover:text-red-800 rounded-xl text-xs font-semibold transition">
                                    Eliminar
                                </button>
                            </div>
                            <span id="profile_file_size" class="text-[11px] text-gray-500 mt-2 block font-medium"></span>
                        </div>
                    </div>

                    <!-- Inputs ocultos para foto de perfil -->
                    <input id="profile_camera_input" type="file" class="hidden" accept="image/*" capture="user" onchange="handleProfilePhotoInput(this)">
                    <input id="profile_gallery_input" type="file" class="hidden" accept="image/*" onchange="handleProfilePhotoInput(this)">
                    <input id="profile_photo" name="profile_photo" type="file" class="hidden" accept="image/*">

                    @error('profile_photo')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 3. Vehículo -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white mr-3 shadow-md">3</span>
                    Información del Vehículo
                </h2>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Vehículo *</label>
                            <select name="vehicle_type" required id="vehicle_type"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition font-medium">
                                <option value="">Seleccionar vehículo...</option>
                                <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>🚲 Bicicleta</option>
                                <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>🏍️ Moto</option>
                                <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>🚗 Auto</option>
                            </select>
                            @error('vehicle_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="plate_container" style="display: {{ in_array(old('vehicle_type'), ['motorcycle', 'car']) ? 'block' : 'none' }};">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Patente / Dominio *</label>
                            <input type="text" name="vehicle_plate" id="vehicle_plate"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition uppercase"
                                placeholder="Ej: AA123BB o 123ABC"
                                value="{{ old('vehicle_plate') }}">
                            @error('vehicle_plate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Foto del Vehículo -->
                    <div class="bg-slate-50/80 p-5 rounded-2xl border-2 border-gray-100 hover:border-cyan-200 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <div>
                                <label class="block text-base font-bold text-gray-800">Foto del Vehículo (Opcional)</label>
                                <p class="text-xs text-gray-500">Muestra tu moto, bicicleta o auto para mayor confianza</p>
                            </div>
                            <span id="vehicle_status_badge" class="hidden text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 w-fit">
                                ✓ Foto lista
                            </span>
                        </div>

                        <!-- Botones de Acción Mobile -->
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <button type="button" onclick="triggerVehicleCamera()"
                                class="flex items-center justify-center gap-2 py-3 px-3 sm:px-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-xl font-bold shadow hover:shadow-md active:scale-95 transition text-xs sm:text-sm">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>📸 Usar Cámara</span>
                            </button>

                            <button type="button" onclick="triggerVehicleGallery()"
                                class="flex items-center justify-center gap-2 py-3 px-3 sm:px-4 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 active:scale-95 transition text-xs sm:text-sm">
                                <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>📁 Elegir de Galería</span>
                            </button>
                        </div>

                        <!-- Dropzone & Preview -->
                        <div id="vehicle_dropzone" onclick="triggerVehicleCamera()"
                            class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-white hover:bg-cyan-50/30 transition-all overflow-hidden relative">
                            <div id="vehicle_placeholder" class="flex flex-col items-center justify-center p-4 text-center">
                                <div class="w-12 h-12 bg-cyan-50 text-cyan-600 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">Toca para abrir la cámara o seleccionar foto</p>
                            </div>
                            <img id="vehicle_preview" class="hidden absolute inset-0 w-full h-full object-contain bg-slate-900/5 p-2" alt="Vehículo Preview" />
                        </div>

                        <!-- Fila info y eliminar -->
                        <div id="vehicle_info_row" class="hidden items-center justify-between mt-3 pt-2 border-t border-gray-200">
                            <span id="vehicle_file_size" class="text-xs text-gray-500 font-medium"></span>
                            <button type="button" class="text-xs text-red-600 font-bold hover:text-red-800 flex items-center gap-1" onclick="removeVehiclePhoto()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar foto
                            </button>
                        </div>

                        <!-- Inputs nativos ocultos -->
                        <input id="vehicle_camera_input" type="file" class="hidden" accept="image/*" capture="environment" onchange="handleVehiclePhotoInput(this)">
                        <input id="vehicle_gallery_input" type="file" class="hidden" accept="image/*" onchange="handleVehiclePhotoInput(this)">
                        <input id="vehicle_photo" name="vehicle_photo" type="file" class="hidden" accept="image/*">

                        @error('vehicle_photo')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 4. Área de Cobertura y Mapa Interactivo -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white mr-3 shadow-md">4</span>
                    Área de Cobertura
                </h2>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Latitud Base *</label>
                            <input type="number" name="location_lat" required step="0.0001" id="location_lat"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition font-mono text-sm"
                                value="{{ old('location_lat', '-32.6471') }}">
                            @error('location_lat')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Longitud Base *</label>
                            <input type="number" name="location_lng" required step="0.0001" id="location_lng"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition font-mono text-sm"
                                value="{{ old('location_lng', '-63.0347') }}">
                            @error('location_lng')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Radio de Entrega (km) *</label>
                            <input type="number" name="coverage_radius_km" id="coverage_radius_km" required min="1" max="50"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                                value="{{ old('coverage_radius_km', 5) }}">
                            @error('coverage_radius_km')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Mapa Leaflet interactivo -->
                    <div id="driver_map" class="h-[280px] w-full rounded-2xl shadow-md border-2 border-gray-100 z-0"></div>

                    <button type="button" onclick="detectDriverLocation()" id="btn_detect_location"
                        class="w-full bg-blue-50 text-blue-700 hover:bg-blue-100 py-3.5 px-4 rounded-xl font-bold flex items-center justify-center gap-2 transition active:scale-98">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>📍 Detectar Mi Ubicación Actual</span>
                    </button>
                    <p class="text-xs text-gray-400 text-center">Puedes arrastrar el pin en el mapa para ajustar tu punto central de reparto.</p>
                </div>
            </div>

            <!-- 5. Datos Bancarios (Opcional) -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center text-white mr-3 shadow-md">5</span>
                    Datos de Cobro (Opcional)
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Banco / Billetera Virtual</label>
                        <input type="text" name="bank_name"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                            placeholder="Ej: Mercado Pago, Banco Nación..."
                            value="{{ old('bank_name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">CBU / CVU o Alias</label>
                        <input type="text" name="cbu_cvu"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                            placeholder="00000031000... o alias.mp"
                            value="{{ old('cbu_cvu') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Cuenta</label>
                        <select name="account_type"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition">
                            <option value="">Seleccionar...</option>
                            <option value="savings" {{ old('account_type') == 'savings' ? 'selected' : '' }}>Caja de Ahorro</option>
                            <option value="checking" {{ old('account_type') == 'checking' ? 'selected' : '' }}>Cuenta Corriente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Número de Cuenta</label>
                        <input type="text" name="account_number"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition"
                            placeholder="Opcional"
                            value="{{ old('account_number') }}">
                    </div>
                </div>
            </div>

            <!-- Botones de Envío -->
            <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                <button type="submit"
                    class="w-full sm:flex-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl active:scale-98 transition-all">
                    🚀 Enviar Solicitud de Repartidor
                </button>
                <a href="{{ route('home') }}"
                    class="w-full sm:w-auto px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-semibold text-center transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            // ==========================================
            // COMPRESIÓN Y UTILIDADES DE IMAGEN
            // ==========================================
            function formatBytes(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            async function compressImage(file, maxWidth = 1600, maxHeight = 1600, quality = 0.82) {
                if (!file || !file.type.startsWith('image/')) {
                    return file;
                }
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
                        img.src = e.target.result;
                    };
                    reader.onerror = () => resolve(file);
                    reader.readAsDataURL(file);
                });
            }

            // ==========================================
            // VARIABLES GLOBALES DE ARCHIVOS
            // ==========================================
            let currentDriverDniFile = null;
            let currentProfilePhotoFile = null;
            let currentVehiclePhotoFile = null;

            // ==========================================
            // MANEJO DNI
            // ==========================================
            function triggerDriverDniCamera() {
                const input = document.getElementById('dni_camera_input');
                if (input) input.click();
            }

            function triggerDriverDniGallery() {
                const input = document.getElementById('dni_gallery_input');
                if (input) input.click();
            }

            async function handleDriverDniInput(input) {
                if (!input.files || !input.files[0]) return;
                const rawFile = input.files[0];

                const preview = document.getElementById('dni_preview');
                const placeholder = document.getElementById('dni_placeholder');
                const badge = document.getElementById('dni_status_badge');
                const infoRow = document.getElementById('dni_info_row');
                const sizeLabel = document.getElementById('dni_file_size');

                // Asignar inmediatamente para asegurar que nunca esté vacío
                currentDriverDniFile = rawFile;

                if (sizeLabel) sizeLabel.innerText = '⌛ Optimizando foto...';
                if (infoRow) {
                    infoRow.classList.remove('hidden');
                    infoRow.classList.add('flex');
                }

                // Preview inmediato
                try {
                    preview.src = URL.createObjectURL(rawFile);
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    badge.classList.remove('hidden');
                } catch(e) {}

                try {
                    const compressed = await compressImage(rawFile, 1600, 1600, 0.82);
                    currentDriverDniFile = compressed;

                    try {
                        preview.src = URL.createObjectURL(compressed);
                    } catch(e) {}

                    try {
                        const dt = new DataTransfer();
                        dt.items.add(compressed);
                        document.getElementById('dni_photo').files = dt.files;
                    } catch(dtErr) {
                        // DataTransfer opcional en navegadores móviles
                    }

                    if (sizeLabel) sizeLabel.innerText = `Foto lista (${formatBytes(compressed.size)} - optimizada)`;
                } catch (e) {
                    console.warn('Error optimizando DNI, se conserva original:', e);
                    currentDriverDniFile = rawFile;
                    if (sizeLabel) sizeLabel.innerText = `Foto lista (${formatBytes(rawFile.size)})`;
                } finally {
                    input.value = '';
                }
            }

            function removeDriverDni() {
                currentDriverDniFile = null;
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

            // ==========================================
            // MANEJO FOTO DE PERFIL (SELFIE)
            // ==========================================
            function triggerProfileCamera() {
                const input = document.getElementById('profile_camera_input');
                if (input) input.click();
            }

            function triggerProfileGallery() {
                const input = document.getElementById('profile_gallery_input');
                if (input) input.click();
            }

            async function handleProfilePhotoInput(input) {
                if (!input.files || !input.files[0]) return;
                const rawFile = input.files[0];

                const preview = document.getElementById('profile_photo_preview');
                const placeholder = document.getElementById('profile_photo_placeholder');
                const btnRemove = document.getElementById('btn_remove_profile');
                const sizeLabel = document.getElementById('profile_file_size');

                currentProfilePhotoFile = rawFile;

                if (sizeLabel) sizeLabel.innerText = '⌛ Optimizando selfie...';

                try {
                    preview.src = URL.createObjectURL(rawFile);
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    if (btnRemove) btnRemove.classList.remove('hidden');
                } catch(e) {}

                try {
                    const compressed = await compressImage(rawFile, 1200, 1200, 0.82);
                    currentProfilePhotoFile = compressed;

                    try {
                        preview.src = URL.createObjectURL(compressed);
                    } catch(e) {}

                    try {
                        const dt = new DataTransfer();
                        dt.items.add(compressed);
                        document.getElementById('profile_photo').files = dt.files;
                    } catch(e) {}

                    if (sizeLabel) sizeLabel.innerText = `Selfie lista (${formatBytes(compressed.size)})`;
                } catch (e) {
                    console.warn('Error optimizando selfie, se conserva original:', e);
                    currentProfilePhotoFile = rawFile;
                    if (sizeLabel) sizeLabel.innerText = `Selfie lista (${formatBytes(rawFile.size)})`;
                } finally {
                    input.value = '';
                }
            }

            function removeProfilePhoto() {
                currentProfilePhotoFile = null;
                try {
                    const dt = new DataTransfer();
                    document.getElementById('profile_photo').files = dt.files;
                } catch(e) {}
                try {
                    document.getElementById('profile_photo').value = '';
                } catch(e) {}

                const preview = document.getElementById('profile_photo_preview');
                const placeholder = document.getElementById('profile_photo_placeholder');
                const btnRemove = document.getElementById('btn_remove_profile');
                const sizeLabel = document.getElementById('profile_file_size');

                preview.src = '';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                if (btnRemove) btnRemove.classList.add('hidden');
                if (sizeLabel) sizeLabel.innerText = '';
            }

            // ==========================================
            // MANEJO FOTO DEL VEHÍCULO
            // ==========================================
            function triggerVehicleCamera() {
                const input = document.getElementById('vehicle_camera_input');
                if (input) input.click();
            }

            function triggerVehicleGallery() {
                const input = document.getElementById('vehicle_gallery_input');
                if (input) input.click();
            }

            async function handleVehiclePhotoInput(input) {
                if (!input.files || !input.files[0]) return;
                const rawFile = input.files[0];

                const preview = document.getElementById('vehicle_preview');
                const placeholder = document.getElementById('vehicle_placeholder');
                const badge = document.getElementById('vehicle_status_badge');
                const infoRow = document.getElementById('vehicle_info_row');
                const sizeLabel = document.getElementById('vehicle_file_size');

                currentVehiclePhotoFile = rawFile;

                if (sizeLabel) sizeLabel.innerText = '⌛ Optimizando foto...';
                if (infoRow) {
                    infoRow.classList.remove('hidden');
                    infoRow.classList.add('flex');
                }

                try {
                    preview.src = URL.createObjectURL(rawFile);
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    badge.classList.remove('hidden');
                } catch(e) {}

                try {
                    const compressed = await compressImage(rawFile, 1600, 1600, 0.82);
                    currentVehiclePhotoFile = compressed;

                    try {
                        preview.src = URL.createObjectURL(compressed);
                    } catch(e) {}

                    try {
                        const dt = new DataTransfer();
                        dt.items.add(compressed);
                        document.getElementById('vehicle_photo').files = dt.files;
                    } catch(e) {}

                    if (sizeLabel) sizeLabel.innerText = `Foto lista (${formatBytes(compressed.size)})`;
                } catch (e) {
                    console.warn('Error optimizando foto vehículo:', e);
                    currentVehiclePhotoFile = rawFile;
                    if (sizeLabel) sizeLabel.innerText = `Foto lista (${formatBytes(rawFile.size)})`;
                } finally {
                    input.value = '';
                }
            }

            function removeVehiclePhoto() {
                currentVehiclePhotoFile = null;
                try {
                    const dt = new DataTransfer();
                    document.getElementById('vehicle_photo').files = dt.files;
                } catch(e) {}
                try {
                    document.getElementById('vehicle_photo').value = '';
                } catch(e) {}

                const preview = document.getElementById('vehicle_preview');
                const placeholder = document.getElementById('vehicle_placeholder');
                const badge = document.getElementById('vehicle_status_badge');
                const infoRow = document.getElementById('vehicle_info_row');

                preview.src = '';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                badge.classList.add('hidden');
                infoRow.classList.add('hidden');
                infoRow.classList.remove('flex');
            }

            // ==========================================
            // MAPA INTERACTIVO Y GEOLOCALIZACIÓN
            // ==========================================
            let driverMap, driverMarker, driverCircle;

            function initDriverMap() {
                const latInput = document.getElementById('location_lat');
                const lngInput = document.getElementById('location_lng');
                const radiusInput = document.getElementById('coverage_radius_km');

                let lat = parseFloat(latInput.value) || -32.6471;
                let lng = parseFloat(lngInput.value) || -63.0347;
                let radiusKm = parseFloat(radiusInput.value) || 5;

                driverMap = L.map('driver_map').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(driverMap);

                driverMarker = L.marker([lat, lng], { draggable: true }).addTo(driverMap);

                driverCircle = L.circle([lat, lng], {
                    radius: radiusKm * 1000,
                    color: '#3b82f6',
                    fillColor: '#60a5fa',
                    fillOpacity: 0.2
                }).addTo(driverMap);

                driverMarker.on('dragend', function() {
                    const pos = driverMarker.getLatLng();
                    updateDriverLocationData(pos.lat, pos.lng);
                });

                driverMap.on('click', function(e) {
                    driverMarker.setLatLng(e.latlng);
                    updateDriverLocationData(e.latlng.lat, e.latlng.lng);
                });

                radiusInput.addEventListener('input', function() {
                    const r = (parseFloat(this.value) || 1) * 1000;
                    if (driverCircle) driverCircle.setRadius(r);
                });
            }

            function updateDriverLocationData(lat, lng) {
                document.getElementById('location_lat').value = lat.toFixed(4);
                document.getElementById('location_lng').value = lng.toFixed(4);
                if (driverCircle) driverCircle.setLatLng([lat, lng]);
            }

            function detectDriverLocation() {
                if (navigator.geolocation) {
                    const btn = document.getElementById('btn_detect_location');
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '⌛ Detectando ubicación...';
                    btn.disabled = true;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            if (driverMarker && driverMap) {
                                driverMarker.setLatLng([lat, lng]);
                                driverMap.setView([lat, lng], 15);
                            }
                            updateDriverLocationData(lat, lng);

                            btn.innerHTML = '✓ ¡Ubicación detectada!';
                            setTimeout(() => {
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                            }, 2500);
                        },
                        (error) => {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                            alert('No se pudo obtener la ubicación automáticamente: ' + error.message);
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                } else {
                    alert('Tu dispositivo o navegador no soporta geolocalización');
                }
            }

            // ==========================================
            // CAMBIO DE TIPO DE VEHÍCULO
            // ==========================================
            document.getElementById('vehicle_type').addEventListener('change', function () {
                const plateContainer = document.getElementById('plate_container');
                const plateInput = document.getElementById('vehicle_plate');
                if (this.value === 'motorcycle' || this.value === 'car') {
                    plateContainer.style.display = 'block';
                    if (plateInput) plateInput.required = true;
                } else {
                    plateContainer.style.display = 'none';
                    if (plateInput) plateInput.required = false;
                }
            });

            // ==========================================
            // ENVÍO DEL FORMULARIO CON BARRA DE PROGRESO
            // ==========================================
            document.addEventListener('DOMContentLoaded', function() {
                initDriverMap();

                const form = document.getElementById('driverProfileForm');
                const overlay = document.getElementById('loadingOverlay');
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const dniInput = document.getElementById('dni_photo');
                    const hasDni = currentDriverDniFile || (dniInput && dniInput.files && dniInput.files.length > 0);
                    if (!hasDni) {
                        alert('⚠️ Por favor toma o selecciona la foto de tu DNI.');
                        window.scrollTo({ top: document.getElementById('dni_dropzone').offsetTop - 120, behavior: 'smooth' });
                        return;
                    }

                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                    progressBar.style.width = '10%';
                    progressText.innerText = 'Subiendo solicitud y fotos... 10%';

                    const formData = new FormData(form);

                    // Inyección explícita garantizada en FormData (100% compatible con móviles)
                    const dniToSend = currentDriverDniFile || (dniInput && dniInput.files && dniInput.files[0]);
                    if (dniToSend) {
                        formData.set('dni_photo', dniToSend, dniToSend.name || 'dni.jpg');
                    }
                    if (currentProfilePhotoFile) {
                        formData.set('profile_photo', currentProfilePhotoFile, currentProfilePhotoFile.name || 'perfil.jpg');
                    }
                    if (currentVehiclePhotoFile) {
                        formData.set('vehicle_photo', currentVehiclePhotoFile, currentVehiclePhotoFile.name || 'vehiculo.jpg');
                    }

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action, true);
                    xhr.setRequestHeader('Accept', 'application/json');

                    xhr.upload.onprogress = function(event) {
                        if (event.lengthComputable) {
                            const percent = Math.round((event.loaded / event.total) * 100);
                            progressBar.style.width = percent + '%';
                            progressText.innerText = `Subiendo fotos y datos: ${percent}%`;
                        }
                    };

                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            progressBar.style.width = '100%';
                            progressText.innerText = '¡Perfil creado con éxito!';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.redirect_url) {
                                    window.location.href = response.redirect_url;
                                } else {
                                    window.location.href = "{{ route('delivery-driver.dashboard') }}";
                                }
                            } catch(e) {
                                window.location.href = "{{ route('delivery-driver.dashboard') }}";
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
                            alert('Ocurrió un error al enviar el formulario. Verifica tu conexión e intenta nuevamente.');
                        }
                    };

                    xhr.onerror = function() {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                        alert('Error de red al intentar enviar el formulario. Verifica tu conexión.');
                    };

                    xhr.send(formData);
                });
            });
        </script>
    @endpush
@endsection