@extends('layouts.admin')

@section('title', 'Repartidores - Admin')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Repartidores</h1>
                <div class="flex space-x-4 text-sm">
                    <span class="text-gray-600">Aprobados: <strong>{{ $approved_count }}</strong></span>
                    <span class="text-gray-600">Pendientes: <strong>{{ $pending_count }}</strong></span>
                </div>
            </div>
            @if($pending_count > 0)
                <a href="{{ route('admin.drivers.pending') }}"
                    class="px-6 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition">
                    Ver Pendientes ({{ $pending_count }})
                </a>
            @endif
        </div>

        @if($drivers->isEmpty())
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <div class="text-6xl mb-4">🚴</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay repartidores aprobados</h3>
                <p class="text-gray-600">Los repartidores aparecerán aquí una vez aprobados</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Repartidor</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Contacto</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Vehículo</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Entregas</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Ganancias</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Rating</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Estado</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($drivers as $driver)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($driver->profile_photo)
                                            <img src="{{ asset('uploads/' . $driver->profile_photo) }}" alt="{{ $driver->user->name }}"
                                                class="w-12 h-12 object-cover rounded-full">
                                        @else
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ substr($driver->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold">{{ $driver->user->name }}</p>
                                            <p class="text-sm text-gray-600">DNI: {{ $driver->dni_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm">{{ $driver->user->email }}</p>
                                    <p class="text-sm text-gray-600">{{ $driver->user->phone ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold">
                                        @if($driver->vehicle_type === 'bicycle')
                                            🚲 Bicicleta
                                        @elseif($driver->vehicle_type === 'motorcycle')
                                            🏍️ Moto
                                        @else
                                            🚗 Auto
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $driver->vehicle_plate ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <p class="text-lg font-bold">{{ $driver->total_deliveries }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <p class="text-lg font-bold text-green-600">
                                        ${{ number_format($driver->total_earnings, 0) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <span class="text-yellow-500">⭐</span>
                                        <span class="font-bold">{{ number_format($driver->rating_avg, 1) }}</span>
                                        <span class="text-sm text-gray-600">({{ $driver->rating_count }})</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $driver->is_available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $driver->is_available ? '🟢 Online' : '⚫ Offline' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.drivers.show', $driver->id) }}"
                                        class="text-blue-600 hover:text-blue-800 font-semibold">
                                        Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $drivers->links() }}
            </div>
        @endif
    </div>
@endsection