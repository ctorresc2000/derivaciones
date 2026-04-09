
<div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400">
            <i class="fa-solid fa-rectangle-list text-lg"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
            Registros de la Intervención
        </h3>
    </div>

    {{-- Iteramos sobre todos los detalles asociados a la intervención --}}
    @forelse($row->detalles as $detalle)
        <div class="mb-6 p-5 border-l-4 border-indigo-500 bg-white dark:bg-zinc-800 rounded-r-md shadow-sm">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">

                {{-- BLOQUE CONVIVENCIA (Si existe falta_id) --}}
                @if(data_get($detalle, 'falta_id'))
                    <div class="bg-slate-50 dark:bg-zinc-900/50 p-3 rounded border border-slate-200 dark:border-zinc-700">
                        <span class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider mb-2 block">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Tipo de Falta
                        </span>
                        <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">
                            {{ data_get($detalle, 'falta.falta') ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-zinc-900/50 p-3 rounded border border-slate-200 dark:border-zinc-700">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                            <i class="fa-solid fa-gavel mr-1"></i> Medida Aplicada
                        </span>
                        <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">
                            {{ data_get($detalle, 'medida.medida') ?? 'Sin medida registrada' }}
                        </p>
                    </div>
                @endif

                {{-- BLOQUE PSICOSOCIAL (Si existe motivo_intervencion_id) --}}
                @if(data_get($detalle, 'motivo_intervencion_id'))
                    <div class="bg-slate-50 dark:bg-zinc-900/50 p-3 rounded border border-slate-200 dark:border-zinc-700">
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2 block">
                            <i class="fa-solid fa-heart-pulse mr-1"></i> Motivo Psicosocial
                        </span>
                        <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">
                            {{ data_get($detalle, 'motivo.motivo') ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-zinc-900/50 p-3 rounded border border-slate-200 dark:border-zinc-700">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                            <i class="fa-solid fa-hand-holding-medical mr-1"></i> Tipo de Intervención
                        </span>
                        <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">
                            {{ data_get($detalle, 'tipo.tipo') ?? 'N/A' }}
                        </p>
                    </div>
                @endif

            </div>

        </div>
    @empty
        {{-- Si por algún motivo no hay detalles --}}
        <div class="flex flex-col items-center justify-center p-8 text-slate-500">
            <i class="fa-solid fa-inbox text-3xl mb-2 opacity-20"></i>
            <p>No se encontraron detalles específicos para esta intervención.</p>
        </div>
     @endforelse
    {{-- CAMPO COMÚN: Observación / Detalle del incidente --}}
    <div class="bg-slate-50 dark:bg-zinc-900/50 p-3 rounded border border-slate-200 dark:border-zinc-700">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
            <i class="fa-solid fa-comment-dots mr-1"></i> Descripción o Relato
        </span>
        <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
            {{ data_get($detalle, 'detalle') ?: 'No se ingresaron observaciones adicionales.' }}
        </p>
    </div>

</div>
