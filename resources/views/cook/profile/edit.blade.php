@extends('layouts.app')

@section('title', 'Editar Perfil de Cocinero')

@section('content')
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Editar Perfil</h1>
            ← Volver al Dashboard
            </a>
        </div>

        @if(auth()->user()->is_suspended)
            <div class="bg-red-500 text-white px-6 py-4 rounded-2xl shadow-lg mb-8">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-bold text-lg">Cuenta Suspendida</h3>
                        <p>Tu cuenta está suspendida. Puedes editar tu perfil, pero no podrás realizar otras acciones hasta
                            contactar a soporte.</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('cook.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Personal Info -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Información General</h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Biografía / Descripción</label>
                        <textarea name="bio" rows="4" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">{{ old('bio', $cook->bio) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Radio de Cobertura (km)</label>
                            <input type="number" name="coverage_radius_km"
                                value="{{ old('coverage_radius_km', $cook->coverage_radius_km) }}" required min="1" max="50"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center cursor-pointer">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="active" value="1" {{ $cook->active ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-purple-500 peer-checked:to-pink-600">
                                    </div>
                                </label>
                                <span class="ml-3 text-gray-700 font-semibold">Perfil Activo (Recibir Pedidos)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Location Map -->
                    <div class="border-t border-gray-100 pt-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">Ubicación de la Cocina</label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="text-xs text-gray-500">Latitud</label>
                                <input type="text" name="location_lat" id="location_lat"
                                    value="{{ old('location_lat', $cook->location_lat) }}" readonly
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Longitud</label>
                                <input type="text" name="location_lng" id="location_lng"
                                    value="{{ old('location_lng', $cook->location_lng) }}" readonly
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>

                        <div id="map" class="h-[300px] w-full rounded-xl shadow-md mb-4 z-0"></div>

                        <button type="button" onclick="detectLocation()"
                            class="w-full bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-100 transition flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Usar Mi Ubicación Actual
                        </button>
                        <p class="text-xs text-gray-500 mt-2 text-center">Arrastra el marcador para ajustar la posición
                            exacta</p>
                    </div>
                </div>
            </div>

            <!-- Gallery Management -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Galería de Mi Cocina</h2>

                <!-- Current Photos -->
                @if($cook->kitchen_photos && count($cook->kitchen_photos) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8">
                        @foreach($cook->kitchen_photos as $photo)
                            <div class="relative group" id="photo-{{ md5($photo) }}">
                                <img src="{{ Storage::url($photo) }}" alt="Cocina"
                                    class="w-full h-48 object-cover rounded-xl shadow-md">
                                <button type="button" onclick="deletePhoto('{{ $photo }}', 'photo-{{ md5($photo) }}')"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic mb-6">No tienes fotos subidas actualmente.</p>
                @endif

                <!-- Upload New -->
                <div class="border-t border-gray-100 pt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Agregar Nuevas Fotos</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="kitchen_photos"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:bg-purple-50 transition-all">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <p class="text-sm text-gray-500">Click para subir fotos adicionales</p>
                            </div>
                            <input id="kitchen_photos" name="kitchen_photos[]" type="file" class="hidden" accept="image/*"
                                multiple>
                        </label>
                    </div>

                    <!-- Image Preview Container -->
                    <div id="preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 hidden">
                        <!-- Previews will be injected here -->
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .toggle-bg {
                transition: background-color 0.2s ease-in-out;
            }

            input:checked~.toggle-bg {
                background-color: #9333ea;
            }

            input:checked~.dot {
                transform: translateX(100%);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function deletePhoto(photoPath, elementId) {
                if (!confirm('¿Estás seguro de querer eliminar esta foto?')) return;

                fetch('{{ route("cook.profile.photo.delete") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ photo: photoPath })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(elementId).remove();
                        } else {
                            alert('Error al eliminar la foto');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al procesar la solicitud');
                    });
            }

            // Image Preview Logic
            document.getElementById('kitchen_photos').addEventListener('change', function (e) {
                const container = document.getElementById('preview-container');
                container.innerHTML = ''; // Clear previous previews

                if (this.files && this.files.length > 0) {
                    container.classList.remove('hidden');

                    Array.from(this.files).forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const div = document.createElement('div');
                                div.className = 'relative group animate-fade-in';
                                div.innerHTML = `
                                                                                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-xl shadow-md border-2 border-purple-100">
                                                                                    <div class="absolute top-2 right-2">
                                                                                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm">Nuevo</span>
                                                                                    </div>
                                                                                `;
                                container.appendChild(div);
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                } else {
                    container.classList.add('hidden');
                }
            });

            // Map Logic
            let map, marker;

            function initMap() {
                const lat = parseFloat(document.getElementById('location_lat').value) || -32.6471;
                const lng = parseFloat(document.getElementById('location_lng').value) || -63.0347;

                map = L.map('map').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', function (event) {
                    const position = marker.getLatLng();
                    updateInputs(position.lat, position.lng);
                });

                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    updateInputs(e.latlng.lat, e.latlng.lng);
                });
            }

            function updateInputs(lat, lng) {
                document.getElementById('location_lat').value = lat.toFixed(4);
                document.getElementById('location_lng').value = lng.toFixed(4);
            }

            function detectLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        updateInputs(lat, lng);
                        marker.setLatLng([lat, lng]);
                        map.setView([lat, lng], 15);
                    }, error => {
                        alert('No se pudo detectar la ubicación.');
                    });
                } else {
                    alert('Tu navegador no soporta geolocalización');
                }
            }

            // Initialize map when DOM is ready
            document.addEventListener('DOMContentLoaded', initMap);
        </script>
    @endpush
@endsection