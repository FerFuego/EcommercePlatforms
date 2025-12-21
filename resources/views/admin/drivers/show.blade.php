@extends('layouts.admin')

@section('title', 'Detalle Repartidor - Admin')

@section('content')
    <div class="container mx-auto px-4 py-12 max-w-6xl">
        <div class="mb-6">
            <a href="{{ route('admin.drivers.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                ← Volver a Repartidores
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Header -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-start space-x-6">
                        @if($driver->profile_photo)
                            <img src="{{ Storage::url($driver->profile_photo) }}" alt="{{ $driver->user->name }}"
                                class="w-32 h-32 object-cover rounded-full">
                        @else
                            <div
                                class="w-32 h-32 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                                {{ substr($driver->user->name, 0, 1) }}
                            </div>
                        @endif

                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-4">
                                <h1 class="text-3xl font-bold">{{ $driver->user->name }}</h1>
                                <span
                                    class="px-4 py-2 rounded-full text-sm font-bold {{ $driver->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $driver->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="font-semibold">{{ $driver->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Teléfono</p>
                                    <p class="font-semibold">{{ $driver->user->phone ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">DNI</p>
                                    <p class="font-semibold">{{ $driver->dni_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Registrado</p>
                                    <p class="font-semibold">{{ $driver->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DNI Photo -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Documento de Identidad</h3>
                    @if($driver->dni_photo)
                        <img src="{{ Storage::url($driver->dni_photo) }}" alt="DNI"
                            class="w-full max-w-md rounded-lg shadow-lg">
                    @else
                        <p class="text-gray-600">No disponible</p>
                    @endif
                </div>

                <!-- Vehicle Info -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Información del Vehículo</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Tipo</p>
                            <p class="font-semibold text-lg">
                                @if($driver->vehicle_type === 'bicycle')
                                    🚲 Bicicleta
                                @elseif($driver->vehicle_type === 'motorcycle')
                                    🏍️ Moto
                                @else
                                    🚗 Auto
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Patente</p>
                            <p class="font-semibold text-lg">{{ $driver->vehicle_plate ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($driver->vehicle_photo)
                        <img src="{{ Storage::url($driver->vehicle_photo) }}" alt="Vehicle"
                            class="w-full max-w-md rounded-lg shadow-lg">
                    @endif
                </div>

                <!-- Recent Deliveries -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Entregas Recientes</h3>
                    @if($driver->deliveries->isEmpty())
                        <p class="text-gray-600 text-center py-8">No hay entregas registradas</p>
                    @else
                        <div class="space-y-3">
                            @foreach($driver->deliveries->take(10) as $delivery)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                    <div>
                                        <p class="font-bold">Pedido #{{ $delivery->order_id }}</p>
                                        <p class="text-sm text-gray-600">{{ $delivery->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-green-600">${{ number_format($delivery->delivery_fee, 0) }}
                                        </p>
                                        <span
                                            class="text-xs px-2 py-1 rounded-full {{ $delivery->status == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($delivery->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stats -->
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-6 text-white shadow-xl">
                    <h3 class="text-lg font-bold mb-4">Estadísticas</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm opacity-90">Total Entregas</p>
                            <p class="text-3xl font-bold">{{ $driver->total_deliveries }}</p>
                        </div>
                        <div>
                            <p class="text-sm opacity-90">Ganancias Totales</p>
                            <p class="text-3xl font-bold">${{ number_format($driver->total_earnings, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-sm opacity-90">Rating Promedio</p>
                            <div class="flex items-center space-x-2">
                                <span class="text-3xl font-bold">{{ number_format($driver->rating_avg, 1) }}</span>
                                <span class="text-yellow-300">⭐</span>
                            </div>
                            <p class="text-sm opacity-90">{{ $driver->rating_count }} reviews</p>
                        </div>
                    </div>
                </div>

                <!-- Coverage Area -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold mb-4">Área de Cobertura</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Radio</p>
                            <p class="font-bold text-lg">{{ $driver->coverage_radius_km }} km</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Ubicación Base</p>
                            <p class="text-sm">{{ $driver->location_lat }}, {{ $driver->location_lng }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bank Info -->
                @if($driver->bank_name)
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold mb-4">Información Bancaria</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Banco</p>
                                <p class="font-semibold">{{ $driver->bank_name }}</p>
                            </div>
                            @if($driver->account_number)
                                <div>
                                    <p class="text-sm text-gray-600">Cuenta</p>
                                    <p class="font-semibold">{{ $driver->account_number }}</p>
                                </div>
                            @endif
                            @if($driver->cbu_cvu)
                                <div>
                                    <p class="text-sm text-gray-600">CBU/CVU</p>
                                    <p class="font-semibold">{{ $driver->cbu_cvu }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                @if(!$driver->is_approved)
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold mb-4">Acciones</h3>
                        <div class="space-y-3">
                            <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-green-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-700 transition">
                                    ✓ Aprobar Repartidor
                                </button>
                            </form>
                            <button onclick="openRejectModal()"
                                class="w-full bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                                ✗ Rechazar Solicitud
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Status -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold mb-4">Estado Actual</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Disponible:</span>
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold {{ $driver->is_available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $driver->is_available ? '🟢 Online' : '⚫ Offline' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-4">Rechazar Solicitud</h3>
            <form action="{{ route('admin.drivers.reject', $driver->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo del Rechazo *</label>
                    <textarea name="rejection_reason" required rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 transition"
                        placeholder="Explica el motivo del rechazo..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit"
                        class="flex-1 bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                        Rechazar
                    </button>
                    <button type="button" onclick="closeRejectModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openRejectModal() {
                document.getElementById('rejectModal').classList.remove('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }
        </script>
    @endpush
@endsection