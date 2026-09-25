@extends('layouts.admin')

@section('title', 'Gestión de Usuarios - Admin')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">Gestión de Usuarios</h1>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                    <span class="text-gray-600">Total: <strong>{{ $stats['total'] }}</strong></span>
                    <span class="text-gray-600">Admins: <strong>{{ $stats['admins'] }}</strong></span>
                    <span class="text-gray-600">Cocineros: <strong>{{ $stats['cooks'] }}</strong></span>
                    <span class="text-gray-600">Repartidores: <strong>{{ $stats['drivers'] }}</strong></span>
                    <span class="text-gray-600">Clientes: <strong>{{ $stats['customers'] }}</strong></span>
                </div>
            </div>

            <!-- Quick Filter Pills -->
            <!-- Quick Filter Pills -->
            <div class="flex flex-wrap gap-2 text-xs">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition {{ !request('status') ? 'bg-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   Todos ({{ $stats['total'] }})
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition flex items-center space-x-1 {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' }}">
                   <span>⏳ Pendientes</span>
                   <span class="bg-amber-200 text-amber-900 px-1.5 py-0.2 rounded-full text-[10px]">{{ $stats['pending'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'pending_cooks']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition {{ request('status') === 'pending_cooks' ? 'bg-orange-600 text-white shadow-sm' : 'bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100' }}">
                   👨‍🍳 Cocineros Pendientes ({{ $stats['pending_cooks'] }})
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'pending_drivers']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition {{ request('status') === 'pending_drivers' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' }}">
                   🛵 Repartidores Pendientes ({{ $stats['pending_drivers'] }})
                </a>
                @if(isset($stats['incomplete']) && $stats['incomplete'] > 0)
                    <a href="{{ route('admin.users.index', ['status' => 'incomplete']) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition flex items-center space-x-1 {{ request('status') === 'incomplete' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100' }}">
                       <span>⚠️ Sin Perfil ({{ $stats['incomplete'] }})</span>
                    </a>
                @endif
                @if($stats['suspended'] > 0)
                    <a href="{{ route('admin.users.index', ['status' => 'suspended']) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition {{ request('status') === 'suspended' ? 'bg-red-600 text-white shadow-sm' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' }}">
                       ❌ Suspendidos ({{ $stats['suspended'] }})
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Search & Status Filters Form -->
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" 
                                value="{{ request('search') }}"
                                placeholder="Nombre, email, teléfono, dirección..."
                                class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm">
                        </div>
                    </div>

                    <!-- Combo de Estado / Moderación -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado de Moderación</label>
                        <select name="status" id="status" class="w-full px-3 py-2 rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm font-semibold text-sm">
                            <option value="">Todos los Estados</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pendientes de Revisión ({{ $stats['pending'] }})</option>
                            <option value="pending_cooks" {{ request('status') === 'pending_cooks' ? 'selected' : '' }}>👨‍🍳 Cocineros Pendientes ({{ $stats['pending_cooks'] }})</option>
                            <option value="pending_drivers" {{ request('status') === 'pending_drivers' ? 'selected' : '' }}>🛵 Repartidores Pendientes ({{ $stats['pending_drivers'] }})</option>
                            @if(isset($stats['incomplete']) && $stats['incomplete'] > 0)
                                <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>⚠️ Sin Perfil / Incompletos ({{ $stats['incomplete'] }})</option>
                            @endif
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✓ Activos</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>❌ Suspendidos ({{ $stats['suspended'] }})</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                        <input type="date" name="date" id="date" 
                            value="{{ request('date') }}"
                            class="w-full px-4 py-2 rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm">
                    </div>

                    <div>
                        <label for="submit">&nbsp;</label>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-blue-700 transition shadow-md">
                                Filtrar
                            </button>
                            @if(request()->has('search') || request()->has('date') || request()->has('status'))
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition" title="Limpiar filtros">
                                    ↺
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-4 text-left text-sm font-bold text-gray-700">Usuario</th>
                        <th class="px-2 py-4 text-left text-sm font-bold text-gray-700">Contacto</th>
                        <th class="px-2 py-4 text-center text-sm font-bold text-gray-700">Rol</th>
                        <th class="px-2 py-4 text-center text-sm font-bold text-gray-700">Estado</th>
                        <th class="px-2 py-4 text-center text-sm font-bold text-gray-700">Registro</th>
                        <th class="px-2 py-4 text-center text-sm font-bold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        @php
                            $cookData = $user->cook ? [
                                'id' => $user->cook->id,
                                'bio' => $user->cook->bio,
                                'is_approved' => (bool)$user->cook->is_approved,
                                'active' => (bool)$user->cook->active,
                                'coverage_radius_km' => $user->cook->coverage_radius_km,
                                'opening_time' => $user->cook->opening_time,
                                'closing_time' => $user->cook->closing_time,
                                'payout_method' => $user->cook->payout_method,
                                'payout_details' => $user->cook->payout_details,
                                'food_handler_declaration' => (bool)$user->cook->food_handler_declaration,
                                'dni_photo' => $user->cook->dni_photo,
                                'kitchen_photos' => $user->cook->kitchen_photos,
                                'location_lat' => $user->cook->location_lat,
                                'location_lng' => $user->cook->location_lng,
                            ] : null;

                            $driverData = $user->deliveryDriver ? [
                                'id' => $user->deliveryDriver->id,
                                'dni_number' => $user->deliveryDriver->dni_number,
                                'dni_photo' => $user->deliveryDriver->dni_photo,
                                'bank_name' => $user->deliveryDriver->bank_name,
                                'cbu_cvu' => $user->deliveryDriver->cbu_cvu,
                                'account_number' => $user->deliveryDriver->account_number,
                                'vehicle_type' => $user->deliveryDriver->vehicle_type,
                                'vehicle_plate' => $user->deliveryDriver->vehicle_plate,
                                'vehicle_photo' => $user->deliveryDriver->vehicle_photo,
                                'profile_photo' => $user->deliveryDriver->profile_photo,
                                'coverage_radius_km' => $user->deliveryDriver->coverage_radius_km,
                                'is_approved' => (bool)$user->deliveryDriver->is_approved,
                                'is_available' => (bool)$user->deliveryDriver->is_available,
                                'location_lat' => $user->deliveryDriver->location_lat,
                                'location_lng' => $user->deliveryDriver->location_lng,
                            ] : null;

                            $userPayload = [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'phone' => $user->phone ?? 'No especificado',
                                'address' => $user->address ?? 'No especificada',
                                'role' => $user->role,
                                'role_label' => $user->role === 'cook' ? 'Cocinero' : ($user->role === 'delivery_driver' ? 'Repartidor' : ($user->role === 'admin' ? 'Administrador' : 'Cliente')),
                                'is_suspended' => (bool)($user->is_suspended ?? false),
                                'email_verified' => $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i') : null,
                                'created_at' => $user->created_at->format('d/m/Y H:i:s'),
                                'created_diff' => $user->created_at->diffForHumans(),
                                'orders_count' => $user->orders_count ?? 0,
                                'cook' => $cookData,
                                'driver' => $driverData,
                                'is_self' => $user->id === auth()->id(),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-2 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($user->profile_photo_path)
                                        <img src="{{ asset('uploads/' . $user->profile_photo_path) }}" alt="{{ $user->name }}"
                                            class="w-10 h-10 object-cover rounded-full">
                                    @else
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <button type="button" onclick='openUserDetailsModal(@json($userPayload))' 
                                            class="font-bold text-gray-900 hover:text-purple-600 transition text-left cursor-pointer">
                                            {{ $user->name }}
                                        </button>
                                        @if($user->role === 'cook')
                                            @if(!$user->cook)
                                                <p class="text-[11px] text-amber-600 font-semibold flex items-center gap-0.5">
                                                    <span>⚠️ Perfil no creado</span>
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-600">
                                                    {{ $user->cook->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                                </p>
                                            @endif
                                        @elseif($user->role === 'delivery_driver')
                                            @if(!$user->deliveryDriver)
                                                <p class="text-[11px] text-amber-600 font-semibold flex items-center gap-0.5">
                                                    <span>⚠️ Doc. no enviada</span>
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-600">
                                                    {{ $user->deliveryDriver->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-4">
                                <p class="text-sm"><b>email:</b> {{ $user->email }}</p>
                                <p class="text-sm"><b>tel:</b> {{ $user->phone ?? 'N/A' }}</p>
                            </td>
                            <td class="px-2 py-4 text-center">
                                @if($user->role === 'admin')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        👑 Admin
                                    </span>
                                @elseif($user->role === 'cook')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Cocinero</span>
                                @elseif($user->role === 'delivery_driver')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Repartidor</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Cliente</span>
                                @endif
                            </td>
                            <td class="px-2 py-4 text-center">
                                @if($user->is_suspended ?? false)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Suspendido</span>
                                @elseif($user->role === 'cook' && !$user->cook)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300" title="Registrado como cocinero pero nunca completó su perfil de cocina">
                                        Incompleto
                                    </span>
                                @elseif($user->role === 'cook' && $user->cook && !$user->cook->is_approved)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 animate-pulse">Pendiente</span>
                                @elseif($user->role === 'delivery_driver' && !$user->deliveryDriver)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300" title="Registrado como repartidor pero nunca completó su documentación">
                                        Incompleto
                                    </span>
                                @elseif($user->role === 'delivery_driver' && $user->deliveryDriver && !$user->deliveryDriver->is_approved)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 animate-pulse">Pendiente</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Activo</span>
                                @endif
                            </td>
                            <td class="px-2 py-4 text-center">
                                <p class="text-sm text-gray-600">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                            </td>
                            <td class="px-2 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Botón Ver Datos (Para TODOS los usuarios) --}}
                                    <button type="button" onclick='openUserDetailsModal(@json($userPayload))'
                                        class="px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-lg text-xs font-semibold transition flex items-center gap-1 shadow-sm"
                                        title="Ver ficha y datos del usuario">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver Datos
                                    </button>

                                    {{-- Botón Aprobar (Solo para pendientes con perfil creado) --}}
                                    @if(
                                            ($user->role === 'cook' && $user->cook && !$user->cook->is_approved) ||
                                            ($user->role === 'delivery_driver' && $user->deliveryDriver && !$user->deliveryDriver->is_approved)
                                        )
                                        <button onclick="openApprovalModal({
                                                                    id: {{ $user->id }},
                                                                    name: '{{ addslashes($user->name) }}',
                                                                    role: '{{ $user->role }}',
                                                                    roleLabel: '{{ $user->role === 'cook' ? 'Cocinero' : 'Repartidor' }}',
                                                                    address: '{{ addslashes($user->address ?? '') }}',
                                                                    phone: '{{ addslashes($user->phone ?? '') }}',
                                                                    email: '{{ addslashes($user->email ?? '') }}',
                                                                    details: {{ json_encode($user->role === 'cook' ? $user->cook : $user->deliveryDriver) }}
                                                                })"
                                            class="px-2.5 py-1 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition"
                                            title="Revisar Solicitud de Aprobación">Revisar
                                        </button>
                                    @endif

                                    {{-- Toggle Suspender/Activar --}}
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ ($user->is_suspended ?? false) ? 'bg-green-500 text-white hover:bg-green-700' : 'bg-yellow-500 text-white hover:bg-yellow-600' }}"
                                                title="{{ ($user->is_suspended ?? false) ? 'Activar' : 'Suspender' }}">
                                                {{ ($user->is_suspended ?? false) ? 'Activar' : 'Suspender' }}
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <button onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="px-2.5 py-1 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition"
                                            title="Eliminar">Eliminar
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-500 italic">Tu cuenta</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Approval Modal --}}
    <div id="approvalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-10">
        <div class="bg-white rounded-2xl p-8 max-w-4xl w-full mx-4 shadow-2xl relative">
            <button onclick="closeApprovalModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="mb-6 border-b pb-4">
                <h3 class="text-3xl font-bold text-gray-800">📋 Revisar Solicitud</h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-gray-600">Solicitante:</span>
                    <strong id="modalUserName" class="text-xl text-gray-900"></strong>
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-gray-600">Rol:</span>
                    <strong id="modalUserRole" class="text-xl text-purple-600"></strong>
                </div>
            </div>
            
            <div id="modalDetailsContent" class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 overflow-y-auto max-h-[60vh] pr-2">
                <!-- Filled by JS -->
            </div>

            <div class="flex space-x-4 border-t pt-6">
                <form id="approveForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full bg-green-500 text-white px-2 py-4 rounded-xl font-bold hover:bg-green-600 transition flex items-center justify-center gap-2 text-lg shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Aprobar Solicitud
                    </button>
                </form>
                
                <button type="button" onclick="toggleRejectForm(true)"
                    class="flex-1 bg-red-500 text-white px-2 py-4 rounded-xl font-bold hover:bg-red-600 transition flex items-center justify-center gap-2 text-lg shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Rechazar Solicitud
                </button>
            </div>

            <!-- Rejection Reason Container (Hidden by default) -->
            <div id="rejectReasonBox" class="hidden mt-6 bg-red-50 p-6 rounded-2xl border-2 border-red-200">
                <h4 class="font-bold text-red-800 text-lg mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Motivo del Rechazo
                </h4>
                <p class="text-sm text-red-700 mb-3">Ingresa la explicación del rechazo. El usuario la recibirá por email y se le notificará al acceder a la plataforma.</p>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason_input" class="block text-xs font-bold text-red-800 uppercase mb-1">Detalle del motivo *</label>
                        <textarea name="rejection_reason" id="rejection_reason_input" required rows="3" 
                            placeholder="Escribe el motivo del rechazo (ej: Documentación de DNI ilegible, fotos de cocina insuficientes...)"
                            class="w-full px-4 py-3 rounded-xl border-red-300 focus:border-red-500 focus:ring focus:ring-red-200 text-sm shadow-sm text-gray-800"></textarea>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition text-sm shadow-md flex items-center gap-2">
                            <span>Confirmar y Enviar Rechazo</span>
                        </button>
                        <button type="button" onclick="toggleRejectForm(false)" class="bg-gray-200 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-300 transition text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-4 text-red-600">⚠️ Confirmar Eliminación</h3>
            <p class="text-gray-700 mb-6">
                ¿Estás seguro de que deseas eliminar al usuario <strong id="userName"></strong>?
                <br><br>
                <span class="text-red-600 font-semibold">Esta acción no se puede deshacer.</span>
            </p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex space-x-3">
                    <button type="submit"
                        class="flex-1 bg-red-600 text-white px-2 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                        Sí, Eliminar
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-2 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- User Details Modal (Ficha del Usuario / Detección Bot) --}}
    <div id="userDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 overflow-y-auto py-10 px-4">
        <div class="bg-white rounded-3xl p-6 md:p-8 max-w-4xl w-full shadow-2xl relative max-h-[90vh] flex flex-col">
            {{-- Modal Header --}}
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 shrink-0">
                <div class="flex items-center space-x-4">
                    <div id="udmAvatar" class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                        U
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 id="udmName" class="text-2xl font-bold text-gray-900">Usuario</h3>
                            <span id="udmRoleBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold"></span>
                            <span id="udmStatusBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold"></span>
                        </div>
                        <p id="udmEmail" class="text-sm text-gray-500 font-medium"></p>
                    </div>
                </div>
                <button type="button" onclick="closeUserDetailsModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="overflow-y-auto py-6 pr-2 space-y-6 flex-1" id="udmBody">
                <!-- Dynamically populated by JS -->
            </div>

            {{-- Modal Footer --}}
            <div class="pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2" id="udmActions">
                    <!-- Action buttons (Suspend, Delete, Review) -->
                </div>
                <button type="button" onclick="closeUserDetailsModal()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openApprovalModal(data) {
                const modal = document.getElementById('approvalModal');
                const approveForm = document.getElementById('approveForm');
                const rejectForm = document.getElementById('rejectForm');
                const content = document.getElementById('modalDetailsContent');
                
                document.getElementById('modalUserName').textContent = data.name;
                document.getElementById('modalUserRole').textContent = data.roleLabel;

                const details = data.details;
                let html = '';
                const storageUrl = path => path ? `/uploads/${path}` : null;

                if (data.role === 'cook') {
                    // Set correct routes (adjust as needed based on your routes file)
                    approveForm.action = `/admin/cooks/${details.id}/approve`;
                    rejectForm.action = `/admin/cooks/${details.id}/reject`;

                    // Col 1: Información Personal y Profesional
                    html += `<div class="space-y-6">`;
                    
                    // Bio & Info
                    html += `
                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">📂 Información General</h4>
                            <p class="mb-2"><span class="font-semibold">Bio:</span> <span class="text-gray-600">${details.bio || 'Sin bio'}</span></p>
                            <p class="mb-2"><span class="font-semibold">Manipulación Alimentos:</span> ${details.food_handler_declaration ? '<span class="text-green-600 font-bold">✓ Declarado</span>' : '<span class="text-red-500 font-bold">✕ No declarado</span>'}</p>
                            <p class="mb-2"><span class="font-semibold">Método Cobro:</span> ${details.payout_method || 'N/A'}</p>
                    `;
                    
                    if (details.payout_details) {
                        let payoutData = details.payout_details;
                        if (typeof payoutData === 'string') {
                             try { payoutData = JSON.parse(payoutData); } catch(e) {}
                        }
                        if (typeof payoutData === 'object' && payoutData !== null) {
                             html += `<div class="mt-2 text-sm bg-white p-2 rounded border">`;
                             for (const [key, value] of Object.entries(payoutData)) {
                                 html += `<p><span class="font-semibold capitalize">${key.replace(/_/g, ' ')}:</span> ${value}</p>`;
                             }
                             html += `</div>`;
                        }
                    }
                    html += `</div>`;

                     // DNI
                     if (details.dni_photo) {
                        html += `
                            <div class="bg-gray-50 p-5 rounded-xl border">
                                <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">🆔 Documento de Identidad</h4>
                                <div class="rounded-lg overflow-hidden border shadow-sm">
                                    <img src="/admin/documents/dni/cook/${details.id}" class="w-full h-auto object-contain hover:scale-105 transition duration-300" alt="Foto DNI">
                                </div>
                            </div>
                        `;
                    }
                    html += `</div>`;

                    // Col 2: Fotos de Cocina
                    html += `<div class="space-y-6">`;
                    if (details.kitchen_photos) {
                        let photos = [];
                        try {
                            photos = typeof details.kitchen_photos === 'string' ? JSON.parse(details.kitchen_photos) : details.kitchen_photos;
                        } catch(e) { photos = []; }

                        html += `
                            <div class="bg-gray-50 p-5 rounded-xl border">
                                <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">🍳 Fotos de la Cocina</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    ${photos.map(photo => `
                                        <a href="${storageUrl(photo)}" target="_blank" class="block rounded-lg overflow-hidden border hover:opacity-90">
                                            <img src="${storageUrl(photo)}" class="w-full h-24 object-cover" alt="Cocina">
                                        </a>
                                    `).join('')}
                                </div>
                                ${photos.length === 0 ? '<p class="text-gray-500 italic">No hay fotos de cocina</p>' : ''}
                            </div>
                        `;
                    }
                     // Location & Hours
                     const cookManualAddress = data.address && data.address.trim() !== '' 
                         ? `<span class="font-bold text-gray-900">${data.address}</span>` 
                         : '<span class="text-amber-600 italic">No especificada manualmente</span>';

                     const cookGpsCoords = (details.location_lat && details.location_lng) 
                         ? `${details.location_lat}, ${details.location_lng}` 
                         : '<span class="text-amber-600 font-semibold italic">✕ No detectada automáticamente</span>';

                     const openTime = details.opening_time ? details.opening_time.substring(0, 5) : null;
                     const closeTime = details.closing_time ? details.closing_time.substring(0, 5) : null;
                     const hoursFormatted = (openTime && closeTime) 
                         ? `<span class="font-bold text-purple-700">${openTime} a ${closeTime} hs</span>` 
                         : '<span class="text-amber-600 italic">No configurado</span>';

                     html += `
                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">📍 Ubicación & Cobertura</h4>
                            <p class="mb-2"><span class="font-semibold text-gray-700">Dirección Manual:</span> <br>${cookManualAddress}</p>
                            <p class="mb-2"><span class="font-semibold text-gray-700">GPS / Coordenadas:</span> ${cookGpsCoords}</p>
                            <p><span class="font-semibold text-gray-700">Radio de Entrega:</span> ${details.coverage_radius_km || 'N/A'} km</p>
                        </div>

                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">🕒 Horario del Local</h4>
                            <p class="mb-1"><span class="font-semibold text-gray-700">Apertura:</span> ${openTime || 'N/A'}</p>
                            <p class="mb-1"><span class="font-semibold text-gray-700">Cierre:</span> ${closeTime || 'N/A'}</p>
                            <div class="mt-3 p-2.5 bg-purple-50 border border-purple-100 rounded-lg text-sm text-purple-900">
                                <span class="font-semibold">Jornada Comercial:</span> ${hoursFormatted}
                            </div>
                        </div>
                    `;
                    html += `</div>`;

                } else {
                    // DRIVER
                    approveForm.action = `/admin/drivers/${details.id}/approve`;
                    rejectForm.action = `/admin/drivers/${details.id}/reject`;

                    // Col 1: Datos Personales y Pago
                    html += `<div class="space-y-6">`;
                    
                    html += `
                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">👤 Datos Personales & Pago</h4>
                            <p class="mb-2"><span class="font-semibold">DNI:</span> ${details.dni_number || 'N/A'}</p>
                            <p class="mb-2"><span class="font-semibold">Banco:</span> ${details.bank_name || 'N/A'}</p>
                            <p class="mb-2"><span class="font-semibold">CBU/CVU:</span> ${details.cbu_cvu || 'N/A'}</p>
                            <p class="mb-2"><span class="font-semibold">Nro Cuenta:</span> ${details.account_number || 'N/A'}</p>
                        </div>
                    `;

                     // DNI Photo
                     if (details.dni_photo) {
                        html += `
                            <div class="bg-gray-50 p-5 rounded-xl border">
                                <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">🆔 Foto DNI</h4>
                                <div class="rounded-lg overflow-hidden border shadow-sm">
                                    <img src="/admin/documents/dni/driver/${details.id}" class="w-full h-auto object-contain" alt="Foto DNI">
                                </div>
                            </div>
                        `;
                    }
                    html += `</div>`;

                    // Col 2: Vehículo, Ubicación y Foto Perfil
                    const driverManualAddress = data.address && data.address.trim() !== '' 
                        ? `<span class="font-bold text-gray-900">${data.address}</span>` 
                        : '<span class="text-amber-600 italic">No especificada manualmente</span>';

                    const driverGpsCoords = (details.location_lat && details.location_lng) 
                        ? `${details.location_lat}, ${details.location_lng}` 
                        : '<span class="text-amber-600 font-semibold italic">✕ No detectada automáticamente</span>';

                    html += `<div class="space-y-6">`;
                    html += `
                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">🚗 Vehículo</h4>
                            <p class="mb-2"><span class="font-semibold">Tipo:</span> <span class="capitalize">${details.vehicle_type || 'N/A'}</span></p>
                            <p class="mb-2"><span class="font-semibold">Patente:</span> ${details.vehicle_plate || 'N/A'}</p>
                        </div>

                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">📍 Ubicación Base & Cobertura</h4>
                            <p class="mb-2"><span class="font-semibold text-gray-700">Dirección Manual:</span> <br>${driverManualAddress}</p>
                            <p class="mb-2"><span class="font-semibold text-gray-700">GPS Base:</span> ${driverGpsCoords}</p>
                            <p><span class="font-semibold text-gray-700">Radio de Cobertura:</span> ${details.coverage_radius_km || 'N/A'} km</p>
                        </div>
                    `;

                    if (details.vehicle_photo) {
                        html += `
                            <div class="bg-gray-50 p-5 rounded-xl border">
                                <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">📸 Foto del Vehículo</h4>
                                <div class="rounded-lg overflow-hidden border shadow-sm">
                                    <img src="${storageUrl(details.vehicle_photo)}" class="w-full h-auto object-contain" alt="Foto Vehículo">
                                </div>
                            </div>
                        `;
                    }

                    if (details.profile_photo) {
                         html += `
                            <div class="bg-gray-50 p-5 rounded-xl border">
                                <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">📸 Foto de Perfil (Driver)</h4>
                                <div class="rounded-lg overflow-hidden border shadow-sm w-32 mx-auto">
                                    <img src="${storageUrl(details.profile_photo)}" class="w-full h-32 object-cover" alt="Foto Perfil">
                                </div>
                            </div>
                        `;
                    }
                    html += `</div>`;
                }
                
                content.innerHTML = html;
                toggleRejectForm(false);
                modal.classList.remove('hidden');
            }

            function toggleRejectForm(show) {
                const box = document.getElementById('rejectReasonBox');
                const input = document.getElementById('rejection_reason_input');
                if (!box) return;

                if (show) {
                    box.classList.remove('hidden');
                    if (input) input.focus();
                } else {
                    box.classList.add('hidden');
                    if (input) input.value = '';
                }
            }

            function closeApprovalModal() {
                toggleRejectForm(false);
                document.getElementById('approvalModal').classList.add('hidden');
            }

            function confirmDelete(userId, userName) {
                const modal = document.getElementById('deleteModal');
                const form = document.getElementById('deleteForm');
                const userNameSpan = document.getElementById('userName');

                form.action = `/admin/users/${userId}`;
                userNameSpan.textContent = userName;
                modal.classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            function openUserDetailsModal(user) {
                const modal = document.getElementById('userDetailsModal');
                const avatar = document.getElementById('udmAvatar');
                const nameEl = document.getElementById('udmName');
                const emailEl = document.getElementById('udmEmail');
                const roleBadge = document.getElementById('udmRoleBadge');
                const statusBadge = document.getElementById('udmStatusBadge');
                const body = document.getElementById('udmBody');
                const actions = document.getElementById('udmActions');

                // Header info
                avatar.textContent = (user.name || 'U').charAt(0).toUpperCase();
                nameEl.textContent = user.name;
                emailEl.textContent = user.email;

                // Role badge
                let roleColor = 'bg-gray-100 text-gray-800';
                if (user.role === 'admin') roleColor = 'bg-purple-100 text-purple-800';
                else if (user.role === 'cook') roleColor = 'bg-orange-100 text-orange-800';
                else if (user.role === 'delivery_driver') roleColor = 'bg-blue-100 text-blue-800';
                else if (user.role === 'customer') roleColor = 'bg-green-100 text-green-800';
                roleBadge.className = `px-2.5 py-0.5 rounded-full text-xs font-bold ${roleColor}`;
                roleBadge.textContent = user.role_label;

                // Status badge
                let statusText = 'Activo';
                let statusColor = 'bg-green-100 text-green-800';
                if (user.is_suspended) {
                    statusText = 'Suspendido';
                    statusColor = 'bg-red-100 text-red-800';
                } else if (user.role === 'cook' && !user.cook) {
                    statusText = 'Incompleto (Sin Cocina)';
                    statusColor = 'bg-amber-100 text-amber-800 border border-amber-300';
                } else if (user.role === 'cook' && user.cook && !user.cook.is_approved) {
                    statusText = 'Pendiente de Aprobación';
                    statusColor = 'bg-yellow-100 text-yellow-800 animate-pulse';
                } else if (user.role === 'delivery_driver' && !user.driver) {
                    statusText = 'Incompleto (Sin Doc)';
                    statusColor = 'bg-amber-100 text-amber-800 border border-amber-300';
                } else if (user.role === 'delivery_driver' && user.driver && !user.driver.is_approved) {
                    statusText = 'Pendiente de Aprobación';
                    statusColor = 'bg-yellow-100 text-yellow-800 animate-pulse';
                }
                statusBadge.className = `px-2.5 py-0.5 rounded-full text-xs font-bold ${statusColor}`;
                statusBadge.textContent = statusText;

                // Body content
                const safe = str => str ? String(str).replace(/[&<>'"]/g, tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)) : '';

                let html = `<div class="grid grid-cols-1 md:grid-cols-2 gap-6">`;

                // Columna 1: Datos de Registro y Contacto
                html += `
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-3 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Información de la Cuenta
                            </h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="text-gray-500 font-medium">ID Usuario:</span> <strong class="text-gray-800">#${user.id}</strong></p>
                                <p><span class="text-gray-500 font-medium">Nombre:</span> <strong class="text-gray-900">${safe(user.name)}</strong></p>
                                <p><span class="text-gray-500 font-medium">Email:</span> <span class="font-semibold">${safe(user.email)}</span></p>
                                <p><span class="text-gray-500 font-medium">Verificación Email:</span> ${user.email_verified ? `<span class="text-green-600 font-bold">✓ Verificado (${user.email_verified})</span>` : `<span class="text-amber-600 font-semibold">⚠️ No verificado</span>`}</p>
                                <p><span class="text-gray-500 font-medium">Teléfono:</span> <strong class="text-gray-800">${safe(user.phone)}</strong></p>
                                <p><span class="text-gray-500 font-medium">Dirección:</span> <span class="text-gray-800">${safe(user.address)}</span></p>
                                <p><span class="text-gray-500 font-medium">Fecha Alta:</span> <span class="text-gray-800">${user.created_at} <span class="text-xs text-gray-500">(${user.created_diff})</span></span></p>
                                ${user.role === 'customer' ? `<p><span class="text-gray-500 font-medium">Pedidos realizados:</span> <strong>${user.orders_count || 0}</strong></p>` : ''}
                            </div>
                        </div>
                `;

                // Diagnóstico de Bot / Alerta de Incompleto
                const isCookIncomplete = user.role === 'cook' && !user.cook;
                const isDriverIncomplete = user.role === 'delivery_driver' && !user.driver;
                const isForeignPhone = user.phone && (user.phone.startsWith('+34') || user.phone.startsWith('34') || (!user.phone.startsWith('+54') && !user.phone.startsWith('54') && !user.phone.startsWith('35') && !user.phone.startsWith('11') && user.phone.startsWith('+')));

                if (isCookIncomplete || isDriverIncomplete || isForeignPhone) {
                    html += `
                        <div class="bg-amber-50 border-2 border-amber-200 p-5 rounded-2xl">
                            <h4 class="font-bold text-amber-900 text-sm mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Diagnóstico de Cuenta / Detección de Bot
                            </h4>
                            <div class="text-xs text-amber-800 space-y-1.5">
                                ${isCookIncomplete ? `
                                    <p class="font-semibold text-red-700">⚠️ Registro Incompleto (Sin Cocina Creada):</p>
                                    <p>Este usuario completó el Paso 1 de registro como "Cocinero", pero <strong>NUNCA envió fotos de cocina, DNI ni completó el formulario de aprobación</strong>.</p>
                                    <p class="font-semibold text-gray-800 mt-1">🔒 Impacto en la Plataforma:</p>
                                    <p>El usuario <strong>NO puede operar, NO puede publicar platos y NO aparece en la tienda</strong>.</p>
                                ` : ''}
                                ${isDriverIncomplete ? `
                                    <p class="font-semibold text-red-700">⚠️ Registro Incompleto (Sin Documentación):</p>
                                    <p>No cargó vehículo, patente ni DNI de repartidor. No puede tomar pedidos.</p>
                                ` : ''}
                                ${isForeignPhone ? `
                                    <p class="font-semibold text-amber-900 mt-2">🌍 Prefijo Telefónico Internacional:</p>
                                    <p>El teléfono <code>${safe(user.phone)}</code> parece ser internacional o de prueba (común en bots automatizados).</p>
                                ` : ''}
                                <p class="text-[11px] text-gray-500 italic mt-2">💡 Si sospechas que es una cuenta falsa o bot, puedes eliminarla directamente con el botón rojo abajo.</p>
                            </div>
                        </div>
                    `;
                }

                html += `</div>`; // Fin Columna 1

                // Columna 2: Datos de Cocinero / Repartidor
                html += `<div class="space-y-4">`;

                if (user.role === 'cook') {
                    if (user.cook) {
                        const cook = user.cook;
                        html += `
                            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h4 class="font-bold text-gray-800 mb-3 text-sm flex items-center justify-between">
                                    <span class="flex items-center gap-2">👨‍🍳 Perfil de Cocina</span>
                                    ${cook.is_approved ? '<span class="text-green-600 text-xs font-bold">✓ Aprobado</span>' : '<span class="text-amber-600 text-xs font-bold animate-pulse">⏳ Pendiente de Aprobación</span>'}
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <p><span class="text-gray-500 font-medium">Bio:</span> <span class="text-gray-800">${safe(cook.bio || 'Sin descripción')}</span></p>
                                    <p><span class="text-gray-500 font-medium">Manipulación Alimentos:</span> ${cook.food_handler_declaration ? '<span class="text-green-600 font-bold">✓ Declarado</span>' : '<span class="text-red-600 font-bold">✕ No</span>'}</p>
                                    <p><span class="text-gray-500 font-medium">Radio de entrega:</span> <strong class="text-gray-800">${cook.coverage_radius_km || 'N/A'} km</strong></p>
                                    <p><span class="text-gray-500 font-medium">Horario:</span> <span class="text-purple-700 font-bold">${cook.opening_time ? cook.opening_time.substring(0, 5) : 'N/A'} a ${cook.closing_time ? cook.closing_time.substring(0, 5) : 'N/A'} hs</span></p>
                                    <p><span class="text-gray-500 font-medium">Método de cobro:</span> <span>${safe(cook.payout_method || 'No configurado')}</span></p>
                                </div>
                            </div>
                        `;

                        // DNI y Fotos de cocina
                        if (cook.dni_photo) {
                            html += `
                                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                    <h4 class="font-bold text-gray-800 mb-2 text-sm">🆔 Documento de Identidad</h4>
                                    <a href="/admin/documents/dni/cook/${cook.id}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-purple-600 hover:text-purple-800 bg-purple-50 px-3 py-2 rounded-xl border border-purple-200">
                                        <span>Ver DNI Confidencial ↗</span>
                                    </a>
                                </div>
                            `;
                        }

                        if (cook.kitchen_photos) {
                            let photos = [];
                            try {
                                photos = typeof cook.kitchen_photos === 'string' ? JSON.parse(cook.kitchen_photos) : cook.kitchen_photos;
                            } catch(e) { photos = []; }

                            if (Array.isArray(photos) && photos.length > 0) {
                                html += `
                                    <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                        <h4 class="font-bold text-gray-800 mb-3 text-sm">🍳 Fotos de la Cocina (${photos.length})</h4>
                                        <div class="grid grid-cols-3 gap-2">
                                            ${photos.map(p => `
                                                <a href="/uploads/${p}" target="_blank" class="block rounded-lg overflow-hidden border hover:opacity-80">
                                                    <img src="/uploads/${p}" class="w-full h-16 object-cover" alt="Cocina">
                                                </a>
                                            `).join('')}
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    } else {
                        html += `
                            <div class="bg-gray-50 p-5 rounded-2xl border border-dashed border-gray-300 text-center py-8">
                                <div class="text-4xl mb-2">🍳</div>
                                <p class="font-bold text-gray-700">Sin Datos de Cocina</p>
                                <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                                    Este usuario no ha completado el formulario de cocinero. No tiene DNI cargado ni fotos de cocina en la base de datos.
                                </p>
                            </div>
                        `;
                    }
                } else if (user.role === 'delivery_driver') {
                    if (user.driver) {
                        const driver = user.driver;
                        html += `
                            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h4 class="font-bold text-gray-800 mb-3 text-sm flex items-center justify-between">
                                    <span class="flex items-center gap-2">🛵 Perfil de Repartidor</span>
                                    ${driver.is_approved ? '<span class="text-green-600 text-xs font-bold">✓ Aprobado</span>' : '<span class="text-amber-600 text-xs font-bold animate-pulse">⏳ Pendiente</span>'}
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <p><span class="text-gray-500 font-medium">Vehículo:</span> <strong class="capitalize">${safe(driver.vehicle_type || 'N/A')}</strong></p>
                                    <p><span class="text-gray-500 font-medium">Patente:</span> <span class="font-mono font-bold">${safe(driver.vehicle_plate || 'N/A')}</span></p>
                                    <p><span class="text-gray-500 font-medium">Radio Cobertura:</span> <span>${driver.coverage_radius_km || 'N/A'} km</span></p>
                                    <p><span class="text-gray-500 font-medium">Banco / CBU:</span> <span>${safe(driver.bank_name || '')} (${safe(driver.cbu_cvu || 'N/A')})</span></p>
                                </div>
                            </div>
                        `;
                        if (driver.dni_photo) {
                            html += `
                                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                    <h4 class="font-bold text-gray-800 mb-2 text-sm">🆔 DNI Repartidor: ${safe(driver.dni_number || '')}</h4>
                                    <a href="/admin/documents/dni/driver/${driver.id}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-2 rounded-xl border border-blue-200">
                                        <span>Ver DNI Confidencial ↗</span>
                                    </a>
                                </div>
                            `;
                        }
                    } else {
                        html += `
                            <div class="bg-gray-50 p-5 rounded-2xl border border-dashed border-gray-300 text-center py-8">
                                <div class="text-4xl mb-2">🛵</div>
                                <p class="font-bold text-gray-700">Sin Documentación de Repartidor</p>
                                <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                                    No ha enviado datos de vehículo, licencia ni DNI.
                                </p>
                            </div>
                        `;
                    }
                } else {
                    html += `
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-3 text-sm">🛒 Actividad del Cliente</h4>
                            <p class="text-sm"><span class="text-gray-500">Total de pedidos:</span> <strong>${user.orders_count || 0}</strong></p>
                            <p class="text-xs text-gray-500 mt-2">Los clientes no requieren moderación administrativa ni subida de DNI.</p>
                        </div>
                    `;
                }

                html += `</div>`; // Fin Columna 2
                html += `</div>`; // Fin Grid

                body.innerHTML = html;

                // Actions Footer
                let actionsHtml = '';
                const isPendingCook = user.role === 'cook' && user.cook && !user.cook.is_approved;
                const isPendingDriver = user.role === 'delivery_driver' && user.driver && !user.driver.is_approved;

                if (isPendingCook || isPendingDriver) {
                    actionsHtml += `
                        <button type="button" onclick="closeUserDetailsModal(); openApprovalModal({
                            id: ${user.id},
                            name: '${user.name.replace(/'/g, "\\'")}',
                            role: '${user.role}',
                            roleLabel: '${user.role_label}',
                            address: '${(user.address || '').replace(/'/g, "\\'")}',
                            phone: '${(user.phone || '').replace(/'/g, "\\'")}',
                            email: '${(user.email || '').replace(/'/g, "\\'")}',
                            details: ${JSON.stringify(isPendingCook ? user.cook : user.driver)}
                        })" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                            <span>Revisar / Aprobar Solicitud</span>
                        </button>
                    `;
                }

                if (!user.is_self) {
                    const token = document.querySelector('input[name="_token"]')?.value || '';
                    actionsHtml += `
                        <form action="/admin/users/${user.id}/toggle-status" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${token}">
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition ${user.is_suspended ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-yellow-500 hover:bg-yellow-600 text-white'} shadow-sm">
                                ${user.is_suspended ? 'Activar Cuenta' : 'Suspender Cuenta'}
                            </button>
                        </form>

                        <button type="button" onclick="closeUserDetailsModal(); confirmDelete(${user.id}, '${user.name.replace(/'/g, "\\'")}')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                            Eliminar Usuario
                        </button>
                    `;
                }

                actions.innerHTML = actionsHtml;
                modal.classList.remove('hidden');
            }

            function closeUserDetailsModal() {
                const modal = document.getElementById('userDetailsModal');
                if (modal) modal.classList.add('hidden');
            }
        </script>
    @endpush
@endsection