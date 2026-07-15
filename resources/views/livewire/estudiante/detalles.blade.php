
    <div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

        <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                <i class="fa-solid fa-address-card text-lg"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                Información Adicional
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-envelope mr-1"></i> Email
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $row->email ?: 'No registrado' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-phone mr-1"></i> Teléfono
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $row->telefono ?: 'No registrado' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-cake-candles mr-1"></i>Fecha Nacimiento
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $row->fecha_nacimiento ? \Carbon\Carbon::parse($row->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-location-dot mr-1"></i> Dirección
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $row->domicilio ?: 'No registrada' }}
                </span>
            </div>

        </div>

        <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Observaciones
            </span>
            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
                {{ $row->observaciones ?: 'El estudiante no tiene observaciones registradas actualmente.' }}
            </p>
        </div>

        <hr class="my-4 border-slate-200 dark:border-zinc-700">

        {{-- NUEVA BARRA DE ACCIONES --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mr-2">
                Acciones:
            </span>

            {{-- Botón Editar (Azul) --}}
            <button wire:click="$dispatch('editEstudiante', { rowId: {{ $row->id }} })"
                    class="flex items-center justify-center p-2 rounded bg-blue-500 text-white hover:bg-blue-600 transition-colors tooltip" title="Editar Estudiante">
                <i class="fa-solid fa-pen-to-square mr-2"></i><strong> Editar Estudiante</strong>
            </button>

            {{-- Botón Derivar (Verde) --}}
            <a href="{{route('derivaciones', ['id' => $row->id])}}"
                    class="flex items-center justify-center p-2 rounded bg-emerald-500 text-white hover:bg-emerald-600 transition-colors" title="Intervenir por Convivencia">
                <i class="fa-solid fa-scale-balanced mr-2"></i> Intervenir por Convivencia
            </a>

            <a href="{{route('intervencionpsicosocial', ['id' => $row->id])}}"
                    class="flex items-center justify-center p-2 rounded bg-teal-500 text-white hover:bg-teal-600 transition-colors" title="Intervenir por Psicosocial">
                <i class="fa-solid fa-hand-holding-heart"></i> <span class="ml-2">Intervenir por Psicosocial</span>
            </a>

            {{-- Botón Redes de Apoyo (Morado) --}}
            <button wire:click="$dispatch('abrirModalRedes', { estudianteId: {{ $row->id }} })"
                class="flex items-center justify-center p-2 rounded bg-purple-500 text-white hover:bg-purple-600 transition-colors" title="Redes de Apoyo">
                <i class="fa-solid fa-house-medical"></i> <span class="ml-2">Redes de Apoyo</span>
            </button>
            <button wire:click="$dispatch('abrirModalDerivacion', {rowId: {{ $row->id }} })"
                class="flex items-center justify-center p-2 rounded bg-pink-500 text-white hover:bg-pink-600 transition-colors" title="Derivar Estudiante">
                <i class="fa-solid fa-file-export"></i> <span class="ml-2">Derivar Estudiante</span>
            </button>
            <a href="{{route('estudiante.historial', ['id' => $row->id])}}"
                    class="flex items-center justify-center p-2 rounded bg-zinc-500 text-white hover:bg-zinc-600 transition-colors" title="Historial Estudiante">
                '<i class="fa-solid fa-clock-rotate-left"></i> <span class="ml-2">Ver Historial Estudiante</span>
            </a>


        </div>

    </div>



