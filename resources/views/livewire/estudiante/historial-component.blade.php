<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <div class="bg-white dark:bg-zinc-900 rounded-t-lg shadow-sm border border-slate-200 dark:border-zinc-800 p-6 mb-0">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-zinc-500"></i> Historial del Estudiante
                </h1>

                <div class="flex gap-2">
                    <a href="{{ route('estudiantes') }}" class="px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white text-sm font-semibold rounded-md shadow-sm transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Volver
                    </a>
                    <a href="{{ route('historial.pdf', $estudiante->id) }}" target="_blank" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-md shadow-sm transition-colors cursor-pointer inline-flex items-center">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Ver PDF
                    </a>
                </div>
            </div>

            {{-- TARJETA DE INFORMACIÓN DEL ESTUDIANTE CON BOTÓN DE ADJUNTOS --}}
            <div class="bg-slate-50 dark:bg-zinc-800/50 p-4 rounded-lg border border-slate-100 dark:border-zinc-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-graduate text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $estudiante->nombre }} {{ $estudiante->apellido }}
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">
                            RUT: {{ $estudiante->rut }} | Curso: {{ $estudiante->curso->nombre_curso ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                {{-- Sección de Documentos Adjuntos del Estudiante --}}
                <div class="flex-shrink-0">
                    @if($estudiante->documents && $estudiante->documents->count() > 0)
                        <button wire:click="mostrarArchivos({{ $estudiante->id }}, 'estudiante')"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-800 shadow-sm cursor-pointer">
                            <i class="fa-solid fa-paperclip text-lg"></i>
                            Ver Documentos Base ({{ $estudiante->documents->count() }})
                        </button>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-500 bg-slate-50 rounded-lg dark:bg-zinc-800/50 dark:text-slate-400 border border-slate-200 dark:border-zinc-700">
                            <i class="fa-solid fa-file-circle-xmark text-lg"></i>
                            Sin documentos base
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-b-lg shadow-sm border-x border-b border-slate-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-800">
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider w-32">Fecha / Hora</th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tipo / Estado</th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Profesional Responsable</th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        @forelse($historial as $item)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="p-5 align-top">
                                    <div class="text-sm font-bold text-slate-700 dark:text-zinc-200">
                                        {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-1">
                                        <i class="fa-regular fa-clock mr-1"></i> {{ $item->hora }} hrs
                                    </div>
                                </td>

                                <td class="p-5 align-top w-1/3">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner {{ $item->color }}">
                                            <i class="fa-solid {{ $item->icono }} text-xl"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 dark:text-white text-[15px] leading-tight mb-1">
                                                {{ $item->tipo_registro }}
                                            </span>

                                            {{-- BOTÓN DE ARCHIVOS ADJUNTOS --}}
                                            @if($item->cantidad_documentos > 0)
                                                <button wire:click="mostrarArchivos({{ $item->id }}, '{{ $item->modelo_tipo }}')" class="text-left mt-1 mb-2 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors font-medium flex items-center gap-1 cursor-pointer">
                                                    <i class="fa-solid fa-paperclip"></i> Ver {{ $item->cantidad_documentos }} adjunto(s)
                                                </button>
                                            @endif

                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border shadow-sm {{ $item->color_estado }}">
                                                    {{ $item->estado }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-5 align-top">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-zinc-300">
                                            {{ substr($item->profesional, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-600 dark:text-zinc-300">
                                            {{ $item->profesional }}
                                        </span>
                                    </div>
                                </td>

                                <td class="p-5 text-sm align-top">
                                    <div class="bg-slate-50 dark:bg-zinc-800/80 p-3.5 rounded-lg border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-slate-300 whitespace-pre-line shadow-sm">
                                        {{ $item->detalle }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="w-16 h-16 bg-slate-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-folder-open text-2xl text-slate-400 dark:text-zinc-500"></i>
                                    </div>
                                    <p class="text-lg font-medium text-slate-700 dark:text-slate-300">Sin registros</p>
                                    <p class="text-sm mt-1">Aún no hay intervenciones o derivaciones para este estudiante.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL PARA VER DOCUMENTOS --}}
    <flux:modal wire:model="verArchivosModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $tituloModalArchivos }}</flux:heading>
                <flux:subheading>Puedes abrir los archivos en una pestaña nueva o descargarlos.</flux:subheading>
            </div>

            <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                @forelse($documentosMostrar as $doc)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-zinc-800/50 rounded-lg border border-slate-200 dark:border-zinc-700">
                        <div class="flex items-center gap-3 overflow-hidden">
                            @if(str_contains($doc->mime_type ?? '', 'pdf'))
                                <i class="fa-solid fa-file-pdf text-red-500 text-2xl flex-shrink-0"></i>
                            @elseif(str_contains($doc->mime_type ?? '', 'image'))
                                <i class="fa-solid fa-file-image text-emerald-500 text-2xl flex-shrink-0"></i>
                            @else
                                <i class="fa-solid fa-file-lines text-slate-400 text-2xl flex-shrink-0"></i>
                            @endif

                            <div class="flex flex-col overflow-hidden">
                                <a href="{{ asset('storage/' . ($doc->file_path ?? $doc->ruta ?? $doc->ruta_archivo)) }}"
                                   target="_blank"
                                   class="text-sm font-medium hover:underline text-blue-600 dark:text-blue-400 truncate"
                                   title="{{ $doc->name ?? $doc->nombre_original }}">
                                    {{ $doc->name ?? $doc->nombre_original }}
                                </a>
                                <span class="text-xs text-slate-500">{{ number_format(($doc->size ?? 0) / 1024, 1) }} KB</span>
                            </div>
                        </div>

                        <flux:button
                            as="a"
                            href="{{ asset('storage/' . ($doc->file_path ?? $doc->ruta ?? $doc->ruta_archivo)) }}"
                            download="{{ $doc->name ?? $doc->nombre_original }}"
                            icon="arrow-down-tray"
                            variant="ghost"
                            size="sm"
                        />
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fa-solid fa-circle-info text-slate-300 text-3xl mb-2"></i>
                        <p class="text-sm text-slate-500">No se encontraron los archivos físicos.</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-zinc-800">
                <flux:button wire:click="$set('verArchivosModal', false)" variant="ghost">Cerrar ventana</flux:button>
            </div>
        </div>
    </flux:modal>
</div>


