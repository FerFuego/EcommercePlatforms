@extends('layouts.admin')

@section('title', 'Detalle de Feedback')

@section('content')
    <div class="min-h-screen py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Breadcrumbs -->
            <div class="mb-6">
                <a href="{{ route('admin.feedback.index') }}"
                    class="text-gray-500 hover:text-purple-600 transition-colors flex items-center text-sm font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al listado
                </a>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <!-- Header with Status -->
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        @if($feedback->type === 'error')
                            <span
                                class="px-4 py-1.5 bg-red-100 text-red-600 rounded-full text-xs font-black uppercase tracking-widest ring-4 ring-red-50">ERROR</span>
                        @else
                            <span
                                class="px-4 py-1.5 bg-blue-100 text-blue-600 rounded-full text-xs font-black uppercase tracking-widest ring-4 ring-blue-50">SUGERENCIA</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Estado:</span>
                        @if($feedback->status === 'new')
                            <span
                                class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-[10px] font-black uppercase">NUEVO</span>
                        @elseif($feedback->status === 'read')
                            <span
                                class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-[10px] font-bold uppercase">LEÍDO</span>
                        @else
                            <span
                                class="px-3 py-1 bg-gray-800 text-white rounded-full text-[10px] font-bold uppercase">ARCHIVADO</span>
                        @endif
                    </div>
                </div>

                <div class="p-8">
                    <!-- Cook Info -->
                    <div
                        class="flex items-center mb-8 p-6 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-purple-500 to-pink-500 flex items-center justify-center text-white font-black text-2xl shadow-lg mr-6">
                            {{ substr($feedback->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $feedback->user->name }}</h3>
                            <p class="text-gray-500 font-medium">{{ $feedback->user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1 font-bold uppercase tracking-wider">Recibido:
                                {{ $feedback->created_at->format('d/m/Y H:i') }}
                                ({{ $feedback->created_at->diffForHumans() }})</p>
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="mb-8">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Mensaje completo:</h4>
                        <div
                            class="bg-gray-50 rounded-3xl p-8 border border-gray-100 text-gray-800 leading-relaxed text-lg whitespace-pre-wrap italic">
                            "{{ $feedback->message }}"
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 pt-8 border-t border-gray-100">
                        @if($feedback->status !== 'archived')
                            <form action="{{ route('admin.feedback.archive', $feedback->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-8 py-4 bg-gray-100 text-gray-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Archivar Feedback
                                </button>
                            </form>
                        @endif

                        @if($feedback->status === 'new')
                            <form action="{{ route('admin.feedback.read', $feedback->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all flex items-center shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Marcar como leído
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection