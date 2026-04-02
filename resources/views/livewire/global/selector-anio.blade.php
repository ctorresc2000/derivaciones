<div class="flex items-center gap-2">
    <span class="text-xl font-semibold text-gray-500 dark:text-gray-400">Año Activo:</span>

    <select wire:model.live="anioSeleccionado" class="text-xl rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
        @foreach($anios as $anio)
            <option value="{{ $anio }}">{{ $anio }}</option>
        @endforeach
    </select>
</div>
