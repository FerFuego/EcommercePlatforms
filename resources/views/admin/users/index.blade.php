@extends('layouts.admin')

@section('title', 'Gestión de Usuarios - Admin')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Gestión de Usuarios</h1>
                <div class="flex space-x-4 text-sm">
                    <span class="text-gray-600">Total: <strong>{{ $stats['total'] }}</strong></span>
                    <span class="text-gray-600">Admins: <strong>{{ $stats['admins'] }}</strong></span>
                    <span class="text-gray-600">Cocineros: <strong>{{ $stats['cooks'] }}</strong></span>
                    <span class="text-gray-600">Repartidores: <strong>{{ $stats['drivers'] }}</strong></span>
                    <span class="text-gray-600">Clientes: <strong>{{ $stats['customers'] }}</strong></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Search Filters -->
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" 
                                value="{{ request('search') }}"
                                placeholder="Nombre, email, dirección, localidad..."
                                class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm">
                        </div>
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
                            <button type="submit" class="bg-blue-600 text-white px-2 py-2 rounded-xl font-semibold hover:bg-blue-700 transition shadow-md">
                                Buscar
                            </button>
                            @if(request()->has('search') || request()->has('date'))
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
                                        <p class="font-bold">{{ $user->name }}</p>
                                        @if($user->role === 'cook' && $user->cook)
                                            <p class="text-xs text-gray-600">
                                                {{ $user->cook->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                            </p>
                                        @elseif($user->role === 'delivery_driver' && $user->deliveryDriver)
                                            <p class="text-xs text-gray-600">
                                                {{ $user->deliveryDriver->is_approved ? '✓ Aprobado' : '⏳ Pendiente' }}
                                            </p>
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
                                @if($user->role === 'cook' && $user->cook && !$user->cook->is_approved)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pendiente</span>
                                @elseif($user->role === 'delivery_driver' && $user->deliveryDriver && !$user->deliveryDriver->is_approved)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pendiente</span>
                                @elseif($user->is_suspended ?? false)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Suspendido</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Activo</span>
                                @endif
                            </td>
                            <td class="px-2 py-4 text-center">
                                <p class="text-sm text-gray-600">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                            </td>
                            <td class="px-2 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Botón Aprobar (Solo para pendientes) --}}
                                    @if(
                                            ($user->role === 'cook' && $user->cook && !$user->cook->is_approved) ||
                                            ($user->role === 'delivery_driver' && $user->deliveryDriver && !$user->deliveryDriver->is_approved)
                                        )
                                        <button onclick="openApprovalModal({
                                                                    id: {{ $user->id }},
                                                                    name: '{{ $user->name }}',
                                                                    role: '{{ $user->role }}',
                                                                    roleLabel: '{{ $user->role === 'cook' ? 'Cocinero' : 'Repartidor' }}',
                                                                    details: {{ json_encode($user->role === 'cook' ? $user->cook : $user->deliveryDriver) }}
                                                                })"
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition"
                                            title="Revisar Solicitud">Revisar
                                        </button>
                                    @endif

                                    {{-- Toggle Suspender/Activar --}}
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 rounded-lg text-xs font-semibold transition {{ ($user->is_suspended ?? false) ? 'bg-green-500 text-white hover:bg-green-700' : 'bg-yellow-500 text-white hover:bg-yellow-600' }}"
                                                title="{{ ($user->is_suspended ?? false) ? 'Activar' : 'Suspender' }}">
                                                {{ ($user->is_suspended ?? false) ? 'Activar' : 'Suspender' }}
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                            class="px-3 py-1 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition"
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
                
                <form id="rejectForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full bg-red-500 text-white px-2 py-4 rounded-xl font-bold hover:bg-red-600 transition flex items-center justify-center gap-2 text-lg shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Rechazar Solicitud
                    </button>
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
                                    <img src="${storageUrl(details.dni_photo)}" class="w-full h-auto object-contain hover:scale-105 transition duration-300" alt="Foto DNI">
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
                     // Location
                     html += `
                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">📍 Ubicación</h4>
                            <p class="mb-1"><span class="font-semibold">Lat:</span> ${details.location_lat}</p>
                            <p class="mb-1"><span class="font-semibold">Lng:</span> ${details.location_lng}</p>
                            <p><span class="font-semibold">Radio:</span> ${details.coverage_radius_km} km</p>
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
                                    <img src="${storageUrl(details.dni_photo)}" class="w-full h-auto object-contain" alt="Foto DNI">
                                </div>
                            </div>
                        `;
                    }
                    html += `</div>`;

                    // Col 2: Vehículo y Foto Perfil (si existe)
                    html += `<div class="space-y-6">`;
                    html += `
                        <div class="bg-gray-50 p-5 rounded-xl border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">🚗 Vehículo</h4>
                            <p class="mb-2"><span class="font-semibold">Tipo:</span> <span class="capitalize">${details.vehicle_type || 'N/A'}</span></p>
                            <p class="mb-2"><span class="font-semibold">Patente:</span> ${details.vehicle_plate || 'N/A'}</p>
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
                modal.classList.remove('hidden');
            }

            function closeApprovalModal() {
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
        </script>
    @endpush
@endsection