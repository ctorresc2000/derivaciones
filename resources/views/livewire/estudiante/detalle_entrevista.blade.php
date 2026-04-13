<div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
            <i class="fa-solid fa-address-card text-lg"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
            Detalle de la Entrevista
        </h3>
    </div>


    <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
            <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Detalle
        </span>
        <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
            {{ $row->detalle ?: 'No hay detalles disponibles para esta entrevista.' }}
        </p>
    </div>

    <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700 flex flex-col items-center justify-center">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 self-start">
            <i class="fa-solid fa-signature text-slate-400 mr-1"></i> Firma del Entrevistado
        </span>

        @if($row->firma)
            <div class="bg-slate-50 rounded p-2 border border-slate-100">
                <img src="{{ $row->firma }}" alt="Firma digital" class="max-h-32 w-auto">
            </div>
        @else
            <p class="text-xs text-slate-400 italic">Sin firma registrada</p>
        @endif
    </div>

</div>

{{-- En resources/views/livewire/estudiante/detalle_entrevista.blade.php --}}
<div class="mt-4 p-4 border rounded-lg bg-gray-50 dark:bg-slate-800">
    <h4 class="text-sm font-bold uppercase text-slate-500 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-shield-halved"></i> Respaldo de Verificación Digital
    </h4>

    @if($row->otp_verified_at)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <span class="text-xs text-slate-400">Correo de Validación</span>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    <i class="fa-solid fa-envelope mr-1 text-blue-500"></i> {{ $row->otp_email }}
                </span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-slate-400">Código Autorizado</span>
                <span class="text-sm font-mono font-bold text-blue-600 dark:text-blue-400">
                    {{ $row->otp_codigo }}
                </span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-slate-400">Fecha y Hora de Validación</span>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    <i class="fa-solid fa-clock mr-1 text-slate-400"></i>
                    {{ \Carbon\Carbon::parse($row->otp_verified_at)->format('d/m/Y H:i:s') }}
                </span>
            </div>
        </div>
        <div class="mt-3 py-1 px-2 bg-green-100 text-green-700 text-[10px] rounded inline-block font-bold">
            <i class="fa-solid fa-circle-check"></i> AUTORIZACIÓN ELECTRÓNICA EXITOSA
        </div>
    @else
        <p class="text-sm text-slate-400 italic">No se realizó validación por correo para esta entrevista.</p>
    @endif
</div>
