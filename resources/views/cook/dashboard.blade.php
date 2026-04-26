@extends('layouts.app')

@section('title', 'Dashboard - Cocinero')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2">
                <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                    ¡Hola, {{ auth()->user()->name }}!
                </span>
            </h1>
            <p class="text-gray-600 text-lg">Aquí está el resumen de tu cocina</p>
        </div>

        @if(auth()->user()->is_suspended)
            <div class="bg-red-500 text-white px-6 py-4 rounded-2xl shadow-lg mb-8">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-bold text-lg">Cuenta Suspendida</h3>
                        <p>Tu cuenta ha sido suspendida debido a irregularidades. Por favor, ponte en contacto con la plataforma
                            para más información.</p>
                    </div>
                </div>
            </div>
        @elseif(!$cook->is_approved)
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-4 rounded-2xl shadow-lg mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold">Tu perfil está siendo revisado por nuestro equipo. Te notificaremos cuando sea
                        aprobado.</span>
                </div>
            </div>
        @endif

        @if(auth()->user()->is_suspended == false && $cook->is_approved && (!$cook->currentSubscription || $cook->currentSubscription->status !== 'active'))
            <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-6 py-5 rounded-2xl shadow-lg mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-bold text-lg block">Suscripción Inactiva</span>
                            <span class="text-white text-opacity-90">Atención: No puedes aceptar pedidos nuevos ni gestionar los pendientes hasta regularizar tu suscripción.</span>
                        </div>
                    </div>
                    <a href="{{ route('cook.subscription.index') }}" class="bg-white text-purple-700 px-6 py-3 rounded-xl font-bold shadow-md hover:shadow-xl hover:scale-105 transition-all text-center flex-shrink-0 whitespace-nowrap">
                        Activar Suscripción
                    </a>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none filter grayscale' : '' }}">

            <div class="bg-white rounded-2xl shadow-xl p-6 border-b-4 border-orange-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-500 font-semibold uppercase text-xs tracking-wider">Pendientes</span>
                    <span class="p-2 bg-orange-100 rounded-lg text-orange-600">⏰</span>
                </div>
                <div class="text-3xl font-bold text-gray-800">{{ $pendingOrders }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border-b-4 border-purple-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-500 font-semibold uppercase text-xs tracking-wider">Programados</span>
                    <span class="p-2 bg-purple-100 rounded-lg text-purple-600">📅</span>
                </div>
                <div class="text-3xl font-bold text-gray-800">{{ $scheduledOrders }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border-b-4 border-blue-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-500 font-semibold uppercase text-xs tracking-wider">Hoy</span>
                    <span class="p-2 bg-blue-100 rounded-lg text-blue-600">📈</span>
                </div>
                <div class="text-3xl font-bold text-gray-800">{{ $todayOrders }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border-b-4 border-green-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-500 font-semibold uppercase text-xs tracking-wider">Ventas Totales</span>
                    <span class="p-2 bg-green-100 rounded-lg text-green-600">💰</span>
                </div>
                <div class="text-3xl font-bold text-gray-800">${{ number_format($totalRevenue, 0) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden">
                    @if(auth()->user()->is_suspended)
                        <div
                            class="absolute inset-0 bg-gray-100 bg-opacity-50 z-10 flex items-center justify-center backdrop-blur-sm">
                            <span
                                class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">Suspendido</span>
                        </div>
                    @endif
                    <h3 class="text-xl font-bold mb-4">
                        Acciones Rápidas
                    </h3>
                    <div class="space-y-3 {{ auth()->user()->is_suspended ? 'opacity-50 pointer-events-none' : '' }}">
                        <a href="{{ route('cook.dashboard') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Dashboard
                        </a>
                        <a href="{{ route('cook.orders.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Ver Pedidos
                        </a>
                        <a href="{{ route('cook.dishes.index') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Mis Platos
                        </a>
                        <a href="{{ route('cook.dishes.create') }}"
                            class="block bg-gradient-to-r from-orange-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Nuevo Plato
                        </a>
                        <a href="{{ route('cook.profile.edit') }}"
                            class="block bg-gradient-to-r from-gray-500 to-gray-700 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center">
                            Configuración
                        </a>
                        <a href="{{ route('cook.subscription.index') }}"
                            class="block bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-center mt-3">
                            Mi Suscripción
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
                    <h3 class="text-xl font-bold mb-4">Mi Perfil</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Suscripción:</span>
                            @php
                                $plan = $cook->plan();
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $plan && $plan->price > 0 ? 'bg-purple-100 text-purple-800 border-purple-200 border' : 'bg-gray-100 text-gray-800 border-gray-200 border' }}">
                                {{ $plan ? $plan->name : 'Básico (FREE)' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Estado:</span>
                            @if(auth()->user()->is_suspended)
                                <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                    ❌ Suspendido
                                </span>
                            @else
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-semibold {{ $cook->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $cook->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Activo:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" {{ $cook->active ? 'checked' : '' }} class="sr-only peer"
                                    onchange="toggleActive(this)">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-purple-500 peer-checked:to-pink-600">
                                </div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Rating:</span>
                            <span class="font-bold text-yellow-500">⭐ {{ number_format($cook->rating_avg, 1) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Reviews:</span>
                            <span class="font-semibold">{{ $cook->rating_count }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-6">Actividad Reciente</h3>

                    @php
                        $recentOrders = $cook->orders()->with('customer')->latest()->limit(5)->get();
                    @endphp

                    @forelse($recentOrders as $order)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-pink-50 rounded-xl mb-3 hover:shadow-md transition-shadow">
                                        <a href="{{ route('orders.show', $order->id) }}" class="flex items-center space-x-4 group">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold group-hover:scale-110 transition-transform">
                                                {{ substr($order->customer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                                                    {{ $order->customer->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $order->created_at->diffForHumans() }}</p>
                                            </div>
                                        </a>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-800">${{ number_format($order->total_amount, 0) }}</p>
                                            <span class="text-xs px-2 py-1 rounded-full  {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' :
                            ($order->status == 'rejected_by_cook' ? 'bg-red-100 text-red-800' :
                                ($order->status == 'awaiting_cook_acceptance' ? 'bg-yellow-100 text-yellow-800' :
                                    ($order->status == 'scheduled' ? 'bg-purple-100 text-purple-800' :
                                        'bg-blue-100 text-blue-800'))) }}">
                                                {{ match ($order->status) {
                            'pending_payment' => '⏳ Pendiente de Pago',
                            'paid' => '✓ Pagado',
                            'awaiting_cook_acceptance' => '⏰ Esperando Confirmación',
                            'rejected_by_cook' => '❌ Rechazado',
                            'preparing' => '👨‍🍳 En Preparación',
                            'ready_for_pickup' => '✅ Listo para Retiro',
                            'assigned_to_delivery' => '🛵 En Camino',
                            'on_the_way' => '🚗 En Camino',
                            'delivered' => '✓ Entregado',
                            'cancelled' => '❌ Cancelado',
                            'scheduled' => '📅 Programado',
                            default => $order->status
                        } }}
                                            </span>
                                        </div>
                                    </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="text-5xl mb-3">🍽️</div>
                            <p class="text-gray-500">No hay pedidos recientes</p>
                        </div>
                    @endforelse

                    @if($recentOrders->count() > 0)
                        <a href="{{ route('cook.orders.index') }}"
                            class="block text-center mt-6 text-purple-600 font-semibold hover:text-pink-600 transition">
                            Ver Todos los Pedidos →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Feedback Button -->
    <button onclick="openFeedbackModal()" class="fixed bottom-8 right-8 z-50 bg-gradient-to-r from-purple-600 to-pink-600 text-white w-14 h-14 rounded-full shadow-2xl hover:scale-110 transition-transform flex items-center justify-center group">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        <span class="absolute right-16 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Sugerencias / Errores</span>
    </button>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="hidden fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50 backdrop-blur-sm" aria-hidden="true" onclick="closeFeedbackModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <span class="mr-2">💬</span> Ayúdanos a mejorar
                        </h3>
                        <button onclick="closeFeedbackModal()" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form id="feedbackForm" onsubmit="submitFeedback(event)" class="p-6">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Tipo de mensaje *</label>
                        <select name="type" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-purple-500 transition-all outline-none">
                            <option value="suggestion">💡 Sugerencia</option>
                            <option value="error">🐛 Reportar un error</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Detalle *</label>
                        <textarea name="message" required maxlength="2000" rows="5" 
                            placeholder="Describe el detalle lo más claro posible..."
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-purple-500 transition-all outline-none resize-none"></textarea>
                        <div class="text-right text-xs text-gray-400 mt-1">Máx. 2000 caracteres</div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeFeedbackModal()" 
                            class="px-6 py-3 rounded-xl font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" id="feedbackSubmitBtn"
                            class="px-8 py-3 rounded-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center">
                            <span>Enviar Feedback</span>
                            <div id="feedbackSpinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                if (window.Echo) {
                    window.Echo.private('cook.{{ auth()->user()->id }}')
                        .listen('OrderStatusUpdated', (e) => {
                            console.log('Order status updated:', e);
                            window.location.reload();
                        });
                }
            });

            function toggleActive(checkbox) {
                fetch('{{ route("cook.profile.toggle-active") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ active: checkbox.checked })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Status updated to:', data.active);
                        } else {
                            alert('Error al actualizar el estado');
                            checkbox.checked = !checkbox.checked; // Revert
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al procesar la solicitud');
                        checkbox.checked = !checkbox.checked; // Revert
                    });
            }

            function openFeedbackModal() {
                document.getElementById('feedbackModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeFeedbackModal() {
                document.getElementById('feedbackModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
                document.getElementById('feedbackForm').reset();
            }

            function submitFeedback(event) {
                event.preventDefault();
                const form = event.target;
                const submitBtn = document.getElementById('feedbackSubmitBtn');
                const spinner = document.getElementById('feedbackSpinner');
                
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75');
                spinner.classList.remove('hidden');

                const formData = new FormData(form);
                const data = {
                    type: formData.get('type'),
                    message: formData.get('message')
                };

                fetch('{{ route("api.feedback.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    closeFeedbackModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al enviar el feedback. Por favor intenta de nuevo.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75');
                    spinner.classList.add('hidden');
                });
            }
        </script>
    @endpush

@endsection