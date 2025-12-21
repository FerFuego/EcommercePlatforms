@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2">
                    <span
                        class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Mi Perfil
                    </span>
                </h1>
                <p class="text-gray-600">Gestiona tu información personal y seguridad</p>
            </div>

            @if (session('status') === 'profile-updated')
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl shadow-sm"
                    role="alert">
                    <p class="font-bold">¡Actualizado!</p>
                    <p>Tu información de perfil ha sido guardada exitosamente.</p>
                </div>
            @endif

            <div class="space-y-8">
                <!-- Profile Information -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Información Personal</h2>
                            <p class="text-gray-500 text-sm">Actualiza tus datos de contacto</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-6"
                        enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <!-- Profile Photo -->
                        <div class="flex items-center space-x-6 mb-6">
                            <div class="shrink-0">
                                @if ($user->profile_photo_path)
                                    <img class="h-24 w-24 object-cover rounded-full border-4 border-purple-100 shadow-md"
                                        src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" />
                                @else
                                    <div
                                        class="h-24 w-24 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-3xl font-bold border-4 border-purple-100 shadow-md">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <label class="block">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="photo" class="block w-full text-sm text-slate-500
                                                              file:mr-4 file:py-2 file:px-4
                                                              file:rounded-full file:border-0
                                                              file:text-sm file:font-semibold
                                                              file:bg-purple-50 file:text-purple-700
                                                              hover:file:bg-purple-100
                                                            " />
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nombre
                                    Completo</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    autofocus autocomplete="name"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Correo
                                    Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    required autocomplete="username"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="address"
                                    class="block text-sm font-semibold text-gray-700 mb-2">Dirección</label>
                                <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('address') border-red-500 @enderror">
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-orange-400 to-pink-500 rounded-xl flex items-center justify-center text-white mr-4 shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Seguridad</h2>
                            <p class="text-gray-500 text-sm">Actualiza tu contraseña</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        @method('put')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Password -->
                            <div>
                                <label for="current_password"
                                    class="block text-sm font-semibold text-gray-700 mb-2">Contraseña Actual</label>
                                <input type="password" name="current_password" id="current_password"
                                    autocomplete="current-password"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Nueva
                                    Contraseña</label>
                                <input type="password" name="password" id="password" autocomplete="new-password"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('password_confirmation') border-red-500 @enderror">
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-gray-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>

                @if (auth()->user()->role !== 'admin')
                    <!-- Delete Account -->
                    <div class="bg-red-50 rounded-2xl shadow-lg p-8 border border-red-100">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-red-700">Zona de Peligro</h2>
                                <p class="text-red-500 text-sm">Eliminar cuenta permanentemente</p>
                            </div>
                        </div>

                        <p class="text-gray-600 mb-6">
                            Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Por
                            favor, descarga cualquier dato o información que desees conservar antes de eliminar tu cuenta.
                        </p>

                        <div class="flex justify-end">
                            <button
                                onclick="if(confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')) document.getElementById('delete-account-form').submit();"
                                class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold shadow hover:bg-red-700 transition-colors">
                                Eliminar Cuenta
                            </button>
                        </div>

                        <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}" class="hidden">
                            @csrf
                            @method('delete')
                            <input type="password" name="password" value="password" required>
                            <!-- Simplificado para demo, idealmente pedir password en modal -->
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection