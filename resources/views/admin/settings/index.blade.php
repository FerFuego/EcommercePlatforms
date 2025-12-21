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

                    {{-- General / Other Settings --}}
                    @foreach($settings as $group => $groupSettings)
                        @if($group !== 'seo' && $group !== 'financial')
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
@endsection
