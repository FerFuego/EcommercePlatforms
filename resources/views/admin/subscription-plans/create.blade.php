@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Crear Plan de Suscripción</h2>

            <form action="{{ route('admin.subscription-plans.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del
                        Plan</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" step="0.01" value="{{ old('price') }}" required
                                class="pl-7 mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="billing_period"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Período de
                            Facturación</label>
                        <select id="billing_period" name="billing_period"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm rounded-md">
                            <option value="monthly" {{ old('billing_period') == 'monthly' ? 'selected' : '' }}>Mensual
                            </option>
                            <option value="yearly" {{ old('billing_period') == 'yearly' ? 'selected' : '' }}>Anual</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="monthly_sales_limit"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Límite de Ventas Mensuales
                            (Opcional)</label>
                        <input type="number" name="monthly_sales_limit" id="monthly_sales_limit" step="0.01"
                            value="{{ old('monthly_sales_limit') }}"
                            class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <span class="text-xs text-gray-500">Dejar en blanco para ilimitado</span>
                        @error('monthly_sales_limit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="monthly_orders_limit"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Límite de Pedidos Mensuales
                            (Opcional)</label>
                        <input type="number" name="monthly_orders_limit" id="monthly_orders_limit"
                            value="{{ old('monthly_orders_limit') }}"
                            class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <span class="text-xs text-gray-500">Dejar en blanco para ilimitado</span>
                        @error('monthly_orders_limit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="commission_percentage"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comisión de la Plataforma
                        (%)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" name="commission_percentage" id="commission_percentage" step="0.01"
                            value="{{ old('commission_percentage', 12) }}" required
                            class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    @error('commission_percentage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="stripe_price_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stripe
                        Price ID (Opcional)</label>
                    <input type="text" name="stripe_price_id" id="stripe_price_id" value="{{ old('stripe_price_id') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="price_1HhG2f2eZvKYlo2... ">
                    @error('stripe_price_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="mp_plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">MercadoPago
                        Plan ID (Opcional)</label>
                    <input type="text" name="mp_plan_id" id="mp_plan_id" value="{{ old('mp_plan_id') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="2c9380847269f38a0172... ">
                    @error('mp_plan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Características
                        (Features)</span>
                    <div class="space-y-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="feature_badge" name="features[premium_badge]" type="checkbox"
                                    class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ old('features.premium_badge') ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="feature_badge" class="font-medium text-gray-700 dark:text-gray-300">Badge
                                    "Cocinero Premium"</label>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="feature_offers" name="features[can_create_offers]" type="checkbox"
                                    class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ old('features.can_create_offers') ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="feature_offers" class="font-medium text-gray-700 dark:text-gray-300">Crear
                                    Ofertas Especiales</label>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="feature_stats" name="features[advanced_stats]" type="checkbox"
                                    class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ old('features.advanced_stats') ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="feature_stats" class="font-medium text-gray-700 dark:text-gray-300">Estadísticas
                                    Avanzadas</label>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="feature_priority" name="features[priority_listing]" type="checkbox"
                                    class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ old('features.priority_listing') ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="feature_priority" class="font-medium text-gray-700 dark:text-gray-300">Prioridad
                                    en Búsquedas</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6 flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_active" name="is_active" type="checkbox" value="1"
                            class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" checked>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">Plan Activo (visible
                            para suscripción)</label>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.subscription-plans.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-8 rounded-xl font-bold dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancelar</a>
                    <button type="submit"
                        class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-3 py-2 rounded-xl font-bold hover:shadow-lg hover:scale-105 transform transition duration-300">Guardar
                        Plan</button>
                </div>
            </form>
        </div>
    </div>
@endsection