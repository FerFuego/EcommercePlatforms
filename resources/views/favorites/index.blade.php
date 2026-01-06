@extends('layouts.app')

@section('title', 'Mis Cocineros Favoritos')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-pink-50 to-purple-50 py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold mb-2">
                        <span
                            class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                            Mis Cocineros Favoritos
                        </span>
                    </h1>
                    <p class="text-gray-600">Acceso rápido a los cocineros que más te gustan</p>
                </div>
                <a href="{{ route('marketplace.catalog') }}"
                    class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Explorador
                </a>
            </div>

            @if($favorites->isEmpty())
                <div class="bg-white rounded-3xl shadow-xl p-12 text-center">
                    <div class="text-6xl mb-6">❤️</div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Aún no tienes favoritos</h2>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">
                        Explora nuestro catálogo y marca a tus cocineros preferidos con el corazón para verlos aquí.
                    </p>
                    <a href="{{ route('marketplace.catalog') }}"
                        class="inline-block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                        Explorar Cocineros
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($favorites as $cook)
                        @include('marketplace.partials.cook-items', ['cooks' => [$cook]])
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $favorites->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection