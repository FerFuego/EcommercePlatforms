@extends('layouts.admin')

@section('title', 'Configuración - Admin')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Configuración de la Plataforma</h1>
                    <p class="mt-2 text-sm text-gray-600">Administra los parámetros globales del sistema.</p>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 md:p-8 space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- SEO Settings --}}
                    @if(isset($settings['seo']))
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-purple-100 text-purple-600 p-2 rounded-lg mr-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                SEO y Metadatos
                            </h2>
                            <div class="grid grid-cols-1 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                                @foreach($settings['seo'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                            {{ $setting->label }}
                                        </label>
                                        
                                        @if($setting->type === 'textarea')
                                            <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3"
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm">{{ $setting->value }}</textarea>
                                        @else
                                            <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                value="{{ $setting->value }}"
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-gray-100"></div>

                    {{-- Financial Settings --}}
                    @if(isset($settings['financial']))
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-green-100 text-green-600 p-2 rounded-lg mr-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Configuración Financiera
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                                @foreach($settings['financial'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                            {{ $setting->label }}
                                        </label>
                                        <div class="relative rounded-md shadow-sm">
                                            @if($setting->key === 'commission_rate')
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                  <span class="text-gray-500 sm:text-sm">%</span>
                                                </div>
                                            @endif
                                            <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                value="{{ $setting->value }}"
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm {{ $setting->key === 'commission_rate' ? 'pr-8' : '' }}">
                                        </div>
                                        @if($setting->key === 'commission_rate')
                                            <p class="mt-1 text-xs text-gray-500">Este porcentaje se aplicará a todos los nuevos pedidos.</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-gray-100"></div>

                    {{-- Payment Gateways Settings --}}
                    @if(isset($settings['pagos']))
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </span>
                                Pasarelas de Pago
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                                @foreach($settings['pagos'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                            {{ $setting->label }}
                                        </label>
                                        <div class="relative rounded-md shadow-sm">
                                            <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                value="{{ $setting->value }}"
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm font-mono text-sm">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-4 px-6 pb-4">
                                <button type="button" id="btn-test-mp" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Verificar Credenciales MP
                                </button>
                                <div id="mp-test-result" class="text-sm font-medium hidden p-2 rounded-lg"></div>
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-gray-100"></div>

                    {{-- Security Settings --}}
                    @if(isset($settings['security']))
                        <div class="pt-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-red-100 text-red-600 p-2 rounded-lg mr-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </span>
                                Seguridad y Validación
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                                @foreach($settings['security'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                            {{ $setting->label }}
                                        </label>
                                        @if($setting->key === 'recaptcha_enabled')
                                            <select name="{{ $setting->key }}" id="{{ $setting->key }}" 
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm">
                                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>✅ Habilitado (Recomendado)</option>
                                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>❌ Deshabilitado (Pruebas / Debug)</option>
                                            </select>
                                            <p class="mt-2 text-xs text-red-500 font-medium">⚠️ ¡Atención! Deshabilitar reCAPTCHA permite realizar pruebas automatizadas pero expone el sitio a spam.</p>
                                        @elseif($setting->key === 'chatbot_enabled')
                                            <select name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm">
                                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>✅ Habilitado</option>
                                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>❌ Deshabilitado</option>
                                            </select>
                                            <p class="mt-2 text-xs text-gray-500 font-medium">💬 Al deshabilitar el chatbot, el botón del asistente virtual desaparece del sitio para todos los usuarios.</p>
                                        @else
                                            <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                value="{{ $setting->value }}"
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- General / Other Settings --}}
                    @foreach($settings as $group => $groupSettings)
                        @if(!in_array($group, ['seo', 'financial', 'pagos', 'security']))
                            <div class="border-t border-gray-100 pt-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-4 capitalize">{{ $group }}</h2>
                                <div class="grid grid-cols-1 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                                    @foreach($groupSettings as $setting)
                                        <div>
                                            <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ $setting->label }}
                                            </label>
                                            <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                value="{{ $setting->value }}"
                                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition shadow-sm">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="border-t border-gray-100 pt-6">
                        <h2 class="text-xl font-bold text-red-600 mb-4 flex items-center">
                            <span class="bg-red-100 text-red-600 p-2 rounded-lg mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </span>
                            Herramientas de Lanzamiento (Zona de Peligro)
                        </h2>
                        <div class="bg-red-50 p-6 rounded-xl border border-red-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-red-900 text-lg">🚀 Limpiar Datos de Prueba para Lanzamiento</h3>
                                <p class="text-sm text-red-700 mt-1">
                                    Elimina automáticamente todos los cocineros, repartidores, clientes, platos, pedidos y reseñas de prueba.
                                    <br><strong class="font-semibold">Preservará intactos:</strong> las cuentas de administradores, configuraciones y planes de suscripción.
                                </p>
                            </div>
                            <button type="button" onclick="openPurgeModal()"
                                class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-md whitespace-nowrap">
                                🗑️ Limpiar Datos de Prueba
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                        <button type="submit" 
                            class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-xl font-bold hover:shadow-lg hover:scale-105 transform transition duration-300">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Limpieza --}}
    <div id="purgeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center text-red-600">
                <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="text-xl font-bold">¿Confirmar Limpieza Pre-Lanzamiento?</h3>
            </div>
            <p class="text-sm text-gray-600">
                Esta acción eliminará de forma <strong>irreversible</strong> todos los platos, cocineros, repartidores, pedidos y clientes de prueba.
            </p>
            <form action="{{ route('admin.settings.purge-test-data') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="confirm_text" class="block text-sm font-semibold text-gray-700 mb-1">
                        Escribe la palabra <span class="font-bold text-red-600">BORRAR</span> para confirmar:
                    </label>
                    <input type="text" name="confirm_text" id="confirm_text" required placeholder="BORRAR"
                        class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200">
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" onclick="closePurgeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-md">
                        Confirmar y Borrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openPurgeModal() {
            document.getElementById('purgeModal').classList.remove('hidden');
        }
        function closePurgeModal() {
            document.getElementById('purgeModal').classList.add('hidden');
        }

        document.getElementById('btn-test-mp').addEventListener('click', function() {
            const tokenInput = document.getElementById('mp_access_token');
            const resultDiv = document.getElementById('mp-test-result');
            const btn = this;
            
            if (!tokenInput || !tokenInput.value) {
                alert('Por favor, ingresa un Access Token antes de verificar.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Verificando...';
            
            resultDiv.classList.add('hidden');
            resultDiv.innerHTML = '';

            fetch('{{ route('admin.settings.test-mp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: tokenInput.value })
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.classList.remove('hidden');
                if (data.status === 'success') {
                    resultDiv.className = 'text-sm font-medium p-2 rounded-lg bg-green-100 text-green-800 border border-green-200';
                    resultDiv.innerHTML = data.message;
                } else {
                    resultDiv.className = 'text-sm font-medium p-2 rounded-lg bg-red-100 text-red-800 border border-red-200';
                    resultDiv.innerHTML = data.message;
                }
            })
            .catch(error => {
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'text-sm font-medium p-2 rounded-lg bg-red-100 text-red-800 border border-red-200';
                resultDiv.innerText = 'Error al conectar con el servidor.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Verificar Credenciales MP';
            });
        });
    </script>
    @endpush
@endsection
