@extends('layouts.app')

@section('title', 'Mis Entregas')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6 bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
            Mis Entregas
        </h1>

        <!-- Tabs -->
        <div class="flex space-x-2 mb-6">
            <a href="{{ route('delivery-driver.orders.index', ['status' => 'active']) }}"
                class="px-6 py-3 rounded-xl font-semibold transition {{ $status === 'active' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Activas
                ({{ $driver->deliveries()->whereIn('status', ['assigned', 'picked_up', 'on_the_way', 'delayed'])->count() }})
            </a>
            <a href="{{ route('delivery-driver.orders.index', ['status' => 'completed']) }}"
                class="px-6 py-3 rounded-xl font-semibold transition {{ $status === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Completadas
            </a>
            <a href="{{ route('delivery-driver.orders.index', ['status' => 'rejected']) }}"
                class="px-6 py-3 rounded-xl font-semibold transition {{ $status === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Rechazadas
            </a>
        </div>

        @if($deliveries->isEmpty())
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay entregas</h3>
                <p class="text-gray-600">
                    @if($status === 'active')
                        No tienes entregas activas en este momento
                    @elseif($status === 'completed')
                        Aún no has completado ninguna entrega
                    @else
                        No has rechazado ninguna entrega
                    @endif
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($deliveries as $delivery)
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold">Pedido #{{ $delivery->order_id }}</h3>
                                <p class="text-gray-600">{{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">${{ number_format($delivery->delivery_fee, 0) }}</p>
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-semibold
                                                            {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' :
                        ($delivery->status === 'rejected' ? 'bg-red-100 text-red-800' :
                            ($delivery->status === 'delayed' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ match ($delivery->status) {
                        'assigned' => '📋 Asignado',
                        'picked_up' => '📦 Recogido',
                        'on_the_way' => '🚴 En Camino',
                        'delayed' => '⏰ Demorado',
                        'delivered' => '✅ Entregado',
                        'rejected' => '❌ Rechazado',
                        default => $delivery->status
                    } }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <p class="text-sm text-gray-600 mb-1">📍 Retiro</p>
                                <p class="font-bold">{{ $delivery->order->cook->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $delivery->order->cook->user->address }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl">
                                <p class="text-sm text-gray-600 mb-1">🏠 Destino</p>
                                @if($delivery->order->status === 'preparing')
                                    <p class="font-bold text-yellow-800">⏳ En Preparación</p>
                                    <p class="text-sm text-yellow-700 italic">Dirección oculta hasta que esté listo</p>
                                @else
                                    <p class="font-bold">{{ $delivery->order->customer->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $delivery->order->delivery_address }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('delivery-driver.orders.show', $delivery->id) }}"
                                class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition text-center">
                                Ver Detalle
                            </a>

                            @if(in_array($delivery->status, ['assigned', 'picked_up', 'on_the_way', 'delayed']))
                                @if($delivery->status === 'assigned')
                                    @if($delivery->order->status === 'preparing')
                                        <button disabled
                                            class="flex-1 bg-yellow-100 text-yellow-600 px-6 py-3 rounded-xl font-bold shadow-none cursor-not-allowed border border-yellow-200">
                                            ⏳ Esperando confirmación
                                        </button>
                                    @else
                                        <form action="{{ route('delivery-driver.orders.update-status', $delivery->id) }}" method="POST"
                                            class="flex-1">
                                            @csrf
                                            <input type="hidden" name="status" value="picked_up">
                                            <button type="submit"
                                                class="w-full bg-gradient-to-r from-blue-500 to-cyan-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                                📦 Marcar Recogido
                                            </button>
                                        </form>
                                    @endif
                                @elseif($delivery->status === 'picked_up')
                                    <form action="{{ route('delivery-driver.orders.update-status', $delivery->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        <input type="hidden" name="status" value="on_the_way">
                                        <button type="submit"
                                            class="w-full bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                            🚴 En Camino
                                        </button>
                                    </form>
                                @elseif(in_array($delivery->status, ['on_the_way', 'delayed']))
                                    <form action="{{ route('delivery-driver.orders.update-status', $delivery->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit"
                                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                            ✅ Marcar Entregado
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection