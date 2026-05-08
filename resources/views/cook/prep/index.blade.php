@extends('layouts.app')

@section('title', 'Hoja de Producción')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-pink-50 to-purple-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                        👨‍🍳 Hoja de Producción
                    </span>
                </h1>
                <p class="text-gray-600">Consolidado de platos a preparar para optimizar tu cocina.</p>
            </div>
            
            <div class="bg-white p-4 rounded-2xl shadow-lg flex items-center gap-4">
                <form action="{{ route('cook.prep.index') }}" method="GET" class="flex items-center gap-3">
                    <label class="text-sm font-bold text-gray-700">Fecha:</label>
                    <input type="date" name="date" value="{{ $date }}" 
                           onchange="this.form.submit()"
                           class="border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-purple-500 outline-none transition">
                </form>
                <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-xl font-bold hover:bg-black transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>

        @if($prepItems->isEmpty())
            <div class="bg-white rounded-3xl shadow-xl p-12 text-center">
                <div class="text-6xl mb-4">🧘</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Todo en calma por aquí</h2>
                <p class="text-gray-500">No hay platos pendientes de preparación para esta fecha.</p>
                <a href="{{ route('cook.dashboard') }}" class="inline-block mt-6 text-purple-600 font-bold hover:underline">Volver al Dashboard</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($prepItems as $item)
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all border-b-8 border-orange-500">
                        <div class="relative h-48">
                            @if($item->photo_url)
                                <img src="{{ asset('uploads/' . $item->photo_url) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-orange-200 to-pink-200 flex items-center justify-center text-5xl">
                                    🍲
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-4 py-2 rounded-2xl shadow-lg">
                                <span class="text-3xl font-black text-orange-600">x{{ (int)$item->total_quantity }}</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">{{ $item->name }}</h3>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                                <div class="text-center">
                                    <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Pedidos</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $item->order_count }}</p>
                                </div>
                                <div class="h-8 w-[2px] bg-gray-200"></div>
                                <div class="text-center">
                                    <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Unidades</p>
                                    <p class="text-xl font-bold text-orange-600">{{ (int)$item->total_quantity }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Print View (Hidden on screen) -->
            <div class="hidden print:block mt-8 bg-white p-8 rounded-xl border">
                <h2 class="text-2xl font-bold mb-4 border-b pb-2">Resumen de Producción - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Plato</th>
                            <th class="py-2 text-right">Cant. Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prepItems as $item)
                            <tr class="border-b">
                                <td class="py-4 font-semibold">{{ $item->name }}</td>
                                <td class="py-4 text-right text-xl font-bold">{{ (int)$item->total_quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        nav, footer, button, form { display: none !important; }
        .bg-gradient-to-br { background: white !important; }
        .shadow-xl, .shadow-2xl { shadow: none !important; }
        .container { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
    }
</style>
@endsection
