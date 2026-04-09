<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="bg-white rounded-t-xl border p-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Historial Estudiantil</h1>
            <div class="flex gap-2">
                <a href="{{ route('historial.pdf', $estudiante->id) }}"
                target="_blank"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i>
                    Imprimir PDF
                </a>

                <a href="{{ route('estudiantes') }}"
                class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold border hover:bg-slate-200 transition-colors">
                    Volver
                </a>
            </div>
        </div>
        {{-- <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Historial Estudiantil</h1>
            <a href="{{ route('estudiantes') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold border">Volver</a>
        </div> --}}




        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border">
            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white shadow-md">
                <i class="fa-solid fa-user-graduate text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-black text-slate-800 uppercase">{{ $estudiante->nombre }} {{ $estudiante->apellido }}</h2>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">RUT: {{ $estudiante->rut }} | {{ $estudiante->curso->curso ?? 'Sin Curso' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white border-x flex p-1 gap-2">
        <button wire:click="setTab('intervenciones')"
            class="flex-1 py-3 text-sm font-black uppercase tracking-widest rounded-lg transition-all {{ $activeTab === 'intervenciones' ? 'bg-blue-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:bg-slate-50' }}">
            <i class="fa-solid fa-handshake-angle mr-2"></i> Intervenciones ({{ count($intervenciones) }})
        </button>
        <button wire:click="setTab('derivaciones')"
            class="flex-1 py-3 text-sm font-black uppercase tracking-widest rounded-lg transition-all {{ $activeTab === 'derivaciones' ? 'bg-orange-500 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:bg-slate-50' }}">
            <i class="fa-solid fa-file-export mr-2"></i> Derivaciones ({{ count($derivaciones) }})
        </button>
    </div>

    <div class="bg-white border rounded-b-xl overflow-hidden shadow-sm">

        @if($activeTab === 'intervenciones')
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase w-40">Fecha</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Origen</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Descripción</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase text-center">Docs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($intervenciones as $reg)
                        <tr>
                            <td class="px-6 py-5 align-top">
                                <div class="text-sm font-bold text-slate-700">{{ $reg['fecha'] }}</div>
                                {{-- <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase border {{ $reg['color_estado'] }}">{{ $reg['estado'] }}</span> --}}
                            </td>
                            <td class="px-6 py-5 align-top">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-[10px] font-black uppercase border border-blue-100">{{ $reg['via'] }}</span>
                                <div class="text-sm font-extrabold text-slate-800 mt-2">{{ $reg['profesional'] }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase italic">{{ $reg['area'] }}</div>
                            </td>
                            <td class="px-6 py-5 align-top">
                                <p class="text-sm text-slate-600 italic">"{{ $reg['descripcion'] }}"</p>
                            </td>
                            <td class="px-6 py-5 text-center align-top">
                                @if(count($reg['documentos']) > 0)
                                    <button wire:click="abrirModalArchivos({{ $reg['id'] }}, 'intervencion')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><i class="fa-solid fa-paperclip"></i></button>
                                @endif
                            </td>
                        </tr>
                        @if(count($reg['detalles']) > 0)
                            <tr class="bg-slate-50/50">
                                <td></td>
                                <td colspan="3" class="px-6 py-4">
                                    <div class="space-y-2 border-l-2 border-slate-200 pl-4">
                                        @foreach($reg['detalles'] as $detalle)
                                            <div class="bg-white border rounded-lg p-3 shadow-sm flex flex-col md:flex-row gap-4">
                                                <div class="flex-1">
                                                    <span class="text-[9px] font-black text-slate-400 uppercase block">Tipo/Motivo</span>
                                                    <span class="text-sm font-bold text-slate-700">
                                                        {{ $detalle->motivo->motivo ?? ($detalle->falta->falta ?? 'Registro de Intervención') }}
                                                    </span>
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-[9px] font-black text-slate-400 uppercase block">Tipo Inervención/Medida</span>
                                                    <span class="text-sm font-bold text-slate-700">
                                                        {{ $detalle->tipointervencion->tipo ?? ($detalle->medida->medida ?? 'Registro de Intervención') }}
                                                    </span>
                                                </div>
                                                {{-- <div class="flex-1 bg-slate-50 p-2 rounded">
                                                    <span class="text-[9px] font-black text-slate-400 uppercase block">Observación de Registro:</span>
                                                    <p class="text-xs text-slate-600">{{ $detalle->detalle ?: 'Sin notas.' }}</p>
                                                </div> --}}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="4" class="py-20 text-center text-slate-400 italic">No hay intervenciones.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @else
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase w-40">Fecha</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Motivo</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Derivado Por</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Detalle Derivación</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase text-center">Docs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($derivaciones as $der)
    <tr class="hover:bg-slate-50/30 transition-colors border-t border-slate-100">
        <td class="px-6 py-5">
            <div class="text-sm font-bold text-slate-700 dark:text-zinc-200">{{ $der['fecha'] }}</div>
            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase border {{ $der['color_estado'] }}">{{ $der['estado'] }}</span>
        </td>
        <td class="px-6 py-5 text-sm font-black text-slate-800 dark:text-zinc-100">{{ $der['motivo'] }}</td>
        <td class="px-6 py-5">
            <div class="text-sm font-bold text-slate-700 dark:text-zinc-200">{{ $der['profesional'] }}</div>
            <div class="text-[10px] text-orange-600 font-black uppercase">{{ $der['tipo'] }}</div>
        </td>
        <td class="px-6 py-5 text-sm text-slate-600 dark:text-zinc-400 italic">
            {{ $der['detalle'] }}
        </td>
        <td class="px-6 py-5 text-center">
            @if(count($der['documentos']) > 0)
                <button wire:click="abrirModalArchivos({{ $der['id'] }}, 'derivacion')" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg">
                    <i class="fa-solid fa-paperclip"></i>
                </button>
            @endif
        </td>
    </tr>

    {{-- SUBTABLA DE ACCIONES --}}
    @if(count($der['acciones']) > 0)
        <tr class="bg-orange-50/30 dark:bg-zinc-800/20">
            <td></td>
            <td colspan="4" class="px-6 py-4">
                <div class="border-l-2 border-orange-200 pl-4 space-y-2">
                    <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-2">
                        <i class="fa-solid fa-list-check mr-1"></i> Seguimiento de Acciones
                    </p>
                    <table class="w-full bg-white dark:bg-zinc-900 border border-slate-100 rounded-lg overflow-hidden shadow-sm">
                        <thead class="bg-slate-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase">Fecha</th>
                                <th class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase">Profesional</th>
                                <th class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase text-left">Acción Realizada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-zinc-800">
                            @foreach($der['acciones'] as $accion)
                                <tr>
                                    <td class="px-4 py-2 text-xs font-bold text-slate-600 w-24">
                                        {{ \Carbon\Carbon::parse($accion->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-xs font-bold text-slate-700 dark:text-zinc-300">
                                        {{ $accion->usuario->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 text-xs text-slate-600 dark:text-zinc-400 italic">
                                        {{ $accion->descripcion }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    @endif
@empty
    <tr><td colspan="5" class="py-20 text-center text-slate-400 italic">No hay derivaciones.</td></tr>
@endforelse
                </tbody>
            </table>
        @endif
    </div>

    <flux:modal wire:model="verArchivosModal" variant="wide">
        <div class="p-4 space-y-4">
            <h3 class="text-lg font-bold border-b pb-2">Archivos Adjuntos</h3>
            <div class="grid grid-cols-1 gap-2">
                @foreach($documentosMostrar as $doc)
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg border">
                        <span class="text-sm font-medium">{{ $doc->name }}</span>
                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 text-xs font-bold uppercase underline">Descargar</a>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end pt-4"><flux:button wire:click="$set('verArchivosModal', false)" variant="ghost">Cerrar</flux:button></div>
        </div>
    </flux:modal>
</div>
