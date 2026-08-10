@extends('layouts.app')

@section('title', 'Crear Cuenta')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-pink-50 to-purple-50 flex items-center justify-center py-12 px-4"
        x-data="{
                        step: {{ $errors->has('name') || $errors->has('email') || $errors->has('phone') ? 2 : ($errors->has('password') ? 3 : 1) }},
                        role: '{{ old('role', 'customer') }}',
                        nextStep() { if(this.step < 3) this.step++ },
                        prevStep() { if(this.step > 1) this.step-- }
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

                    <!-- Step 1: Role Selection -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-8"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">¿Cómo quieres usar Cocinarte?</h3>
                        <div class="grid grid-cols-1 gap-4 mb-8">
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role" value="customer" x-model="role" class="peer sr-only">
                                <div
                                    class="border-2 border-gray-100 rounded-2xl p-5 peer-checked:border-orange-500 peer-checked:bg-orange-50 group-hover:border-orange-200 transition-all flex items-center space-x-4">
                                    <div class="text-4xl">🍽️</div>
                                    <div>
                                        <p class="font-bold text-gray-800">Quiero Comer</p>
                                        <p class="text-sm text-gray-500">Descubre sabores caseros cerca de ti</p>
                                    </div>
                                    <div class="ml-auto opacity-0 peer-checked:opacity-100 text-orange-500">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role" value="cook" x-model="role" class="peer sr-only">
                                <div
                                    class="border-2 border-gray-100 rounded-2xl p-5 peer-checked:border-purple-500 peer-checked:bg-purple-50 group-hover:border-purple-200 transition-all flex items-center space-x-4">
                                    <div class="text-4xl">👨‍🍳</div>
                                    <div>
                                        <p class="font-bold text-gray-800">Quiero Cocinar</p>
                                        <p class="text-sm text-gray-500">Vende tus platos y genera ingresos</p>
                                    </div>
                                    <div class="ml-auto opacity-0 peer-checked:opacity-100 text-purple-500">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role" value="delivery_driver" x-model="role" class="peer sr-only">
                                <div
                                    class="border-2 border-gray-100 rounded-2xl p-5 peer-checked:border-blue-500 peer-checked:bg-blue-50 group-hover:border-blue-200 transition-all flex items-center space-x-4">
                                    <div class="text-4xl">🚴</div>
                                    <div>
                                        <p class="font-bold text-gray-800">Quiero Repartir</p>
                                        <p class="text-sm text-gray-500">Entrega pedidos en tu vehículo</p>
                                    </div>
                                    <div class="ml-auto opacity-0 peer-checked:opacity-100 text-blue-500">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>
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
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Tus datos personales</h3>

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
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-6">
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                placeholder="+54 3537 123456"
                                class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-100 transition @error('phone') border-red-500 @enderror">
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
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Seguridad de la cuenta</h3>

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
                            <button type="submit"
                                class="w-2/3 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                                Finalizar Registro
                            </button>
                        </div>
                    </div>
                </form>

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

                    document.getElementById('registerForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const form = this;
                        window.getRecaptchaToken('register').then(token => {
                            document.getElementById('g-recaptcha-response').value = token;
                            form.submit();
                        }).catch(err => {
                            console.error(err);
                            form.submit();
                        });
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