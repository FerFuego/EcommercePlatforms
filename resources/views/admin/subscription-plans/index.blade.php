@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Planes de Suscripción</h1>
            <div class="flex items-center space-x-3">
                <form action="{{ route('admin.subscription-plans.sync') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-blue-500 text-white px-3 py-2 rounded-xl font-bold hover:shadow-lg hover:bg-blue-600 transform transition duration-300 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Sincronizar con Mercado Pago
                    </button>
                </form>
                <a href="{{ route('admin.subscription-plans.create') }}"
                    class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-3 py-2 rounded-xl font-bold hover:shadow-lg hover:scale-105 transform transition duration-300">
                    Crear Nuevo Plan
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 mb-4 text-sm text-orange-700 bg-orange-100 rounded-lg dark:bg-orange-200 dark:text-orange-800"
                role="alert">
                {{ session('warning') }}
            </div>
        @endif

        <div
            class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nombre</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Precio</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Período</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Límites</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Estado</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider max-w-xs">
                            IDs Externos</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($plans as $plan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $plan->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                ${{ number_format($plan->price, 2) }} {{ $plan->currency }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ ucfirst($plan->billing_period) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                Ventas:
                                {{ $plan->monthly_sales_limit ? '$' . number_format($plan->monthly_sales_limit, 2) : '∞' }}<br>
                                Pedidos: {{ $plan->monthly_orders_limit ?? '∞' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $plan->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[10px] text-gray-500 dark:text-gray-400 max-w-xs break-all">
                                @if($plan->mp_plan_id)
                                    <span class="block">MP: {{ $plan->mp_plan_id }}</span>
                                @endif
                                @if($plan->stripe_price_id)
                                    <span class="block">Stripe: {{ $plan->stripe_price_id }}</span>
                                @endif
                                @if(!$plan->mp_plan_id && !$plan->stripe_price_id)
                                    <span class="text-gray-400 italic">No vinculado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.subscription-plans.edit', $plan) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Editar</a>
                                <form action="{{ route('admin.subscription-plans.toggle', $plan) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="text-{{ $plan->is_active ? 'red' : 'green' }}-600 hover:text-{{ $plan->is_active ? 'red' : 'green' }}-900 dark:text-{{ $plan->is_active ? 'red' : 'green' }}-400 dark:hover:text-{{ $plan->is_active ? 'red' : 'green' }}-300">
                                        {{ $plan->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('¿Estás seguro de que deseas eliminar este plan? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 ml-3">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection