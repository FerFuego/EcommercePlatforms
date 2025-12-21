@extends('layouts.admin')

@section('title', 'Gestión de Cocineros')

@section('content')
<div class="min-h-screen py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Gestión de Cocineros
                    </span>
                </h1>
                <p class="text-gray-600">Aprobar y administrar cocineros de la plataforma</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 px-6 py-3 rounded-xl font-semibold transition">
                ← Volver al Dashboard
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-lg p-2 mb-6 inline-flex">
            <a href="{{ route('admin.cooks.index') }}" class="px-6 py-3 rounded-xl font-semibold {{ request('filter') !== 'approved' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">
                Pendientes ({{ $pending_count }})
            </a>
            <a href="{{ route('admin.cooks.index', ['filter' => 'approved']) }}" class="px-6 py-3 rounded-xl font-semibold {{ request('filter') === 'approved' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">
                Aprobados ({{ $approved_count }})
            </a>
        </div>

        <!-- Cooks Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($cooks as $cook)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
                        <div class="flex items-center space-x-4">
                            @if($cook->user->profile_photo_path)
                                <img src="{{ asset('storage/' . $cook->user->profile_photo_path) }}" alt="{{ $cook->user->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-white">
                            @else
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl font-bold text-purple-600">
                                    {{ strtoupper(substr($cook->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-xl">{{ $cook->user->name }}</h3>
                                <p class="text-purple-100 text-sm">{{ $cook->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $cook->user->phone }}
                            </div>

                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Radio: {{ $cook->coverage_radius_km }} km
                            </div>

                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $cook->created_at->diffForHumans() }}
                            </div>

                            @if($cook->rating_count > 0)
                                <div class="flex items-center text-sm">
                                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ $cook->rating_avg }} ({{ $cook->rating_count }} reviews)
                                </div>
                            @endif
                        </div>

                        <!-- Bio -->
                        @if($cook->bio)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 line-clamp-3">{{ $cook->bio }}</p>
                            </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="mb-4">
                            @if($cook->is_approved)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Aprobado
                                </span>
                                @if($cook->active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 ml-2">
                                        Activo
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pendiente Aprobación
                                </span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            @if(!$cook->is_approved)
                                <form action="{{ route('admin.cooks.approve', $cook) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition">
                                        ✓ Aprobar
                                    </button>
                                </form>

                                <form action="{{ route('admin.cooks.reject', $cook) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-pink-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition" onclick="return confirm('¿Rechazar este cocinero?')">
                                        ✗ Rechazar
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('marketplace.cook.profile', $cook) }}" class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-xl font-semibold text-center hover:shadow-lg transition">
                                    Ver Perfil
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">👨‍🍳</div>
                    <p class="text-xl text-gray-500">No hay cocineros {{ request('filter') === 'approved' ? 'aprobados' : 'pendientes' }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($cooks->hasPages())
            <div class="mt-8">
                {{ $cooks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
