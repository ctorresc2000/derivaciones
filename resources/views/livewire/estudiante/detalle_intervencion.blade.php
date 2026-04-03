<div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400">
            <i class="fa-solid fa-hand-holding-heart text-lg"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
            Detalles de la Intervención
        </h3>
    </div>

    {{-- Transformamos de forma segura el array a colección para extraer el primero --}}
    @php
        $detalle = collect($row->detalles)->first();
    @endphp

    @if($detalle)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            {{-- TARJETA: TIPO DE FALTA --}}
            <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                    <i class="fa-solid fa-triangle-exclamation text-slate-400 mr-1"></i> Tipo de Falta
                </span>
                <p class="text-sm text-slate-700 dark:text-slate-300">
                    {{ data_get($detalle, 'falta.falta') ?? data_get($detalle, 'falta') ?? 'No especificada' }}
                </p>
            </div>

            {{-- TARJETA: MEDIDA APLICADA --}}
            <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                    <i class="fa-solid fa-gavel text-slate-400 mr-1"></i> Medida Aplicada
                </span>
                <p class="text-sm text-slate-700 dark:text-slate-300">
                    {{ data_get($detalle, 'medida.medida') ?? data_get($detalle, 'tipo_medida') ?? 'No especificada' }}
                </p>
            </div>
        </div>

        {{-- TARJETA: OBSERVACIONES --}}
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Observaciones del Incidente
            </span>
            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
                {{ data_get($detalle, 'descripcion') ?? data_get($row, 'descripcion') ?? 'Sin observaciones registradas.' }}
            </p>
        </div>
    @else
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700 text-center">
            <p class="text-sm text-slate-500">
                <i class="fa-solid fa-circle-info mr-2"></i> No se encontraron detalles adicionales para esta intervención.
            </p>
        </div>
    @endif

</div>
