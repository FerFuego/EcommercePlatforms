@extends('layouts.app')

@section('title', 'Editar Plato: ' . $dish->name)

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2">
                <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                    Editar Plato
                </span>
            </h1>
            <p class="text-gray-600">Actualiza los detalles de tu plato</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden">
                    @if(auth()->user()->is_suspended)
                        <div
                            class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
                            <span
                                class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
                        </div>
                    @endif
                    <h3 class="text-xl font-bold mb-4">
                        Acciones Rápidas
                    </h3>
                    <div class="space-y-3 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none' : '' }}">
                        <a href="{{ route('cook.dashboard') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Dashboard
                        </a>
                        <a href="{{ route('cook.dishes.create') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Nuevo Plato
                        </a>
                        <a href="{{ route('cook.dishes.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Mis Platos
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <form action="{{ route('cook.dishes.update', $dish->id) }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col gap-8">
                    @csrf
                    @method('PUT')

                    <!-- Photo Upload -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 mt-0">
                        <h3 class="text-xl font-bold mb-4">Foto del Plato</h3>
                        <div class="flex items-center justify-center w-full">
                            <label for="photo"
                                class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gradient-to-br from-gray-50 to-pink-50 hover:bg-gradient-to-br hover:from-orange-50 hover:to-pink-100 transition-all overflow-hidden relative">
                                @if($dish->photo_url)
                                    <img src="{{ asset('uploads/' . $dish->photo_url) }}" alt="{{ $dish->name }}" id="preview-image"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Haz clic para
                                                subir</span> o arrastra</p>
                                    </div>
                                @endif
                                <input id="photo" name="photo" type="file" class="hidden" accept="image/*"
                                    onchange="previewImage(this)">
                            </label>
                        </div>
                        @error('photo')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Basic Info -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
                        <h3 class="text-xl font-bold">Información Básica</h3>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Plato *</label>
                            <input type="text" name="name" value="{{ old('name', $dish->name) }}" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                            <textarea name="description" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">{{ old('description', $dish->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Precio *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-gray-500 text-lg">$</span>
                                    <input type="number" name="price" value="{{ old('price', $dish->price) }}" required step="0.01"
                                        min="0"
                                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Disponible *</label>
                                <input type="number" name="available_stock" value="{{ old('available_stock', $dish->available_stock) }}" required
                                    min="0"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                            </div>
                        </div>

                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $dish->is_active ? 'checked' : '' }} class="w-5 h-5 text-purple-600 rounded">
                                <span class="ml-2 text-gray-700 font-semibold">Plato Activo</span>
                            </label>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
                        <h3 class="text-xl font-bold">Disponibilidad</h3>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Días Disponibles</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @php
                                    $days = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                    $availableDays = $dish->available_days ?? [];
                                @endphp
                                @foreach($days as $num => $name)
                                    <label class="flex items-center p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-purple-50 transition">
                                        <input type="checkbox" name="available_days[]" value="{{ $num }}"
                                            {{ in_array($num, $availableDays) ? 'checked' : '' }}
                                            class="w-5 h-5 text-purple-600 rounded">
                                        <span class="ml-2 text-sm font-medium">{{ $name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tiempo de Preparación (minutos) *</label>
                            <input type="number" name="preparation_time_minutes"
                                value="{{ old('preparation_time_minutes', $dish->preparation_time_minutes) }}" required min="10"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Método de Entrega *</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <label class="flex items-center p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-blue-50 transition border-2 border-transparent has-[:checked]:border-blue-500">
                                    <input type="radio" name="delivery_method" value="pickup" {{ $dish->delivery_method == 'pickup' ? 'checked' : '' }} class="w-5 h-5 text-blue-600">
                                    <span class="ml-3 font-medium">Solo Retiro</span>
                                </label>
                                <label class="flex items-center p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-purple-50 transition border-2 border-transparent has-[:checked]:border-purple-500">
                                    <input type="radio" name="delivery_method" value="delivery" {{ $dish->delivery_method == 'delivery' ? 'checked' : '' }} class="w-5 h-5 text-purple-600">
                                    <span class="ml-3 font-medium">Solo Delivery</span>
                                </label>
                                <label class="flex items-center p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-green-50 transition border-2 border-transparent has-[:checked]:border-green-500">
                                    <input type="radio" name="delivery_method" value="both" {{ $dish->delivery_method == 'both' ? 'checked' : '' }} class="w-5 h-5 text-green-600">
                                    <span class="ml-3 font-medium">Ambos</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Diet Tags -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
                        <h3 class="text-xl font-bold">Etiquetas de Dieta</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $dietOptions = [
                                    'vegetarian' => '🥬 Vegetariano',
                                    'vegan' => '🌱 Vegano',
                                    'gluten-free' => '🌾 Sin Gluten',
                                    'lactose-free' => '🥛 Sin Lactosa',
                                    'low-carb' => '🥗 Bajo en Carbos',
                                    'spicy' => '🌶️ Picante'
                                ];
                                $selectedTags = $dish->diet_tags ?? [];
                            @endphp
                            @foreach($dietOptions as $value => $label)
                                <label class="flex items-center p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-green-50 transition">
                                    <input type="checkbox" name="diet_tags[]" value="{{ $value }}"
                                        {{ in_array($value, $selectedTags) ? 'checked' : '' }}
                                        class="w-5 h-5 text-green-600 rounded">
                                    <span class="ml-2 text-sm">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Options and Varieties -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold">Opciones y Variedades</h3>
                            <button type="button" onclick="addGroup()"
                                class="bg-purple-100 text-purple-600 px-4 py-2 rounded-xl font-semibold hover:bg-purple-200 transition flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Agregar Grupo
                            </button>
                        </div>
                        <p class="text-sm text-gray-500">Ejemplo: "Gusto de Empanada", "Término de la carne", "Extras".</p>

                        <div id="option-groups-container" class="space-y-6">
                            @foreach($dish->optionGroups as $group)
                                @include('cook.dishes.partials.option-group-row', ['group' => $group, 'groupId' => $loop->index])
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center space-x-4">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                            Guardar Cambios
                        </button>
                        <a href="{{ route('cook.dishes.index') }}"
                            class="px-8 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.getElementById('preview-image');
                        if (img) {
                           img.src = e.target.result;
                        } else {
                           // Handle case where img doesn't exist yet (uploading first time)
                           location.reload(); // Simple fix for now
                        }
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            let groupCount = {{ $dish->optionGroups->count() }};

            function addGroup() {
                const container = document.getElementById('option-groups-container');
                const groupId = groupCount++;
                
                // For simplicity in the first pass of edit, I'll use a template approach or just JS injection
                // Since I'm using partials for existing ones, I'll need a way to inject new ones too.
                // Reusing the create logic here:
                
                const groupHtml = `
                    <div class="group-container bg-gray-50 rounded-2xl p-6 border-2 border-gray-100 animate-fade-in" id="group-${groupId}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1 mr-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Grupo</label>
                                <input type="text" name="option_groups[${groupId}][name]" required
                                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-purple-500 transition"
                                    placeholder="Ej: Elegí el gusto">
                            </div>
                            <button type="button" onclick="removeElement('group-${groupId}')" class="text-red-500 hover:text-red-700 p-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Mín. Opciones</label>
                                <input type="number" name="option_groups[${groupId}][min_options]" value="0" min="0"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Máx. Opciones</label>
                                <input type="number" name="option_groups[${groupId}][max_options]" value="1" min="1"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="option_groups[${groupId}][is_required]" value="1" class="w-4 h-4 text-purple-600 rounded">
                                    <span class="ml-2 text-xs font-semibold text-gray-600">Es Obligatorio</span>
                                </label>
                            </div>
                        </div>

                        <div class="ml-4 pl-4 border-l-2 border-purple-100">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold text-gray-700">Opciones Individuales</h4>
                                <button type="button" onclick="addOption(${groupId})"
                                    class="text-xs bg-white text-purple-600 px-3 py-1 rounded-lg border border-purple-200 hover:bg-purple-50 transition">
                                    + Agregar Opción
                                </button>
                            </div>
                            <div id="options-container-${groupId}" class="space-y-2"></div>
                        </div>
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', groupHtml);
                addOption(groupId);
            }

            let optionCount = 1000; // High start to avoid collision with existing IDs if I use them, but here I'm using array indices

            function addOption(groupId) {
                const container = document.getElementById(`options-container-${groupId}`);
                const optionId = optionCount++;
                
                const optionHtml = `
                    <div class="flex items-center gap-3 animate-fade-in" id="option-${optionId}">
                        <div class="flex-1">
                            <input type="text" name="option_groups[${groupId}][options][${optionId}][name]" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                                placeholder="Nombre de la opción">
                        </div>
                        <div class="w-32">
                            <div class="relative">
                                <span class="absolute left-2 top-2 text-gray-400 text-xs">$</span>
                                <input type="number" name="option_groups[${groupId}][options][${optionId}][additional_price]" value="0" step="0.01" min="0"
                                    class="w-full pl-5 pr-2 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                        <button type="button" onclick="removeElement('option-${optionId}')" class="text-red-400 hover:text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', optionHtml);
            }

            function removeElement(id) {
                document.getElementById(id).remove();
            }
        </script>
    @endpush

@endsection
