<div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
            <i class="fa-solid fa-address-card text-lg"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
            Detalle de la Entrevista
        </h3>
    </div>

    {{-- BLOQUE DE DETALLE --}}
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700 mb-4">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
            <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Detalle
        </span>
        <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
            {{ $row->detalle ?: 'No hay detalles disponibles para esta entrevista.' }}
        </p>
    </div>

    {{-- BLOQUE DE FIRMA Y DOCUMENTOS (EN DOS COLUMNAS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- COLUMNA 1: Firma --}}
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700 flex flex-col items-center justify-center min-h-[140px]">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 self-start w-full">
                <i class="fa-solid fa-signature text-slate-400 mr-1"></i> Firma del Entrevistado
            </span>

            @if($row->firma)
                <div class="bg-slate-50 rounded p-2 border border-slate-100 flex-grow flex items-center justify-center w-full">
                    <img src="{{ $row->firma }}" alt="Firma digital" class="max-h-24 w-auto">
                </div>
            @else
                <p class="text-xs text-slate-400 italic flex-grow flex items-center">Sin firma registrada</p>
            @endif
        </div>

        {{-- COLUMNA 2: Documentos Adjuntos --}}
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700 flex flex-col min-h-[140px]">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block w-full">
                <i class="fa-solid fa-paperclip text-indigo-500 mr-1"></i> Documentos y Evidencias
            </span>

            {{-- LO NUEVO: Buscamos la entrevista real en la BD para recuperar sus relaciones --}}
            @php
                $entrevistaReal = \App\Models\Entrevista::find($row->id);
            @endphp

            @if($entrevistaReal && $entrevistaReal->documents->count() > 0)
                <ul class="space-y-2 overflow-y-auto max-h-28 pr-2 flex-grow">
                    @foreach($entrevistaReal->documents as $doc)
                        <li class="flex items-center justify-between p-2 bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700 rounded-lg hover:shadow-md transition-shadow">

                            <div class="flex items-center overflow-hidden">
                                @if(str_contains($doc->mime_type, 'pdf'))
                                    <i class="fa-solid fa-file-pdf text-red-500 text-lg mr-2"></i>
                                @else
                                    <i class="fa-solid fa-image text-emerald-500 text-lg mr-2"></i>
                                @endif

                                <div class="flex flex-col truncate">
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate" title="{{ $doc->name }}">
                                        {{ $doc->name }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ number_format($doc->size / 1024, 2) }} KB
                                    </span>
                                </div>
                            </div>

                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                               class="ml-2 flex-shrink-0 flex items-center justify-center w-7 h-7 rounded bg-indigo-100 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors" title="Ver Documento">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-3 bg-slate-50 dark:bg-zinc-800/50 rounded border border-dashed border-slate-300 dark:border-zinc-700 text-center flex-grow flex items-center justify-center">
                    <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                        Sin documentos adjuntos.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- BLOQUE DE VALIDACIÓN OTP --}}
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

</div>
