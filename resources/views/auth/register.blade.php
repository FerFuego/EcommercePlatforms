@extends('layouts.app')

@section('title', 'Crear Cuenta')

@section('content')
    @php
        $rawRole = old('role', request()->query('role', request()->query('tipo')));
        $roleMap = [
            'cook' => 'cook',
            'cocinero' => 'cook',
            'chef' => 'cook',
            'delivery_driver' => 'delivery_driver',
            'repartidor' => 'delivery_driver',
            'driver' => 'delivery_driver',
            'customer' => 'customer',
            'cliente' => 'customer',
            'comer' => 'customer',
        ];
        $initialRole = $roleMap[strtolower((string) $rawRole)] ?? (in_array($rawRole, ['customer', 'cook', 'delivery_driver']) ? $rawRole : '');
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-pink-50 to-purple-50 flex items-center justify-center py-12 px-4"
        x-data="{
                        step: {{ $errors->has('name') || $errors->has('email') || $errors->has('phone') ? 2 : ($errors->has('password') ? 3 : 1) }},
                        role: '{{ $initialRole }}',
                        roleError: false,
                        nextStep() { 
                            if (this.step === 1 && !this.role) {
                                this.roleError = true;
                                return;
                            }
                            this.roleError = false;
                            if(this.step < 3) this.step++;
                        },
                        prevStep() { if(this.step > 1) this.step--; }
                    }">
        <div class="max-w-md w-full">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4">
                    <img src="{{ asset('assets/front/icon.png') }}" alt="Logo" class="h-20 w-auto">
                </div>
                <h2
                    class="text-4xl font-bold bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                    Únete a Cocinarte
                </h2>
                <div class="flex items-center justify-center mt-4 space-x-4">
                    <div class="flex items-center">
                        <div :class="step >= 1 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500'"
                            class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition-colors duration-300">
                            1</div>
                    </div>
                    <div class="w-8 h-1" :class="step >= 2 ? 'bg-orange-500' : 'bg-gray-200'"></div>
                    <div class="flex items-center">
                        <div :class="step >= 2 ? 'bg-pink-500 text-white' : 'bg-gray-200 text-gray-500'"
                            class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition-colors duration-300">
                            2</div>
                    </div>
                    <div class="w-8 h-1" :class="step >= 3 ? 'bg-pink-500' : 'bg-gray-200'"></div>
                    <div class="flex items-center">
                        <div :class="step >= 3 ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-500'"
                            class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition-colors duration-300">
                            3</div>
                    </div>
                </div>
            </div>

            <!-- Register Form Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 relative overflow-hidden">
                <form id="registerForm" method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                    {{-- Honeypot Anti-Bot Field (Invisible to real humans) --}}
                    <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                        <input type="text" name="system_website_check" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <!-- Step 1: Role Selection -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-8"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <h3 class="text-xl font-bold text-gray-800 mb-1 text-center">¿Cómo quieres usar Cocinarte?</h3>
                        <p class="text-sm text-gray-500 mb-6 text-center">Selecciona una opción para configurar tu cuenta</p>

                        <!-- Banner si ya viene con un rol preseleccionado -->
                        <div x-show="role === 'cook'" x-cloak class="mb-5 p-3.5 bg-purple-50 border border-purple-200 rounded-xl text-xs text-purple-800 flex items-center gap-2.5">
                            <span class="text-xl">👨‍🍳</span>
                            <div>
                                <p class="font-bold">Registro de Cocinero</p>
                                <p class="text-purple-600">Crearás tu cuenta para ofrecer y vender tus platos caseros.</p>
                            </div>
                        </div>

                        <div x-show="role === 'delivery_driver'" x-cloak class="mb-5 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800 flex items-center gap-2.5">
                            <span class="text-xl">🚴</span>
                            <div>
                                <p class="font-bold">Registro de Repartidor</p>
                                <p class="text-blue-600">Crearás tu cuenta para realizar entregas en tu zona.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 mb-6">
                            <!-- Opción 1: Cliente -->
                            <label class="relative cursor-pointer group block">
                                <input type="radio" name="role" value="customer" x-model="role" @change="roleError = false" class="peer sr-only">
                                <div
                                    class="border-2 rounded-2xl p-5 transition-all flex items-center space-x-4"
                                    :class="role === 'customer' ? 'border-orange-500 bg-orange-50/70 ring-2 ring-orange-200 shadow-sm' : 'border-gray-100 hover:border-orange-200 hover:bg-gray-50/50'">
                                    <div class="text-4xl">🍽️</div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-800">Quiero Comer</p>
                                        <p class="text-sm text-gray-500">Descubre sabores caseros cerca de ti</p>
                                    </div>
                                    <div class="ml-auto text-orange-500 transition-opacity" :class="role === 'customer' ? 'opacity-100' : 'opacity-0'">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Opción 2: Cocinero -->
                            <label class="relative cursor-pointer group block">
                                <input type="radio" name="role" value="cook" x-model="role" @change="roleError = false" class="peer sr-only">
                                <div
                                    class="border-2 rounded-2xl p-5 transition-all flex items-center space-x-4"
                                    :class="role === 'cook' ? 'border-purple-500 bg-purple-50/80 ring-2 ring-purple-200 shadow-sm' : 'border-gray-100 hover:border-purple-200 hover:bg-gray-50/50'">
                                    <div class="text-4xl">👨‍🍳</div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-gray-800">Quiero Cocinar</p>
                                            <span class="text-[11px] bg-purple-100 text-purple-700 font-bold px-2 py-0.5 rounded-full">Cocinero</span>
                                        </div>
                                        <p class="text-sm text-gray-500">Vende tus platos y genera ingresos</p>
                                    </div>
                                    <div class="ml-auto text-purple-500 transition-opacity" :class="role === 'cook' ? 'opacity-100' : 'opacity-0'">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Opción 3: Repartidor -->
                            <label class="relative cursor-pointer group block">
                                <input type="radio" name="role" value="delivery_driver" x-model="role" @change="roleError = false" class="peer sr-only">
                                <div
                                    class="border-2 rounded-2xl p-5 transition-all flex items-center space-x-4"
                                    :class="role === 'delivery_driver' ? 'border-blue-500 bg-blue-50/80 ring-2 ring-blue-200 shadow-sm' : 'border-gray-100 hover:border-blue-200 hover:bg-gray-50/50'">
                                    <div class="text-4xl">🚴</div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-gray-800">Quiero Repartir</p>
                                            <span class="text-[11px] bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">Repartidor</span>
                                        </div>
                                        <p class="text-sm text-gray-500">Entrega pedidos en tu vehículo</p>
                                    </div>
                                    <div class="ml-auto text-blue-500 transition-opacity" :class="role === 'delivery_driver' ? 'opacity-100' : 'opacity-0'">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Error si no seleccionó ningún rol -->
                        <div x-show="roleError" x-cloak class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Por favor selecciona cómo quieres usar Cocinarte para continuar.</span>
                        </div>

                        <button type="button" @click="nextStep"
                            class="w-full bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                            Continuar
                        </button>
                    </div>

                    <!-- Step 2: Personal Info -->
                    <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-8"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Tus datos personales</h3>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1"
                                :class="{
                                    'bg-orange-100 text-orange-700': role === 'customer',
                                    'bg-purple-100 text-purple-700': role === 'cook',
                                    'bg-blue-100 text-blue-700': role === 'delivery_driver'
                                }"
                                x-text="role === 'cook' ? '👨‍🍳 Cocinero' : (role === 'delivery_driver' ? '🚴 Repartidor' : '🍽️ Cliente')">
                            </span>
                        </div>

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-100 transition @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-100 transition @error('email') border-red-500 @enderror">
                            @error('email')
                                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                                    <p class="font-semibold">{{ $message }}</p>
                                    <p class="text-gray-600">
                                        ¿Ya tienes una cuenta o intentaste registrarte antes? 
                                        <a href="{{ route('login') }}" class="text-purple-600 font-bold underline hover:text-purple-800">
                                            Inicia sesión aquí
                                        </a> para entrar y completar tu perfil de cocina.
                                    </p>
                                </div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-6">
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Teléfono Celular (WhatsApp) <span class="text-pink-600">*</span>
                            </label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                placeholder="Ej: 11 2345 6789 o 351 234 5678"
                                class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-100 transition @error('phone') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                <span class="mr-1">💡</span> 10 dígitos con código de área (sin 0 ni 15). Usado para enviarte notificaciones por WhatsApp.
                            </p>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" @click="prevStep"
                                class="w-1/3 border-2 border-gray-200 text-gray-600 px-6 py-4 rounded-xl font-bold hover:bg-gray-50 transition-all">
                                Volver
                            </button>
                            <button type="button" @click="nextStep"
                                class="w-2/3 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                Siguiente
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Password -->
                    <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-8"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Seguridad de la cuenta</h3>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1"
                                :class="{
                                    'bg-orange-100 text-orange-700': role === 'customer',
                                    'bg-purple-100 text-purple-700': role === 'cook',
                                    'bg-blue-100 text-blue-700': role === 'delivery_driver'
                                }"
                                x-text="role === 'cook' ? '👨‍🍳 Cocinero' : (role === 'delivery_driver' ? '🚴 Repartidor' : '🍽️ Cliente')">
                            </span>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required
                                    class="w-full px-4 py-3 pr-12 border-2 border-gray-100 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition @error('password') border-red-500 @enderror">
                                <button type="button" onclick="togglePasswordVisibility('password', 'eyeIconRegisterPass')"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                    title="Mostrar / Ocultar contraseña">
                                    <svg id="eyeIconRegisterPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-8">
                            <label for="password_confirmation"
                                class="block text-sm font-semibold text-gray-700 mb-2">Confirmar
                                Contraseña</label>
                            <div class="relative">
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    class="w-full px-4 py-3 pr-12 border-2 border-gray-100 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eyeIconRegisterConfirm')"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                    title="Mostrar / Ocultar contraseña">
                                    <svg id="eyeIconRegisterConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" @click="prevStep"
                                class="w-1/3 border-2 border-gray-200 text-gray-600 px-6 py-4 rounded-xl font-bold hover:bg-gray-50 transition-all">
                                Volver
                            </button>
                            <button type="submit" id="btnSubmitRegister"
                                class="w-2/3 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                                <span id="btnSubmitRegisterText">Finalizar Registro</span>
                                <svg id="btnSubmitRegisterSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Overlay de carga durante el registro --}}
                <div id="registerLoadingOverlay" class="fixed inset-0 bg-black/60 z-50 hidden flex-col items-center justify-center backdrop-blur-sm transition-all duration-300">
                    <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4 text-center">
                        <div class="w-16 h-16 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mb-4"></div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Creando tu cuenta...</h3>
                        <p class="text-sm text-gray-500">Estamos configurando tu perfil y tus accesos. Por favor no cierres ni recargues esta ventana.</p>
                    </div>
                </div>

                @push('scripts')
                <script>
                    function togglePasswordVisibility(inputId, iconId) {
                        const input = document.getElementById(inputId);
                        const icon = document.getElementById(iconId);
                        if (!input || !icon) return;

                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 013.122-.563c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.49 4.385m-1.748 1.748L3 3l18 18" />';
                        } else {
                            input.type = 'password';
                            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                        }
                    }

                    let isSubmittingRegister = false;

                    document.getElementById('registerForm').addEventListener('submit', function(e) {
                        if (isSubmittingRegister) {
                            e.preventDefault();
                            return false;
                        }

                        e.preventDefault();
                        isSubmittingRegister = true;

                        const btn = document.getElementById('btnSubmitRegister');
                        const btnText = document.getElementById('btnSubmitRegisterText');
                        const spinner = document.getElementById('btnSubmitRegisterSpinner');
                        const overlay = document.getElementById('registerLoadingOverlay');

                        if (btn) {
                            btn.disabled = true;
                            btn.classList.add('opacity-80', 'cursor-not-allowed');
                        }
                        if (btnText) btnText.textContent = 'Procesando...';
                        if (spinner) spinner.classList.remove('hidden');
                        if (overlay) {
                            overlay.classList.remove('hidden');
                            overlay.classList.add('flex');
                        }

                        // Timeout de seguridad en caso de red congelada
                        setTimeout(function() {
                            if (isSubmittingRegister && !window.submittedSuccessfully) {
                                isSubmittingRegister = false;
                                if (btn) {
                                    btn.disabled = false;
                                    btn.classList.remove('opacity-80', 'cursor-not-allowed');
                                }
                                if (btnText) btnText.textContent = 'Finalizar Registro';
                                if (spinner) spinner.classList.add('hidden');
                                if (overlay) {
                                    overlay.classList.add('hidden');
                                    overlay.classList.remove('flex');
                                }
                            }
                        }, 20000);

                        const form = this;
                        const finalizeSubmit = (token) => {
                            window.submittedSuccessfully = true;
                            if (token) {
                                document.getElementById('g-recaptcha-response').value = token;
                            }
                            form.submit();
                        };

                        if (typeof window.getRecaptchaToken === 'function') {
                            window.getRecaptchaToken('register').then(token => {
                                finalizeSubmit(token);
                            }).catch(err => {
                                console.error('Error reCAPTCHA:', err);
                                finalizeSubmit(null);
                            });
                        } else {
                            finalizeSubmit(null);
                        }
                    });
                </script>
                @endpush
            </div>

            <!-- Login Link -->
            <p class="text-center mt-6 text-gray-600">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
    </div>
@endsection