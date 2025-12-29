<div class="group-container bg-gray-50 rounded-2xl p-6 border-2 border-gray-100" id="group-{{ $groupId }}">
    <div class="flex justify-between items-start mb-4">
        <div class="flex-1 mr-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Grupo</label>
            <input type="text" name="option_groups[{{ $groupId }}][name]" value="{{ $group->name }}" required
                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-purple-500 transition">
        </div>
        <button type="button" onclick="removeElement('group-{{ $groupId }}')"
            class="text-red-500 hover:text-red-700 p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Mín. Opciones</label>
            <input type="number" name="option_groups[{{ $groupId }}][min_options]" value="{{ $group->min_options }}"
                min="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Máx. Opciones</label>
            <input type="number" name="option_groups[{{ $groupId }}][max_options]" value="{{ $group->max_options }}"
                min="1" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
        </div>
        <div class="flex items-center pt-5">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="option_groups[{{ $groupId }}][is_required]" value="1" {{ $group->is_required ? 'checked' : '' }} class="w-4 h-4 text-purple-600 rounded">
                <span class="ml-2 text-xs font-semibold text-gray-600">Es Obligatorio</span>
            </label>
        </div>
    </div>

    <div class="ml-4 pl-4 border-l-2 border-purple-100">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-bold text-gray-700">Opciones Individuales</h4>
            <button type="button" onclick="addOption({{ $groupId }})"
                class="text-xs bg-white text-purple-600 px-3 py-1 rounded-lg border border-purple-200 hover:bg-purple-50 transition">
                + Agregar Opción
            </button>
        </div>
        <div id="options-container-{{ $groupId }}" class="space-y-2">
            @foreach($group->options as $option)
                @php $optionId = $groupId . '_' . $loop->index; @endphp
                <div class="flex items-center gap-3" id="option-{{ $optionId }}">
                    <div class="flex-1">
                        <input type="text" name="option_groups[{{ $groupId }}][options][{{ $loop->index }}][name]"
                            value="{{ $option->name }}" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div class="w-32">
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-gray-400 text-xs">$</span>
                            <input type="number"
                                name="option_groups[{{ $groupId }}][options][{{ $loop->index }}][additional_price]"
                                value="{{ $option->additional_price }}" step="0.01" min="0"
                                class="w-full pl-5 pr-2 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                    <button type="button" onclick="removeElement('option-{{ $optionId }}')"
                        class="text-red-400 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>