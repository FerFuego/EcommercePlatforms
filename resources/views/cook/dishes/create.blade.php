@extends('layouts.app')

@section('title', 'Crear Nuevo Plato')

@section('content')
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2">
                <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                    Crear Nuevo Plato
                </span>
            </h1>
            <p class="text-gray-600">Agrega un plato delicioso a tu menú</p>
        </div>

        <form action="{{ route('cook.dishes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Photo Upload -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold mb-4">Foto del Plato *</h3>
                <div class="flex items-center justify-center w-full">
                    <label for="photo"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gradient-to-br from-gray-50 to-pink-50 hover:bg-gradient-to-br hover:from-orange-50 hover:to-pink-100 transition-all">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Haz clic para subir</span> o
                                arrastra</p>
                            <p class="text-xs text-gray-500">PNG, JPG (MAX. 2MB)</p>
                        </div>
                        <input id="photo" name="photo" type="file" class="hidden" accept="image/*" required
                            onchange="previewImage(this)">
                    </label>
                </div>
                <div id="preview" class="mt-4 hidden">
                    <img id="preview-image" class="w-full h-64 object-cover rounded-2xl shadow-lg">
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
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                        placeholder="Ej: Lasagna de Carne">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                        placeholder="Describe los ingredientes, sabor, porción...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Precio *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500 text-lg">$</span>
                            <input type="number" name="price" value="{{ old('price') }}" required step="0.01" min="0"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                placeholder="1200">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Disponible Hoy *</label>
                        <input type="number" name="available_stock" value="{{ old('available_stock') }}" required min="0"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                            placeholder="10">
                        @error('available_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Availability -->
            <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
                <h3 class="text-xl font-bold">Disponibilidad</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Días Disponibles</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            $days = [
                                1 => 'Lunes',
                                2 => 'Martes',
                                3 => 'Miércoles',
                                4 => 'Jueves',
                                5 => 'Viernes',
                                6 => 'Sábado',
                                7 => 'Domingo'
                            ];
                        @endphp
                        @foreach($days as $num => $name)
                            <label
                                class="flex items-center p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-purple-50 transition">
                                <input type="checkbox" name="available_days[]" value="{{ $num }}"
                                    class="w-5 h-5 text-purple-600 rounded">
                                <span class="ml-2 text-sm font-medium">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Deja vacío si está disponible todos los días</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tiempo de Preparación (minutos) *</label>
                    <input type="number" name="preparation_time_minutes" value="{{ old('preparation_time_minutes', 30) }}"
                        required min="10"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                        placeholder="30">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Método de Entrega *</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label
                            class="flex items-center p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-blue-50 transition border-2 border-transparent has-[:checked]:border-blue-500">
                            <input type="radio" name="delivery_method" value="pickup" class="w-5 h-5 text-blue-600">
                            <span class="ml-3 font-medium">Solo Retiro</span>
                        </label>
                        <label
                            class="flex items-center p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-purple-50 transition border-2 border-transparent has-[:checked]:border-purple-500">
                            <input type="radio" name="delivery_method" value="delivery" class="w-5 h-5 text-purple-600">
                            <span class="ml-3 font-medium">Solo Delivery</span>
                        </label>
                        <label
                            class="flex items-center p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-green-50 transition border-2 border-transparent has-[:checked]:border-green-500">
                            <input type="radio" name="delivery_method" value="both" checked class="w-5 h-5 text-green-600">
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
                    @endphp
                    @foreach($dietOptions as $value => $label)
                        <label class="flex items-center p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-green-50 transition">
                            <input type="checkbox" name="diet_tags[]" value="{{ $value }}"
                                class="w-5 h-5 text-green-600 rounded">
                            <span class="ml-2 text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center space-x-4">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                    Crear Plato
                </button>
                <a href="{{ route('cook.dishes.index') }}"
                    class="px-8 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('preview').classList.remove('hidden');
                        document.getElementById('preview-image').src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush

@endsection