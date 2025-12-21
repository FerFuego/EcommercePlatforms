@extends('layouts.app')

@section('title', 'Reseñas de ' . $cook->user->name)

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Reseñas de {{ $cook->user->name }}</h1>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < floor($cook->rating_avg))
                                    <svg class="w-6 h-6 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xl font-bold text-gray-700">{{ number_format($cook->rating_avg, 1) }}</span>
                        <span class="text-gray-500">({{ $cook->rating_count }} reseñas)</span>
                    </div>
                </div>
                <a href="{{ route('marketplace.cook.profile', $cook->id) }}"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition font-semibold">
                    ← Volver al Perfil
                </a>
            </div>

            <!-- Reviews List -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 space-y-8">
                    @forelse($reviews as $review)
                        <div class="border-b border-gray-100 pb-8 last:border-0 last:pb-0">
                            <div class="flex items-start space-x-4">
                                @if($review->customer->profile_photo_path)
                                    <img src="{{ asset('storage/' . $review->customer->profile_photo_path) }}"
                                        alt="{{ $review->customer->name }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-purple-100">
                                @else
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                        {{ substr($review->customer->name, 0, 1) }}
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-lg text-gray-800">{{ $review->customer->name }}</h4>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->format('d M, Y') }}</span>
                                    </div>

                                    <div class="flex items-center mb-3">
                                        @for($i = 0; $i < 5; $i++)
                                            @if($i < $review->rating)
                                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-200 fill-current" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>

                                    <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>

                                    @if($review->order)
                                        <div class="mt-3 bg-gray-50 p-3 rounded-lg inline-block">
                                            <p class="text-xs text-gray-500">
                                                <span class="font-semibold">Pidió:</span>
                                                {{ $review->order->items->map(fn($item) => $item->dish->name)->join(', ') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">⭐</div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Aún no hay reseñas</h3>
                            <p class="text-gray-500">Este cocinero aún no ha recibido calificaciones.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($reviews->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection