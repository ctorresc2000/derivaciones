<div class="max-w-7xl mx-auto py-8 px-4">
    {{-- HEADER RESPONSIVE --}}
    <div class="bg-white rounded-t-xl border p-4 md:p-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">Historial Estudiantil</h1>
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ route('historial.pdf', $estudiante->id) }}" target="_blank"
                class="flex-1 md:flex-none px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('estudiantes') }}"
                class="flex-1 md:flex-none px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold border hover:bg-slate-200 text-center">
                    Volver
                </a>
            </div>
        </div>

        {{-- INFO ESTUDIANTE RESPONSIVE --}}
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4 p-4 bg-slate-50 rounded-xl border">
            <div class="flex items-center gap-4 w-full">
                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fa-solid fa-user-graduate text-xl"></i>
                </div>
                <div>
                    <h2 class="text-base md:text-lg font-black text-slate-800 uppercase leading-tight">{{ $estudiante->nombre }} {{ $estudiante->apellido }}</h2>
                    <p class="text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest">RUT: {{ $estudiante->rut }} | {{ $estudiante->curso->curso ?? 'Sin Curso' }}</p>
                </div>
            </div>

            <div class="flex flex-col items-start lg:items-end gap-2 w-full">
                @if($estudiante->documents && count($estudiante->documents) > 0)
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Archivos Base:</span>
                    <div class="flex flex-wrap lg:justify-end gap-2">
                        @foreach($estudiante->documents as $docBase)
                            <a href="{{ asset('storage/'.$docBase->file_path) }}" target="_blank"
                               class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-md text-[10px] font-bold text-slate-600 hover:bg-slate-50 shadow-sm">
                                <i class="fa-solid fa-file-pdf text-red-500"></i>
                                {{ Str::limit($docBase->name, 15) }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TABS RESPONSIVE --}}
    <div class="bg-white border-x flex p-1 gap-1 md:gap-2">
        <button wire:click="setTab('intervenciones')"
            class="flex-1 py-3 text-[10px] md:text-sm font-black uppercase tracking-tighter md:tracking-widest rounded-lg transition-all {{ $activeTab === 'intervenciones' ? 'bg-blue-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:bg-slate-50' }}">
            <i class="fa-solid fa-handshake-angle md:mr-2"></i> <span class="hidden md:inline">Intervenciones</span> ({{ count($intervenciones) }})
        </button>
        <button wire:click="setTab('derivaciones')"
            class="flex-1 py-3 text-[10px] md:text-sm font-black uppercase tracking-tighter md:tracking-widest rounded-lg transition-all {{ $activeTab === 'derivaciones' ? 'bg-orange-500 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:bg-slate-50' }}">
            <i class="fa-solid fa-file-export md:mr-2"></i> <span class="hidden md:inline">Derivaciones</span> ({{ count($derivaciones) }})
        </button>
        <button wire:click="setTab('redes')"
            class="flex-1 py-3 text-[10px] md:text-sm font-black uppercase tracking-tighter md:tracking-widest rounded-lg transition-all {{ $activeTab === 'redes' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:bg-slate-50' }}">
            <i class="fa-solid fa-house-chimney-medical md:mr-2"></i>
            <span class="hidden md:inline">Redes de Apoyo</span> ({{ $estudiante->redes->count() }})
        </button>
    </div>

    {{-- CONTENEDOR DE TABLAS CON SCROLL HORIZONTAL --}}
    <div class="bg-white border rounded-b-xl overflow-x-auto shadow-sm">
        @if($activeTab === 'intervenciones')
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-4 md:px-6 py-4 text-xs font-black text-slate-500 uppercase">Fecha</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-black text-slate-500 uppercase">Origen/Prof.</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-black text-slate-500 uppercase">Descripción</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-black text-slate-500 uppercase text-center">Docs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($intervenciones as $reg)
                        <tr>
                            <td class="px-4 md:px-6 py-5 align-top text-sm font-bold text-slate-700">{{ $reg['fecha'] }}</td>
                            <td class="px-4 md:px-6 py-5 align-top">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-[9px] font-black uppercase border border-blue-100">{{ $reg['via'] }}</span>
                                <div class="text-sm font-extrabold text-slate-800 mt-2">{{ $reg['profesional'] }}</div>
                                <div class="text-[9px] text-slate-400 font-bold uppercase italic">{{ $reg['area'] }}</div>
                            </td>
                            <td class="px-4 md:px-6 py-5 align-top">
                                <p class="text-sm text-slate-600 italic leading-relaxed">"{{ $reg['descripcion'] }}"</p>
                            </td>
                            <td class="px-4 md:px-6 py-5 text-center align-top">
                                <button wire:click="abrirModalArchivos({{ $reg['id'] }}, 'intervencion')"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg border border-blue-100 flex flex-col items-center mx-auto">
                                    <i class="fa-solid fa-paperclip text-lg"></i>
                                    <span class="text-[9px] font-bold uppercase mt-1">{{ count($reg['documentos'] ?? []) }} Docs</span>
                                </button>
                            </td>
                        </tr>
                        @if(count($reg['detalles']) > 0)
                            <tr class="bg-slate-50/50">
                                <td colspan="4" class="px-4 md:px-6 py-4">
                                    <div class="space-y-2 border-l-2 border-slate-200 pl-4">
                                        @foreach($reg['detalles'] as $detalle)
                                            <div class="bg-white border rounded-lg p-3 shadow-sm flex flex-col sm:flex-row gap-4">
                                                <div class="flex-1">
                                                    <span class="text-[9px] font-black text-slate-400 uppercase block">Tipo/Motivo</span>
                                                    <span class="text-xs md:text-sm font-bold text-slate-700">{{ $detalle->motivo->motivo ?? ($detalle->falta->falta ?? 'Registro') }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-[9px] font-black text-slate-400 uppercase block">Medida</span>
                                                    <span class="text-xs md:text-sm font-bold text-slate-700">{{ $detalle->tipointervencion->tipo ?? ($detalle->medida->medida ?? 'N/A') }}</span>
                                                </div>
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

        @endif
        @if($activeTab === 'derivaciones')
            {{-- TABLA DERIVACIONES CORREGIDA --}}
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Fecha/Estado</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Motivo</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Derivado Por</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase w-1/3">Detalle</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase text-center">Docs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($derivaciones as $der)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-slate-700">{{ $der['fecha'] }}</div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border {{ $der['color_estado'] }}">{{ $der['estado'] }}</span>
                            </td>
                            <td class="px-6 py-5 text-sm font-black text-slate-800">{{ $der['motivo'] }}</td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-slate-700">{{ $der['profesional'] }}</div>
                                <div class="text-[10px] text-orange-600 font-black uppercase">{{ $der['tipo'] }}</div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-600 italic leading-snug">{{ $der['detalle'] }}</td>
                            <td class="px-6 py-5 text-center">
                                @if(isset($der['documentos']) && count($der['documentos']) > 0)
                                    <button wire:click="abrirModalArchivos({{ $der['id'] }}, 'derivacion')"
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-emerald-100 flex flex-col items-center mx-auto transition-all">
                                        <i class="fa-solid fa-paperclip text-lg"></i>
                                        <span class="text-[9px] font-bold mt-1">({{ count($der['documentos']) }}) Docs</span>
                                    </button>
                                @else
                                    <span class="text-slate-300 italic text-[10px]">Sin adjuntos</span>
                                @endif
                            </td>
                        </tr>

                        @if(count($der['acciones']) > 0)
                            <tr class="bg-orange-50/30">
                                <td colspan="5" class="px-6 py-4">
                                    <div class="border-l-2 border-orange-200 pl-4 space-y-2">
                                        <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-2">Seguimiento de Acciones</p>
                                        <div class="overflow-x-auto rounded-lg border shadow-sm">
                                            <table class="w-full bg-white text-left border-collapse">
                                                <thead class="bg-slate-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-[9px] font-black text-slate-400 uppercase">Fecha</th>
                                                        <th class="px-4 py-2 text-[9px] font-black text-slate-400 uppercase">Profesional</th>
                                                        <th class="px-4 py-2 text-[9px] font-black text-slate-400 uppercase">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50">
                                                    @foreach($der['acciones'] as $accion)
                                                        <tr>
                                                            <td class="px-4 py-2 text-xs font-bold text-slate-600">{{ \Carbon\Carbon::parse($accion->fecha)->format('d/m/Y') }}</td>
                                                            <td class="px-4 py-2 text-xs font-bold text-slate-700">{{ $accion->usuario->name ?? 'N/A' }}</td>
                                                            <td class="px-4 py-2 text-xs text-slate-600 italic">{{ $accion->descripcion }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
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
        @if($activeTab === 'redes')
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($estudiante->redes as $red)
                        <div class="bg-white border rounded-xl p-4 shadow-sm hover:border-emerald-300 transition-colors border-l-4 border-l-emerald-500">
                            <div class="flex justify-between items-start mb-3">
                                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                                    <i class="fa-solid fa-hospital-user text-xl"></i>
                                </div>
                                <span class="px-2 py-1 {{ $red->pivot->activo ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }} rounded text-[9px] font-black uppercase">
                                    {{ $red->pivot->activo ? 'Activo' : 'Histórico' }}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-800 uppercase text-sm mb-1">{{ $red->nombre }}</h3>
                            <p class="text-[11px] text-slate-500 font-bold mb-3">
                                <i class="fa-solid fa-calendar-day mr-1"></i> Vinculado el: {{ $red->pivot->created_at->format('d/m/Y') }}
                            </p>

                            <div class="bg-slate-50 p-3 rounded-lg border border-dashed border-slate-200">
                                <span class="text-[9px] font-black text-slate-400 uppercase block mb-1">Observación del Profesional</span>
                                <p class="text-xs text-slate-600 italic">
                                    {{ $red->pivot->observacion ?? 'Sin observaciones registradas.' }}
                                </p>
                            </div>

                            {{-- <div class="mt-4 pt-3 border-t flex flex-col gap-1">
                                <div class="flex items-center text-[11px] text-slate-600">
                                    <i class="fa-solid fa-phone w-5"></i> {{ $red->telefono }}
                                </div>
                                <div class="flex items-center text-[11px] text-slate-600">
                                    <i class="fa-solid fa-envelope w-5"></i> {{ $red->email }}
                                </div>
                            </div> --}}
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="mb-4 text-slate-200">
                                <i class="fa-solid fa-box-open text-6xl"></i>
                            </div>
                            <p class="text-slate-400 italic">El estudiante no cuenta con redes de apoyo externas registradas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL ADJUNTOS --}}
    <flux:modal wire:model="verArchivosModal" variant="wide">
        <div class="p-2 md:p-4 space-y-4">
            <h3 class="text-lg font-bold border-b pb-2 text-slate-800">Archivos Adjuntos</h3>
            <div class="grid grid-cols-1 gap-2 max-h-[60vh] overflow-y-auto">
                @forelse($documentosMostrar as $doc)
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <i class="fa-solid fa-file-lines text-blue-500 flex-shrink-0"></i>
                            <span class="text-xs md:text-sm font-semibold text-slate-700 truncate">{{ $doc->name }}</span>
                        </div>
                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank"
                           class="flex-shrink-0 px-3 py-1.5 bg-blue-600 text-white text-[9px] font-black uppercase rounded-md shadow-sm">
                            Ver
                        </a>
                    </div>
                @empty
                    <p class="text-center text-slate-400 italic py-4">No se encontraron archivos.</p>
                @endforelse
            </div>
            <div class="flex justify-end pt-2">
                <flux:button wire:click="$set('verArchivosModal', false)" variant="ghost">Cerrar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
