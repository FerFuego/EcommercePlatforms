@extends('layouts.app')

@section('title', 'Explorar Cocineros')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-pink-50 to-purple-50 py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-5xl font-bold mb-4">
                    <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Encuentra Cocineros Cerca de Ti
                    </span>
                </h1>
                <p class="text-gray-600 text-lg">Comida casera auténtica en tu zona</p>
            </div>

            <!-- Location Search Bar -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tu Ubicación</label>
                        <input type="text" id="locationSearch"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition"
                            placeholder="Ingresa tu dirección o usa ubicación actual">
                    </div>
                    <div class="w-full md:w-48">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Radio (km)</label>
                        <select id="radiusSelect"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition">
                            <option value="1" {{ $radius == 1 ? 'selected' : '' }}>1 km</option>
                            <option value="2" {{ $radius == 2 ? 'selected' : '' }}>2 km</option>
                            <option value="5" {{ $radius == 5 ? 'selected' : '' }}>5 km</option>
                            <option value="10" {{ $radius == 10 ? 'selected' : '' }}>10 km</option>
                            <option value="15" {{ $radius == 15 ? 'selected' : '' }}>15 km</option>
                            <option value="20" {{ $radius == 20 ? 'selected' : '' }}>20 km</option>
                            @if($radius == 50)
                                <option value="50" selected>50 km (Expandido)</option>
                            @endif
                        </select>
                    </div>
                    <div class="w-full md:w-auto">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label>
                        <button id="useCurrentLocation"
                            class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all w-full flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Usar Mi Ubicación
                        </button>
                    </div>
                </div>

                <!-- Current Location Display -->
                <div id="currentLocationDisplay" class="mt-4 hidden">
                    <div
                        class="flex items-center text-sm text-gray-600 bg-gradient-to-r from-green-50 to-emerald-50 p-3 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>Ubicación detectada: <strong id="locationText"></strong></span>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <div id="map" class="h-[500px] w-full"></div>
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-orange-500 rounded-full mr-2 shadow"></div>
                            <span class="text-sm text-gray-600">Cocineros Disponibles</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-2 shadow"></div>
                            <span class="text-sm text-gray-600">Tu Ubicación</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 border-2 border-purple-400 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Radio de Cobertura</span>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">
                        <span id="cookCount">{{ count($cooks) }}</span> cocineros encontrados
                    </span>
                </div>
            </div>

            <!-- Filters -->
            <!-- Filters Form -->
            <form id="filterForm" action="{{ route('marketplace.catalog') }}" method="GET"
                class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <input type="hidden" name="lat" id="latInput" value="{{ $lat }}">
                <input type="hidden" name="lng" id="lngInput" value="{{ $lng }}">
                <input type="hidden" name="radius" id="radiusInput" value="{{ $radius }}">

                <h3 class="font-bold text-lg mb-4 text-gray-800">Filtros</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar cocinero o plato..."
                            class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-purple-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Dieta</label>
                        <select name="diet"
                            class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-purple-500 transition">
                            <option value="">Todas</option>
                            <option value="vegetarian" {{ request('diet') == 'vegetarian' ? 'selected' : '' }}>Vegetariana
                            </option>
                            <option value="vegan" {{ request('diet') == 'vegan' ? 'selected' : '' }}>Vegana</option>
                            <option value="gluten-free" {{ request('diet') == 'gluten-free' ? 'selected' : '' }}>Sin Gluten
                            </option>
                            <option value="low-carb" {{ request('diet') == 'low-carb' ? 'selected' : '' }}>Bajo en Carbos
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Precio Máximo</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="$0"
                            class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-purple-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ordenar Por</label>
                        <select name="sort"
                            class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-purple-500 transition">
                            <option value="distance" {{ request('sort') == 'distance' ? 'selected' : '' }}>Distancia</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Calificación</option>
                            <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Precio (Menor a Mayor)
                            </option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Cooks Grid Container -->
            <div id="cooksContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if($cooks->isEmpty())
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">👨‍🍳</div>
                        <p class="text-xl text-gray-500">No hay cocineros disponibles con estos filtros</p>
                        <p class="text-gray-400 mt-2">Intenta ampliar el radio de búsqueda o cambiar los filtros</p>
                    </div>
                @else
                    @include('marketplace.partials.cook-items')
                @endif
            </div>

            <!-- Sentinel for Infinite Scroll -->
            <div id="sentinel" class="mt-8 text-center py-4">
                <div id="spinner"
                    class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 hidden"></div>
            </div>
            <div id="loadingOverlay"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-2xl p-8 shadow-2xl">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600 mb-4"></div>
                        <p class="text-gray-700 font-semibold">Buscando cocineros cercanos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let map;
            let userMarker;
            let cookMarkers = [];
            let coverageCircles = [];
            let userLocation = null;
            let userCoverageCircle = null;

            // Initialize map
            function initMap() {
                // Default to Bell Ville, Córdoba
                const defaultLat = {{ $lat ?? '-32.6471' }};
                const defaultLng = {{ $lng ?? '-63.0347' }};

                map = L.map('map').setView([defaultLat, defaultLng], 16);
                map.setMaxZoom(20);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Add cooks to map (Using mapCooks variable which contains ALL matching cooks, not just paginated ones)
                @foreach($mapCooks as $cook)
                    addCookToMap({
                        id: {{ $cook->id }},
                        name: '{{ $cook->user->name }}',
                        lat: {{ $cook->location_lat }},
                        lng: {{ $cook->location_lng }},
                        rating: {{ $cook->rating_avg }},
                        radius: {{ $cook->coverage_radius_km }},
                        photo_path: '{{ $cook->user->profile_photo_path }}'
                    });
                @endforeach
                // Intentar tomar ubicación real del usuario al cargar
                    if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            // Actualizar inputs ocultos del filtro
                            document.getElementById('latInput').value = lat.toFixed(4);
                            document.getElementById('lngInput').value = lng.toFixed(4);

                            // Mostrar ubicación en mapa
                            showUserLocation(lat, lng);

                            // Forzar búsqueda actualizada cerca del usuario
                            submitFilterForm();
                        },
                        (error) => {
                            console.warn("No se pudo obtener tu ubicación, usando default.");
                            showUserLocation(defaultLat, defaultLng);
                        }
                    );
                } else {
                    // Sin geolocalización → usar default
                    showUserLocation(defaultLat, defaultLng);
                }

            }

            // Add cook marker to map
            function addCookToMap(cook) {

                // filtrar por radio (evito cocineros, fuera del radio)
                const radiusKm = parseInt(document.getElementById("radiusSelect").value);
                if (userLocation) {
                    const dist = calculateDistance(userLocation.lat, userLocation.lng, cook.lat, cook.lng);
                    if (dist > radiusKm) return; // fuera del radio -> no agregar marker
                }
                // Fin (evito cocineros, fuera del radio)

                const customIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div class="bg-orange-500 w-10 h-10 rounded-full flex items-center justify-center shadow-lg border-4 border-white transform hover:scale-110 transition"><span class="text-white font-bold text-lg">🍳</span></div>`,
                    iconSize: [40, 40]
                });

                let imageHtml = '';
                if (cook.photo_path) {
                    imageHtml = `<img src="/storage/${cook.photo_path}" class="w-16 h-16 rounded-full object-cover mx-auto mb-2 border-2 border-orange-500 shadow-sm">`;
                } else {
                    imageHtml = `<div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2 border-2 border-orange-500 text-orange-600 font-bold text-xl shadow-sm">${cook.name.charAt(0).toUpperCase()}</div>`;
                }

                const marker = L.marker([cook.lat, cook.lng], { icon: customIcon })
                    .bindPopup(`<div class="text-center min-w-[160px] p-2">${imageHtml}<strong class="text-lg block text-gray-800 leading-tight mb-1">${cook.name}</strong><div class="flex items-center justify-center mb-2"><svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" /></svg><span class="font-bold text-gray-700">${cook.rating.toFixed(1)}</span></div><a href="/marketplace/cook/${cook.id}" class="inline-block w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:shadow-md transition-all transform hover:-translate-y-0.5">Ver Perfil</a></div>`)
                    .addTo(map);

                cookMarkers.push({ marker, data: cook });
            }

            // Show user location on map
            function showUserLocation(lat, lng) {
                if (userMarker) {
                    map.removeLayer(userMarker);
                }

                const userIcon = L.divIcon({
                    className: 'user-marker',
                    html: `<div class="bg-blue-500 w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-4 border-white animate-pulse">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>`,
                    iconSize: [32, 32]
                });

                userMarker = L.marker([lat, lng], { icon: userIcon })
                    .bindPopup('<strong>Tu Ubicación</strong>')
                    .addTo(map);

                map.setView([lat, lng], 15);
                userLocation = { lat, lng };

                // FER
                // Remove previous coverage circle
                if (userCoverageCircle) {
                    map.removeLayer(userCoverageCircle);
                }

                // Draw new coverage circle based on selected radius
                const selectedRadius = parseInt(document.getElementById("radiusSelect").value);

                userCoverageCircle = L.circle([lat, lng], {
                    color: '#a855f7',
                    fillColor: '#c084fc',
                    fillOpacity: 0.1,
                    radius: selectedRadius * 1000 // km to meters
                }).addTo(map);
                // End fer


                // Update distances
                updateDistances(lat, lng);
            }

            // Use current location
            document.getElementById('useCurrentLocation').addEventListener('click', function () {
                if (navigator.geolocation) {
                    document.getElementById('loadingOverlay').classList.remove('hidden');

                    navigator.geolocation.getCurrentPosition(function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        updateLocationAndFetch(lat, lng, 'Tu ubicación actual');

                    }, function (error) {
                        alert('No se pudo obtener tu ubicación. Por favor, verifica los permisos.');
                        document.getElementById('loadingOverlay').classList.add('hidden');
                    });
                }
            });

            // Handle location search (Geocoding)
            const locationInput = document.getElementById('locationSearch');
            locationInput.addEventListener('change', function () {
                geocodeAddress(this.value);
            });

            locationInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent form submission if inside form (it's not, but good practice)
                    geocodeAddress(this.value);
                }
            });

            function geocodeAddress(address) {
                if (!address.trim()) return;

                document.getElementById('loadingOverlay').classList.remove('hidden');

                // Use Nominatim (OpenStreetMap) for geocoding
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            const displayName = data[0].display_name;

                            updateLocationAndFetch(lat, lng, displayName);
                        } else {
                            alert('No se encontró la dirección. Intenta ser más específico.');
                            document.getElementById('loadingOverlay').classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                        alert('Error al buscar la dirección.');
                        document.getElementById('loadingOverlay').classList.add('hidden');
                    });
            }

            function updateLocationAndFetch(lat, lng, label) {
                // Update hidden inputs
                document.getElementById('latInput').value = lat.toFixed(4);
                document.getElementById('lngInput').value = lng.toFixed(4);

                // Update UI
                document.getElementById('locationText').textContent = label;
                document.getElementById('currentLocationDisplay').classList.remove('hidden');
                document.getElementById('locationSearch').value = label; // Optional: update input with full address

                // Update map user marker
                showUserLocation(lat, lng);

                // Submit filters
                submitFilterForm();
            }

            // Update distances in cook cards (Client side calculation for display only)
            function updateDistances(userLat, userLng) {
                document.querySelectorAll('.cook-card').forEach(card => {
                    const cookLat = parseFloat(card.dataset.lat);
                    const cookLng = parseFloat(card.dataset.lng);

                    const distance = calculateDistance(userLat, userLng, cookLat, cookLng);
                    const deliveryFee = calculateDeliveryFee(distance);

                    const distanceBadge = card.querySelector('.distance-badge');
                    const distanceValue = card.querySelector('.distance-value');
                    const deliveryFeeBadge = card.querySelector('.delivery-fee-badge');

                    distanceBadge.classList.remove('hidden');
                    distanceValue.textContent = distance.toFixed(1);

                    if (deliveryFee === 0) {
                        deliveryFeeBadge.textContent = 'Envío Gratis';
                        deliveryFeeBadge.className = 'delivery-fee-badge text-xs font-bold px-3 py-1 rounded-full bg-green-100 text-green-700';
                    } else {
                        deliveryFeeBadge.textContent = `Envío $${deliveryFee}`;
                        deliveryFeeBadge.className = 'delivery-fee-badge text-xs font-bold px-3 py-1 rounded-full bg-orange-100 text-orange-700';
                    }
                });
            }

            // Calculate distance using Haversine formula
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371; // Earth radius in km
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            // Calculate delivery fee based on distance
            function calculateDeliveryFee(distance) {
                if (distance <= 2) return 0;
                if (distance <= 5) return 200;
                if (distance <= 10) return 400;
                return 600;
            }

            let nextPageUrl = '{{ $cooks->nextPageUrl() }}';
            let isLoading = false;

            // Handle form submission via AJAX
            const filterForm = document.getElementById('filterForm');

            filterForm.addEventListener('submit', function (e) {
                e.preventDefault();
                submitFilterForm();
            });

            // Trigger submit on select change
            filterForm.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', submitFilterForm);
            });

            // Trigger submit on input with debounce
            const debouncedSubmit = debounce(() => submitFilterForm(), 500);
            filterForm.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                input.addEventListener('input', function () {
                    if (this.name === 'search') {
                        const val = this.value.trim();
                        // If not empty and less than 3 chars, do nothing
                        if (val.length > 0 && val.length < 3) return;
                    }
                    debouncedSubmit();
                });
            });

            function submitFilterForm() {
                const url = filterForm.action + '?' + new URLSearchParams(new FormData(filterForm)).toString();
                fetchCooks(url, true); // true = replace content
            }

            // Debounce helper
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Fetch cooks function
            function fetchCooks(url, replace = false) {
                if (isLoading || !url) return;
                isLoading = true;

                if (replace) {
                    document.getElementById('loadingOverlay').classList.remove('hidden');
                } else {
                    document.getElementById('spinner').classList.remove('hidden');
                }

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update Grid
                        if (replace) {
                            document.getElementById('cooksContainer').innerHTML = data.html;
                            // If empty html and replace, show empty state (handled by controller returning empty view? No, controller returns items)
                            // If data.html is empty string, we should show empty state manually if replace is true
                            if (!data.html.trim()) {
                                document.getElementById('cooksContainer').innerHTML = `<div class="col-span-full text-center py-12"><div class="text-6xl mb-4">👨‍🍳</div><p class="text-xl text-gray-500">No hay cocineros disponibles con estos filtros</p><p class="text-gray-400 mt-2">Intenta ampliar el radio de búsqueda o cambiar los filtros</p></div>`;
                            }
                        } else {
                            document.getElementById('cooksContainer').insertAdjacentHTML('beforeend', data.html);
                        }

                        // Update Map (only on filter change/replace)
                        if (replace && data.mapCooks) {
                            updateMapMarkers(data.mapCooks);
                        }

                        // Update URL history (only on replace, to keep filter state)
                        if (replace) {
                            window.history.pushState({}, '', url);
                        }

                        // Handle expanded radius
                        if (data.expandedRadius) {
                            // Update UI
                            const radiusSelect = document.getElementById('radiusSelect');
                            const radiusInput = document.getElementById('radiusInput');

                            // Add 50km option if not exists
                            if (!radiusSelect.querySelector('option[value="50"]')) {
                                const option = document.createElement('option');
                                option.value = "50";
                                option.text = "50 km";
                                radiusSelect.add(option);
                            }

                            radiusSelect.value = data.newRadius;
                            radiusInput.value = data.newRadius;

                            // Show notification
                            showToast(`No encontramos cocineros en ${getUrlParameter('radius') || 10}km. Ampliamos la búsqueda a 50km.`);
                        }

                        // Update distances for new cards
                        if (userLocation) {
                            updateDistances(userLocation.lat, userLocation.lng);
                        }

                        nextPageUrl = data.next_page_url;
                        isLoading = false;
                        document.getElementById('loadingOverlay').classList.add('hidden');
                        document.getElementById('spinner').classList.add('hidden');

                        // Re-observe sentinel if there is a next page
                        if (nextPageUrl) {
                            observer.observe(document.getElementById('sentinel'));
                        } else {
                            observer.unobserve(document.getElementById('sentinel'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        isLoading = false;
                        document.getElementById('loadingOverlay').classList.add('hidden');
                        document.getElementById('spinner').classList.add('hidden');
                    });
            }

            // Infinite Scroll Observer
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && nextPageUrl) {
                    fetchCooks(nextPageUrl, false); // false = append content
                }
            }, { rootMargin: '100px' });

            if (nextPageUrl) {
                observer.observe(document.getElementById('sentinel'));
            }

            // Update map markers
            function updateMapMarkers(cooks) {

                // borrar anteriores
                cookMarkers.forEach(({ marker }) => map.removeLayer(marker));
                cookMarkers = [];

                const radiusKm = parseInt(document.getElementById("radiusSelect").value);

                cooks.forEach(cook => {

                    if (userLocation) {
                        const dist = calculateDistance(userLocation.lat, userLocation.lng, cook.location_lat, cook.location_lng);

                        if (dist > radiusKm) return; // SALTAR markers lejos
                    }

                    addCookToMap({
                        id: cook.id,
                        name: cook.user?.name || "Cocinero",
                        lat: parseFloat(cook.location_lat),
                        lng: parseFloat(cook.location_lng),
                        rating: parseFloat(cook.rating_avg || 0),
                        radius: parseFloat(cook.coverage_radius_km),
                        photo_path: cook.user?.profile_photo_path ?? null
                    });
                });

                document.getElementById('cookCount').textContent = cookMarkers.length;
            }

            // Radius change
            document.getElementById('radiusSelect').addEventListener('change', function () {
                const radius = this.value;
                document.getElementById('radiusInput').value = radius;
                submitFilterForm();
            });

            // Initialize on load
            document.addEventListener('DOMContentLoaded', function () {
                initMap();

                @if(isset($expandedRadius) && $expandedRadius)
                    showToast('No encontramos cocineros en el radio seleccionado. Ampliamos la búsqueda a 50km.');
                @endif
                });
            // Reinit radius on radius change
            document.getElementById('radiusSelect').addEventListener('change', function () {
                const radius = this.value;
                document.getElementById('radiusInput').value = radius;

                // 🔥 Redibujar el círculo del usuario si ya tenemos ubicación
                if (userLocation) {
                    showUserLocation(userLocation.lat, userLocation.lng);
                }

                submitFilterForm();
            });

            function showToast(message) {
                // Create toast element
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-full z-50';
                toast.textContent = message;
                document.body.appendChild(toast);

                // Animate in
                setTimeout(() => toast.classList.remove('translate-y-full'), 100);

                // Remove after 4s
                setTimeout(() => {
                    toast.classList.add('translate-y-full');
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            }

            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }
        </script>
    @endpush
@endsection