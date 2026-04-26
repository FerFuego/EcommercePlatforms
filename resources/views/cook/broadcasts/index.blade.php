@extends('layouts.app')

@section('title', 'Marketing y Ofertas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Ofertas por WhatsApp</h1>
            <p class="text-gray-600 mt-2">Envía promociones directas al WhatsApp de los clientes que ya te han comprado.</p>
        </div>
        @if($isPremium)
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-yellow-900 px-4 py-2 rounded-xl font-bold shadow-md flex items-center">
                <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
                Premium Feature
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if(!$isPremium)
        <!-- PAYWALL -->
        <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-3xl shadow-xl overflow-hidden mb-8 relative">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full filter blur-3xl"></div>
            </div>
            <div class="relative z-10 p-8 md:p-12 text-white flex flex-col md:flex-row items-center">
                <div class="flex-1 mb-6 md:mb-0">
                    <div class="flex items-center mb-4">
                        <span class="text-5xl mr-4">📱</span>
                        <h2 class="text-3xl font-bold">Llega directo al bolsillo de tus clientes</h2>
                    </div>
                    <p class="text-green-50 text-lg mb-6 max-w-2xl">Aumenta tus ventas enviando ofertas por WhatsApp. Cocinarte arma automáticamente la lista de tus clientes frecuentes y te permite enviarles un mensaje promocional con un solo clic.</p>
                    <ul class="space-y-2 font-medium">
                        <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Hasta 3 campañas masivas incluidas</li>
                        <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Filtro inteligente de audiencia</li>
                        <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Integración con WhatsApp Web/App</li>
                    </ul>
                </div>
                <div class="w-full md:w-auto">
                    <a href="{{ route('cook.subscription.index') }}" class="block w-full text-center bg-white text-green-600 font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all transform hover:-translate-y-1">
                        Desbloquear Ofertas Premium
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- PREMIUM CONTENT -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- CREATE FORM -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Nueva Oferta</h3>
                        <span class="text-sm font-semibold {{ $canCreate ? 'text-green-600' : 'text-red-500' }}">
                            {{ $broadcastsToday }} / {{ $broadcastLimit }} Hoy
                        </span>
                    </div>

                    @if($canCreate)
                        <form action="{{ route('cook.broadcasts.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mensaje de WhatsApp</label>
                                <textarea name="message" rows="5" required
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 transition-all"
                                    placeholder="¡Hola! Tengo una oferta especial hoy..."></textarea>
                                <p class="text-xs text-gray-500 mt-2">El mensaje se enviará a todos los clientes que te hayan comprado previamente.</p>
                            </div>
                            <button type="submit" class="w-full bg-green-500 text-white font-bold py-3 rounded-xl shadow-md hover:bg-green-600 transition-all">
                                Crear Campaña
                            </button>
                        </form>
                    @else
                        <div class="bg-red-50 text-red-700 p-4 rounded-xl text-center">
                            Has alcanzado el límite de 3 campañas. ¡Felicidades por tus ventas!
                        </div>
                    @endif
                </div>
            </div>

            <!-- BROADCAST LIST -->
            <div class="lg:col-span-2">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Tus Campañas</h3>
                
                @if($broadcasts->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="text-6xl mb-4">📣</div>
                        <p class="text-xl text-gray-500 font-semibold">Aún no has creado campañas.</p>
                        <p class="text-gray-400 mt-2">Usa el formulario para crear tu primera oferta especial.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($broadcasts as $broadcast)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row items-center justify-between hover:shadow-md transition-all">
                                <div class="flex-1 mb-4 md:mb-0 pr-4">
                                    <div class="flex items-center mb-2">
                                        @if($broadcast->status === 'completed')
                                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full mr-2">Completada</span>
                                        @elseif($broadcast->status === 'running')
                                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full mr-2">En progreso</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-700 text-xs font-bold px-2 py-1 rounded-full mr-2">Borrador</span>
                                        @endif
                                        <span class="text-sm text-gray-500">{{ $broadcast->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="text-gray-800 font-medium line-clamp-2">{{ $broadcast->message }}</p>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500">Enviados</p>
                                        <p class="font-bold text-xl">{{ $broadcast->sent_count }}</p>
                                    </div>
                                    <a href="{{ route('cook.broadcasts.show', $broadcast->id) }}" class="bg-purple-100 text-purple-700 hover:bg-purple-200 font-bold px-4 py-2 rounded-lg transition-all">
                                        {{ $broadcast->status === 'completed' ? 'Ver Detalles' : 'Continuar Envío' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
