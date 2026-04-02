  <div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

        <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                <i class="fa-solid fa-address-card text-lg"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                Información Adicional
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-6">

            <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                    <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Antecedentes Previos
                </span>
                <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
                    {{ $row->previos_derivacion ?: 'El estudiante no tiene observaciones registradas actualmente.' }}
                </p>
            </div>

            <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                    <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Detalle Derivacion
                </span>
                <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
                    {{ $row->detalle_derivacion ?: 'El estudiante no tiene observaciones registradas actualmente.' }}
                </p>
            </div>
        </div>
        <div x-data="{ textoConclusion: @js($row->conclusiones ?? '') }">
            <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                        <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Conclusiones de la Derivación
                    </span>
                    <flux:textarea x-model="textoConclusion" placeholder="Escribe aquí..." />
            </div>

            <hr class="mt-3 mb-3">

            <div class="flex justify-end px-6">
                <flux:button wire:click="$dispatch('alternarDetalle', { id: {{ $row->id }} })" class="ml-3" variant="primary" color="yellow">
                    <i class="fa-solid fa-xmark"></i><span class="ml-3">Cerrar</span>
                </flux:button>

                <flux:button variant="primary" x-on:click="$dispatch('guardarConclusion', { id: {{ $row->id }}, texto: textoConclusion })" class="ml-4">
                    <i class="fa-solid fa-floppy-disk"></i><span class="ml-3">Guardar</span>
                </flux:button>
            </div>
        </div>
    </div>
