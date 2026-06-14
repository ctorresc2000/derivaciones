<div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
            <i class="fa-solid fa-address-card text-lg"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
            Estudiantes asignados
        </h3>
    </div>

    {{-- 1. CAMBIO AQUÍ: Usamos la función count() nativa de PHP --}}
    @if(count($row->estudiantes) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            @foreach($row->estudiantes as $estudiante)
                <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-4 rounded-md shadow-sm border border-slate-200 dark:border-zinc-700">
                    <div>
                        <p class="font-bold text-sm text-slate-800 dark:text-white">
                            <i class="fa-solid fa-user-graduate text-slate-400 mr-1"></i>
                            {{-- 2. CAMBIO AQUÍ: Usamos corchetes ['nombre'] en vez de flechas ->nombre --}}
                            {{ $estudiante['nombre'] }} {{ $estudiante['apellido'] }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
                            <strong>RUT:</strong> {{ $estudiante['rut'] }}
                        </p>
                    </div>

                    {{-- 3. CAMBIO AQUÍ: Usamos $estudiante['id'] --}}
                    <button
                        wire:click="desvincularEstudiante({{ $row->id }}, {{ $estudiante['id'] }})"
                        class="flex items-center gap-2 px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 rounded-md text-xs font-semibold transition-colors"
                        title="Quitar estudiante de este apoderado"
                    >
                        <i class="fa-solid fa-user-minus"></i> Quitar
                    </button>
                </div>
            @endforeach

        </div>
    @else
        <div class="text-center py-6 bg-white dark:bg-zinc-800 rounded-md border border-dashed border-slate-300 dark:border-zinc-700">
            <i class="fa-solid fa-user-slash text-slate-400 text-3xl mb-2"></i>
            <p class="text-sm text-slate-500 dark:text-zinc-400">Este apoderado aún no tiene estudiantes asignados.</p>
        </div>
    @endif

</div>
