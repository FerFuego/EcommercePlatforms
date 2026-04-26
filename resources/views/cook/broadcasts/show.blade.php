@extends('layouts.app')

@section('title', 'Campaña WhatsApp')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('cook.broadcasts.index') }}" class="text-gray-500 hover:text-gray-800 flex items-center font-semibold">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Volver
        </a>
        @if($broadcast->status === 'completed')
            <span class="bg-green-100 text-green-700 font-bold px-4 py-1 rounded-full border border-green-200">Campaña Completada 🎉</span>
        @else
            <span class="bg-blue-100 text-blue-700 font-bold px-4 py-1 rounded-full border border-blue-200" id="campaignStatus">En progreso</span>
        @endif
    </div>

    <!-- Header Stats -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Asistente de Envío Masivo</h1>
        <p class="text-gray-600 mb-6">El sistema abrirá WhatsApp Web automáticamente para cada cliente. Tú solo debes enviarlo.</p>
        
        <div class="flex items-center justify-center space-x-12">
            <div>
                <p class="text-sm text-gray-500 font-semibold uppercase">Total Clientes</p>
                <p class="text-4xl font-bold text-gray-800">{{ $broadcast->recipients->count() }}</p>
            </div>
            <div class="w-px h-12 bg-gray-200"></div>
            <div>
                <p class="text-sm text-gray-500 font-semibold uppercase">Enviados</p>
                <p class="text-4xl font-bold text-green-500" id="sentCountDisplay">{{ $broadcast->sent_count }}</p>
            </div>
        </div>
    </div>

    <!-- The Message -->
    <div class="bg-green-50 rounded-2xl border border-green-100 p-6 mb-8 relative">
        <div class="absolute -top-4 -left-4 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center shadow-lg text-white">💬</div>
        <h3 class="text-sm font-bold text-green-800 mb-2 uppercase tracking-wider ml-6">El Mensaje que se enviará:</h3>
        <p class="text-gray-800 font-medium whitespace-pre-wrap ml-6">{{ $broadcast->message }}</p>
    </div>

    @if($broadcast->status !== 'completed')
        <!-- Controller / Runner -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-8 text-center bg-gradient-to-br from-gray-50 to-gray-100">
                <div id="runnerControls">
                    <button id="startBtn" class="bg-green-500 hover:bg-green-600 text-white text-xl font-bold py-4 px-12 rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center mx-auto">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                        INICIAR ENVÍO
                    </button>
                    <p class="text-sm text-gray-500 mt-4 max-w-md mx-auto">Importante: Asegúrate de tener <a href="https://web.whatsapp.com" target="_blank" class="text-green-600 font-bold hover:underline">WhatsApp Web</a> abierto en otra pestaña o la App instalada en tu computadora/celular.</p>
                </div>

                <div id="runnerActive" class="hidden">
                    <div class="w-16 h-16 border-4 border-green-200 border-t-green-500 rounded-full animate-spin mx-auto mb-4"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Enviando mensaje a <span id="currentCustomerName" class="text-green-600">Cliente</span>...</h3>
                    <p class="text-gray-600 mb-6">Regresa a esta pestaña después de apretar "Enviar" en WhatsApp.</p>
                    <button id="pauseBtn" class="text-gray-500 hover:text-red-500 font-bold transition-colors">
                        ⏸️ Pausar Envío
                    </button>
                </div>
            </div>
            
            <div class="bg-gray-50 border-t border-gray-100 p-4">
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div id="progressBar" class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ ($broadcast->sent_count / max($broadcast->recipients->count(), 1)) * 100 }}%"></div>
                </div>
                <p class="text-xs text-center text-gray-500"><span id="progressText">{{ $broadcast->sent_count }} / {{ $broadcast->recipients->count() }}</span> completados</p>
            </div>
        </div>
    @endif

    <!-- Recipients List -->
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Lista de Clientes ({{ $broadcast->recipients->count() }})</h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <ul class="divide-y divide-gray-100" id="recipientsList">
                @foreach($broadcast->recipients as $index => $recipient)
                    <li class="p-4 flex items-center justify-between" id="recipient-{{ $recipient->id }}" data-id="{{ $recipient->id }}" data-phone="{{ $recipient->phone }}" data-name="{{ $recipient->name }}" data-status="{{ $recipient->status }}">
                        <div class="flex items-center">
                            <span class="w-8 text-center text-gray-400 font-bold">{{ $index + 1 }}</span>
                            <div class="ml-4">
                                <p class="font-bold text-gray-800">{{ $recipient->name ?? 'Cliente Anónimo' }}</p>
                                <p class="text-xs text-gray-500">{{ $recipient->phone }}</p>
                            </div>
                        </div>
                        <div class="status-badge">
                            @if($recipient->status === 'sent')
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Enviado
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded-full">Pendiente</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const broadcastId = {{ $broadcast->id }};
    const rawMessage = {!! json_encode($broadcast->message) !!};
    let isRunning = false;
    let recipients = Array.from(document.querySelectorAll('#recipientsList li')).map(li => ({
        id: li.dataset.id,
        phone: li.dataset.phone,
        name: li.dataset.name,
        status: li.dataset.status
    }));
    
    let currentIndex = recipients.findIndex(r => r.status === 'pending');

    const startBtn = document.getElementById('startBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const controls = document.getElementById('runnerControls');
    const activeState = document.getElementById('runnerActive');
    const currentName = document.getElementById('currentCustomerName');

    if(startBtn) {
        startBtn.addEventListener('click', () => {
            if(currentIndex === -1) {
                alert("Todos los mensajes ya fueron enviados.");
                return;
            }
            isRunning = true;
            controls.classList.add('hidden');
            activeState.classList.remove('hidden');
            processNext();
        });
    }

    if(pauseBtn) {
        pauseBtn.addEventListener('click', () => {
            isRunning = false;
            controls.classList.remove('hidden');
            activeState.classList.add('hidden');
        });
    }

    // Detect when user comes back to the tab
    document.addEventListener("visibilitychange", function() {
        if (document.visibilityState === 'visible' && isRunning) {
            // User came back from WhatsApp, mark current as sent but ask them to click next
            markAsSentAndWait();
        }
    });

    function processNext() {
        if (!isRunning) return;
        
        if (currentIndex >= recipients.length || currentIndex === -1) {
            finishCampaign();
            return;
        }

        const current = recipients[currentIndex];
        
        if (current.status === 'sent') {
            currentIndex++;
            processNext();
            return;
        }

        currentName.textContent = current.name || 'Cliente';
        
        // Open WhatsApp
        let text = rawMessage.replace('{name}', current.name || '');
        let encodedText = encodeURIComponent(text);
        let url = `https://api.whatsapp.com/send?phone=${current.phone}&text=${encodedText}`;
        
        // Hide the pause button, we will show a "Siguiente" button when they return
        if (pauseBtn) pauseBtn.classList.add('hidden');
        
        window.open(url, '_blank');
        // Now we wait for visibilitychange event (user returning)
    }

    function markAsSentAndWait() {
        const current = recipients[currentIndex];
        
        // Optimistic UI update
        updateRecipientUI(current.id);
        
        // API Call
        fetch(`/cook/broadcasts/${broadcastId}/mark-sent/${current.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        current.status = 'sent';
        currentIndex++;
        
        if (currentIndex >= recipients.length) {
            finishCampaign();
        } else {
            // Wait for user to click next to avoid popup blockers
            isRunning = false;
            activeState.classList.add('hidden');
            controls.classList.remove('hidden');
            
            startBtn.innerHTML = `<svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg> ABRIR SIGUIENTE CLIENTE`;
            startBtn.classList.replace('bg-green-500', 'bg-blue-500');
            startBtn.classList.replace('hover:bg-green-600', 'hover:bg-blue-600');
        }
    }

    function updateRecipientUI(id) {
        const li = document.getElementById(`recipient-${id}`);
        if(li) {
            const badgeContainer = li.querySelector('.status-badge');
            badgeContainer.innerHTML = `<span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Enviado
                                </span>`;
        }

        // Update counters
        const sentCountEl = document.getElementById('sentCountDisplay');
        let newCount = parseInt(sentCountEl.textContent) + 1;
        sentCountEl.textContent = newCount;
        
        const total = recipients.length;
        document.getElementById('progressText').textContent = `${newCount} / ${total}`;
        document.getElementById('progressBar').style.width = `${(newCount / total) * 100}%`;
    }

    function finishCampaign() {
        isRunning = false;
        controls.classList.add('hidden');
        activeState.classList.add('hidden');
        document.getElementById('campaignStatus').textContent = "Campaña Completada 🎉";
        document.getElementById('campaignStatus').className = "bg-green-100 text-green-700 font-bold px-4 py-1 rounded-full border border-green-200";
        alert("¡Campaña finalizada con éxito!");
        window.location.reload();
    }
</script>
@endpush
@endsection
