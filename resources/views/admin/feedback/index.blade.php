@extends('layouts.admin')

@section('title', 'Gestión de Feedback')

@section('content')
    <div class="min-h-screen py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold mb-2">
                        <span class="bg-gradient-to-r from-purple-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">
                            Sugerencias y Reportes
                        </span>
                    </h1>
                    <p class="text-gray-600">Gestión de feedback de los cocineros</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Cocinero</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($feedbacks as $feedback)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        @if($feedback->type === 'error')
                                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold tracking-tight uppercase">ERROR</span>
                                        @else
                                            <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-bold tracking-tight uppercase">SUGERENCIA</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-gray-200 to-gray-300 flex items-center justify-center text-gray-600 font-bold text-xs mr-3">
                                                {{ substr($feedback->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800">{{ $feedback->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $feedback->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $feedback->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($feedback->status === 'new')
                                            <span class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-[10px] font-black uppercase ring-2 ring-yellow-200">NUEVO</span>
                                        @elseif($feedback->status === 'read')
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[10px] font-bold uppercase">LEÍDO</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-[10px] font-bold uppercase">ARCHIVADO</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.feedback.show', $feedback->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-xl text-xs font-bold hover:bg-black transition-all">
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-5xl mb-4">📭</span>
                                            <p class="text-gray-400 font-medium">No hay feedback recibido aún</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($feedbacks->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
