@extends('layouts.admin')

@section('title', 'Repartidores Pendientes - Admin')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">Repartidores Pendientes de Aprobación</h1>
            <a href="{{ route('admin.drivers.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">
                Ver Todos los Repartidores
            </a>
        </div>

        @if($drivers->isEmpty())
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay solicitudes pendientes</h3>
                <p class="text-gray-600">Todas las solicitudes han sido procesadas</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($drivers as $driver)
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                @if($driver->profile_photo)
                                    <img src="{{ Storage::url($driver->profile_photo) }}" alt="{{ $driver->user->name }}"
                                        class="w-20 h-20 object-cover rounded-full">
                                @else
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                        {{ substr($driver->user->name, 0, 1) }}
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold mb-2">{{ $driver->user->name }}</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
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
                                            <p class="text-sm text-gray-600">Vehículo</p>
                                            <p class="font-semibold">
                                                @if($driver->vehicle_type === 'bicycle')
                                                    🚲 Bicicleta
                                                @elseif($driver->vehicle_type === 'motorcycle')
                                                    🏍️ Moto
                                                @else
                                                    🚗 Auto
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span>📍 Cobertura: {{ $driver->coverage_radius_km }} km</span>
                                        <span>📅 Registrado: {{ $driver->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('admin.drivers.show', $driver->id) }}"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                                    Ver Detalle
                                </a>
                                <form action="{{ route('admin.drivers.approve', $driver->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                                        ✓ Aprobar
                                    </button>
                                </form>
                                <button onclick="openRejectModal({{ $driver->id }})"
                                    class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                                    ✗ Rechazar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $drivers->links() }}
            </div>
        @endif
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-4">Rechazar Solicitud</h3>
            <form id="rejectForm" method="POST">
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
            function openRejectModal(driverId) {
                const modal = document.getElementById('rejectModal');
                const form = document.getElementById('rejectForm');
                form.action = `/admin/drivers/${driverId}/reject`;
                modal.classList.remove('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }
        </script>
    @endpush
@endsection
