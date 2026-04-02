
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

    </div>



