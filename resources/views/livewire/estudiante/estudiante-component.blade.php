<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Estudiantes</h1>
    <hr class="mb-3">

    <div class="flex justify-end px-6">
        <flux:button wire:click="$set('abrirModal', true)" variant="primary" color="green">
            <i class="fa-solid fa-circle-plus"></i><span class="ml-3">Nuevo Estudiante</span>
        </flux:button>

        @if (Auth::user()->rol==="Administrador")
        <flux:button wire:click="$set('excelModal', true)" class="ml-3" variant="primary" color="blue">
            <i class="fa-solid fa-upload"></i><span class="ml-3">Subir Excel</span>
        </flux:button>

        <flux:button wire:click="abrirModalPromocionCurso" class="ml-3" variant="primary" color="orange">
            <i class="fa-solid fa-calendar-plus text-white"></i><span class="ml-3 text-white">Nuevo Año Escolar</span>
        </flux:button>
        @endif


    </div>

    <hr  class="mt-3 mb-3">

    {{-- {{$profesionales}} --}}
    @livewire('tablas.estudiante-table')


    <flux:modal wire:model="modalMasivo" class="md:w-[600px]">
        <div class="space-y-6">
            <flux:heading size="lg">Registrar Intervención Grupal</flux:heading>
            <flux:text>
                Se aplicará a <b>{{ count($selectedIds) }}</b> estudiantes seleccionadas.
            </flux:text>

            <flux:input label="Fecha: " wire:model="fechaintervencionMasiva" type="date"/>

            <flux:select label="Vía de Ingreso / Área" wire:model="tipoMasivo">
                <flux:select.option value="">Seleccione una opción...</flux:select.option>
                @foreach($vias as $via)
                    <flux:select.option value="{{ $via->id }}">{{ $via->via_ingreso }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea
                label="Descripción de la intervención"
                wire:model="detalleMasivo"
                placeholder="Escriba aquí el detalle que aparecerá en la ficha de cada estudiante..."
                rows="5"
            />

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('modalMasivo', false)">Cancelar</flux:button>
                <flux:button variant="primary" wire:click="guardarIntervencionMasiva">
                    <span wire:loading.remove wire:target="guardarIntervencionMasiva">Guardar en todas las fichas</span>
                    <span wire:loading wire:target="guardarIntervencionMasiva">Procesando...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="abrirModal" :dismissible="false" :closable="false" class="w-full max-w-7xl">

        <flux:card  class="w-full max-w-none">

        <flux:heading size="4xl">
                Nuevo Estudiante
            </flux:heading>

            <flux:text class="text-gray-500 mb-4">
                Registra un nuevo estudiante en el sistema.
            </flux:text>

            <form wire:submit="procesarFormulario" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">

                    <div>
                        <flux:input
                            label="Rut"
                            mask:dynamic="$input.replace(/[\.\-]/g, '').length > 9 ? '999.999.999-*' : ($input.replace(/[\.\-]/g, '').length > 8 ? '99.999.999-*' : '9.999.999-*')"
                            wire:model="rut"
                            placeholder="Rut del estudiante"
                            />
                    </div>
                    <div>
                        <flux:input
                            type="date"
                            label="Fecha de Nacimiento"
                            wire:model="fecha_nacimiento"
                            placeholder="Fecha de nacimiento del estudiante"
                            />
                    </div>
                    <div>
                        <flux:input
                            label="Nombre Social"
                            wire:model="social"
                            placeholder="Nombre social del estudiante"
                            />
                    </div>

                    <div>
                        <flux:input
                            label="Nombre del Estudiante"
                            wire:model="name"
                            placeholder="Nombre del estudiante"
                            />
                    </div>

                    <div  class="col-span-2">
                        <flux:input
                            label="Apellidos del Estudiante"
                            wire:model="apellido"
                            placeholder="Apellidos del estudiante"
                            />
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                    <div>
                        <flux:input
                            label="Emai"
                            wire:model="email"
                            placeholder="email del estudiante"
                        />
                    </div>
                    <div>
                        <flux:select label="Curso" wire:model="curso_id">
                            <option value="">Seleccione</option>
                            @foreach($cursos as $curso)
                                @if ($curso->estado==="Activo")
                                    <option value="{{ $curso->id }}">{{ $curso->curso }}</option>
                                @endif
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="col-span-2">
                        <flux:input
                            label="Dirección"
                            wire:model="domicilio"
                            placeholder="dirección del estudiante"
                        />
                    </div>
                    <div>
                        <flux:input
                            label="Teléfono"
                            wire:model="telefono"
                            placeholder="teléfono del estudiante"
                        />
                    </div>
                </div>


                    <flux:textarea
                        label="Observaciones"
                        wire:model="observaciones"
                        placeholder="Observaciones del estudiante"
                    />

                    {{-- 👇 LO NUEVO: Input para subir documentos del estudiante 👇 --}}
                    <div class="mt-4 mb-4">
                        <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                            Documentos Anexos (Informes, diagnósticos, etc.)
                        </flux:text>
                        <flux:input type="file" wire:model="archivo_estudiante"/>
                        @error('archivo_estudiante')
                            <span class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- 👆 FIN DE LO NUEVO 👆 --}}

                    <div class="flex justify-end mt-4">
                    <div class="mr-3">
                        <flux:button variant="danger" wire:click="cerrarModal" class="ml-3">
                            Cerrar
                        </flux:button>
                    </div>

                    @if ($estudianteId)
                        <div class="ml-3">
                            {{-- Usamos type="submit" para no chocar con el formulario --}}
                            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="actualizar, archivo_estudiante">
                                Actualizar
                            </flux:button>
                        </div>
                    @else
                        <div class="ml-3">
                            {{-- Usamos type="submit" para no chocar con el formulario --}}
                            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="guardar, archivo_estudiante">
                                Guardar
                            </flux:button>
                        </div>
                    @endif
                </div>


            </form>

        </flux:card>

    </flux:modal >

    <flux:modal wire:model="derivarModal" :dismissible="false" :closable="false" class="w-full max-w-7xl">

        <flux:card  class="w-full max-w-none">

            <flux:heading size="4xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                        <i class="fa-solid fa-address-card text-lg"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                        <strong>Derivando Estudiante:</strong> {{ $estudianteSeleccionado?->nombre }}   {{ $estudianteSeleccionado?->apellido }} <strong> / Nombre Social: </strong> {{$estudianteSeleccionado?->social}}
                    </h3>
                </div>
            </flux:heading>

        @if($estudianteSeleccionado)
            <flux:text class="text-gray-500 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 items-center">
                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-id-card mr-1"></i> RUT
                        </span>
                        <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                            {{ $estudianteSeleccionado?->rut ?: 'No registrado' }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-graduation-cap mr-1"></i> Curso
                        </span>
                        <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                            {{ $estudianteSeleccionado?->curso ? $estudianteSeleccionado?->curso->curso : 'Sin curso asignado' }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-envelope mr-1"></i> Email
                        </span>
                        <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                            {{ $estudianteSeleccionado?->email ?: 'No registrado' }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-phone mr-1"></i> Teléfono
                        </span>
                        <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                            {{ $estudianteSeleccionado?->telefono ?: 'No registrado' }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-cake-candles mr-1"></i> Fecha Nacimiento
                        </span>
                        <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                            {{ $estudianteSeleccionado?->fecha_nacimiento ? \Carbon\Carbon::parse($estudianteSeleccionado?->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-location-dot mr-1"></i> Dirección
                        </span>
                        <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                            {{ $estudianteSeleccionado?->domicilio ?: 'No registrada' }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-toggle-on mr-1"></i> Estado
                        </span>
                        <div>
                            @if($estudianteSeleccionado?->estado === 'Activo')
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-green-900/30 dark:text-green-400">Activo</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-red-900/30 dark:text-red-400">Inactivo</span>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="bg-white dark:bg-zinc-800 p-4 rounded-md border border-slate-200 dark:border-zinc-700">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">
                        <i class="fa-solid fa-clipboard text-slate-400 mr-1"></i> Observaciones
                    </span>
                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">
                        {{ $estudianteSeleccionado?->observaciones ?: 'El estudiante no tiene observaciones registradas actualmente.' }}
                    </p>
                </div>

            </flux:text>
        @endif

            <form wire:submit="guardarDerivacion" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 mt-4">

                    <div class="flex flex-col mb-4">
                        <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider">
                            Fecha Derivación
                        </flux:text>

                        <strong><flux:input wire:model="fechaDerivacion" type="date"/></strong>

                        {{-- <flux:text class="text-lg font-medium text-slate-800 dark:text-white mt-1">
                            {{ now()->format('d/m/Y') }}
                        </flux:text> --}}
                    </div>

                    <div class="flex flex-col mb-4">
                        <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider">
                            Derivado Por:
                        </flux:text>

                        <flux:text class="text-lg font-medium text-slate-800 dark:text-white mt-1">
                            {{Auth::user()->name}}
                        </flux:text>
                    </div>

                    <div>
                        <flux:text  class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                            Motivo Derivación
                        </flux:text>
                        <flux:select wire:model="motivo_derivacion">
                            <option value="">Seleccione</option>
                            {{-- Grupo de Motivos --}}
                            <optgroup label="Motivos de Intervención">
                                @foreach($motivos->where('grupo', 'Motivos de Intervención') as $item)
                                    <option value="{{ $item->valor }}">{{ $item->texto }}</option>
                                @endforeach
                            </optgroup>

                            {{-- Grupo de Faltas --}}
                            <optgroup label="Faltas Disciplinarias">
                                @foreach($motivos->where('grupo', 'Faltas Disciplinarias') as $item)
                                    <option value="{{ $item->valor }}">{{ $item->texto }}</option>
                                @endforeach
                            </optgroup>
                        </flux:select>
                    </div>
                </div>
                    {{-- <div class="col-span-2">
                        <flux:text  class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                            Profesional al que deriva
                        </flux:text>
                        <flux:select wire:model="profesional_derivado_id">
                            <option value="">Seleccione</option>
                            @foreach($profesionales as $profesional)
                                @if ($profesional->estado==="Activo")
                                    <option value="{{ $profesional->id }}">{{ $profesional->name }} - {{$profesional->tipoProfesional->tipo}}</option>
                                @endif
                            @endforeach
                        </flux:select>
                    </div> --}}
                <div class="col-span-2">
                    <flux:checkbox.group wire:model="profesionales_derivados_ids" label="Profesional(es) al que deriva">
                        {{-- EL CAMBIO ESTÁ AQUÍ: Agregamos lg:grid-cols-4 y ajustamos el gap --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
                            @foreach($profesionales as $profesional)
                                @if ($profesional->estado === "Activo")
                                    <flux:checkbox
                                        value="{{ $profesional->id }}"
                                        label="{{ $profesional->name }} - {{ $profesional->tipoProfesional->tipo }}"
                                    />
                                @endif
                            @endforeach
                        </div>
                    </flux:checkbox.group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

    {{-- COLUMNA 1: DETALLE DE DERIVACIÓN --}}
    <div>
        {{-- Contenedor Flex para alinear Título y Botón en la misma línea --}}
        <div class="flex items-center justify-between mb-2">
            <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider m-0">
                Detalle de la Derivacion
            </flux:text>

            <flux:button
                variant="subtle"
                size="sm"
                wire:click="mejorarTextoIAdetalle"
                wire:loading.attr="disabled"
                class="text-indigo-600 border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100"
            >
                <span wire:loading.remove wire:target="mejorarTextoIAdetalle">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Mejorar con IA
                </span>
                <span wire:loading wire:target="mejorarTextoIAdetalle">
                    <i class="fa-solid fa-spinner animate-spin mr-2"></i> Procesando...
                </span>
            </flux:button>
        </div>

        <flux:textarea
            wire:model="detalle_derivacion"
            placeholder="Detalle aquí el motivo por el cual deriva a la estudiante..."
        />
    </div>

    {{-- COLUMNA 2: ACCIONES PREVIAS --}}
    <div>
        {{-- Contenedor Flex para alinear Título y Botón en la misma línea --}}
        <div class="flex items-center justify-between mb-2">
            <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider m-0">
                Acciones Previas
            </flux:text>

            <flux:button
                variant="subtle"
                size="sm"
                wire:click="mejorarTextoIAacciones"
                wire:loading.attr="disabled"
                class="text-indigo-600 border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100"
            >
                <span wire:loading.remove wire:target="mejorarTextoIAacciones">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Mejorar con IA
                </span>
                <span wire:loading wire:target="mejorarTextoIAacciones">
                    <i class="fa-solid fa-spinner animate-spin mr-2"></i> Procesando...
                </span>
            </flux:button>
        </div>

        <flux:textarea
            wire:model="previos_derivacion"
            placeholder="Detalle aquí que acciones tomó antes de derivar a la estudiante..."
        />
    </div>

</div>

                <div>
                    <div class="mt-4">
                        <flux:label>Adjuntar Documentos (Opcional)</flux:label>
                        <flux:input
                            type="file"
                            wire:model="archivo_adjunto"
                            multiple
                            accept=".pdf,.doc,.docx,.jpg,.png"
                        />

                        {{-- Indicador de progreso para múltiples archivos --}}
                        <div wire:loading wire:target="archivo_adjunto" class="text-xs text-blue-500 mt-2">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Subiendo archivos al servidor...
                        </div>

                        @error('archivo_adjunto.*')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                    {{-- @dump($cursos) --}}

                    <div class="flex justify-end">
                        <div class="mr-3">
                            <flux:button variant="danger" wire:click="cerrarModalDerivacion" class="ml-3">
                                Cerrar
                            </flux:button>
                        </div>
                        {{-- @if ($estudianteId) --}}
                            {{-- @dump({{ $estudiante}}) --}}
                            {{-- <div class="ml-3">
                                <flux:button wire:click="actualizar" variant="primary">
                                    Actualizar
                                </flux:button>
                            </div>
                        @else --}}
                        <div class="ml-3">
                            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="guardarDerivacion, archivo_adjunto">
                                Guardar
                            </flux:button>
                        </div>
                        {{-- @endif --}}

                    </div>


            </form>

        </flux:card>

    </flux:modal >

    <flux:modal wire:model="excelModal" :dismissible="false" :closable="false" class="w-full max-w-7xl">
        <flux:card class="w-full max-w-none">
            <div class="mt-2">
                <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider ">
                    Suba acá su archivo excel con el listado de estudiantes.
                </flux:text>
                <flux:input class="mt-2" type="file" wire:model="subirExcel" accept=".xlsx, .xls, .csv" />
                @error('subirExcel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-3 flex justify-end">
                <flux:button variant="danger" wire:click="cerrarModalExcel" class="ml-3">
                    Cerrar
                </flux:button>

                <flux:button variant="primary" wire:click="importarExcel" class="ml-3">
                    <i class="fa-solid fa-cloud-arrow-up"></i><span class="ml-2">Importar Archivo</span>
                </flux:button>
            </div>
        </flux:card>
    </flux:modal>

    <flux:modal wire:model="modalRedes" class="md:w-1/2">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl">Redes de Apoyo</flux:heading>
                <flux:subheading>Estudiante: {{ $estudianteSeleccionadoRedes?->nombre }} {{ $estudianteSeleccionadoRedes?->apellido }}</flux:subheading>
            </div>

            {{-- LISTADO ACTUAL --}}
            <div class="space-y-2">
                <p class="text-xs font-bold uppercase text-slate-400">Centros vinculados actualmente:</p>
                @if($estudianteSeleccionadoRedes && $estudianteSeleccionadoRedes->redes->count() > 0)
                    @foreach($estudianteSeleccionadoRedes->redes as $red)
                        <div class="flex justify-between items-center p-3 bg-slate-50 border rounded-lg">
                            <div>
                                <p class="text-sm font-bold">{{ $red->nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $red->pivot->observacion }}</p>
                            </div>
                            <flux:button variant="ghost" icon="trash" wire:click="desvincularRed({{ $red->id }})" />
                        </div>
                    @endforeach
                @else
                    <p class="text-sm italic text-slate-400">No tiene redes asignadas.</p>
                @endif
            </div>

            <hr>

            {{-- FORMULARIO DE ASIGNACIÓN --}}
            <div class="grid gap-4">
                <flux:select label="Nueva Red" wire:model="red_id">
                    <flux:select.option value="">Seleccione un Centro</flux:select.option>
                    @foreach($redes as $red) {{-- Asegúrate de pasar $redes desde el render --}}
                        <flux:select.option value="{{ $red->id }}">{{ $red->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input label="Contacto: (Nombre, Teléfono, Correo Electrónico)" wire:model="observacion_red" placeholder="Ej. Juan Pérez, +56958745269, correo@mail.com" />

                <flux:button variant="primary" wire:click="asignarRed">Vincular Estudiante</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="modalApoderados" class="w-full max-w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl">Apoderados Asignados</flux:heading>
                <flux:subheading>Estudiante: {{ $estudianteSeleccionadoRedes?->nombre }} {{ $estudianteSeleccionadoRedes?->apellido }}</flux:subheading>
            </div>

            {{-- LISTADO ACTUAL --}}
            <div class="space-y-2 ">
                @if($estudianteSeleccionadoRedes && $estudianteSeleccionadoRedes->apoderados->count() > 0)
                    @foreach($estudianteSeleccionadoRedes->apoderados as $apod)
                        <div class="flex justify-between items-center p-3 bg-slate-50 border rounded-lg">
                            <div class="flex justify-between items-start p-3 bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-sm">
                            <div class="grid justify-between grid-cols-1 md:grid-cols-2 gap-6 w-full pr-4">
                                <div><p class="text-sm"><strong>Nombre : </strong>{{ $apod->apoderado }}</p></div>
                                <div><p class="text-sm"><strong>Teléfono : </strong>{{ $apod->telefono }}</p></div>
                                <div><p class="text-sm"><strong>Email : </strong>{{ $apod->correo }}</p></div>
                                <div><p class="text-sm"><strong>Dirección : </strong>{{ $apod->direccion }}</p></div>
                                <div>
                                    <p class="text-sm">
                                        <strong>Estado : </strong>
                                        <span class="{{ $apod->estado == 'Activo' ? 'text-emerald-600' : 'text-red-600' }} font-semibold">
                                            {{ $apod->estado }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm"><strong>Carnet : </strong>
                                        @if($apod->carnet)
                                            <a class="text-blue-600 hover:underline" href="{{ asset('storage/'. $apod->carnet) }}" target="_blank">Ver Carnet</a>
                                        @else
                                            <span class="text-slate-400 italic">No adjunto</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- EL BOTÓN DE DESVINCULAR --}}
                            <div class="flex-shrink-0 mt-1">
                                <flux:button variant="danger" size="sm" icon="trash" wire:click="desvincularApoderado({{ $apod->id }})" title="Quitar Apoderado de este Estudiante" />
                            </div>
                        </div>
                            {{-- <flux:button variant="ghost" icon="trash" wire:click="desvincularRed({{ $red->id }})" /> --}}
                        </div>
                    @endforeach
                @else
                    <p class="text-sm italic text-slate-400">No tiene Apoderados asignados.</p>
                @endif
            </div>

            <hr>

            {{-- FORMULARIO DE ASIGNACIÓN --}}
            <div class="grid gap-4">
                {{-- <flux:select label="Nueva Red" wire:model="red_id">
                    <flux:select.option value="">Seleccione un Centro</flux:select.option>
                    @foreach($redes as $red)
                        <flux:select.option value="{{ $red->id }}">{{ $red->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input label="Contacto: (Nombre, Teléfono, Correo Electrónico)" wire:model="observacion_red" placeholder="Ej. Juan Pérez, +56958745269, correo@mail.com" />

                <flux:button variant="primary" wire:click="asignarRed">Vincular Estudiante</flux:button>--}}
            </div>
        </div>
    </flux:modal>

    {{-- Botón para abrir el modal de promoción (puedes ponerlo al lado del otro botón masivo) --}}
    @if(count($selectedIds) > 0)
        <flux:button wire:click="$set('modalPromocion', true)" variant="filled" color="indigo" class="mr-3">
            <i class="fa-solid fa-graduation-cap"></i>
            <span class="ml-2">Cambio de Curso ({{ count($selectedIds) }})</span>
        </flux:button>
    @endif

    {{-- Modal de Cambio de Curso Masivo --}}
    <flux:modal wire:model="modalPromocion" class="md:w-[500px]">
        <div class="space-y-6">
            <flux:heading size="lg">Cambio Masivo de Curso</flux:heading>
            <flux:text>
                Vas a mover a <b>{{ count($selectedIds) }}</b> estudiantes al siguiente curso.
            </flux:text>

            <flux:select label="Seleccione el Curso de Destino" wire:model="nuevo_curso_id">
                <flux:select.option value="">Seleccione un curso...</flux:select.option>
                @foreach($cursos as $curso)
                    <flux:select.option value="{{ $curso->id }}">{{ $curso->curso }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex gap-3 justify-end">
                <flux:button variant="ghost" wire:click="$set('modalPromocion', false)">Cancelar</flux:button>
                <flux:button variant="primary" wire:click="cambiarCursoMasivo">Confirmar Cambio</flux:button>
            </div>
        </div>
    </flux:modal>

<flux:modal wire:model="modalPromocionCurso" class="w-full md:w-[1000px] h-[750px]">
    <div class="flex flex-col h-full">
        <flux:heading size="xl" class="mb-4">Promoción Estudiantes {{ $anioParaPromocion }}</flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <flux:select label="Curso Origen ({{ $anioParaPromocion }})" wire:model.live="curso_origen">
                <option value="">Seleccione origen...</option>
                @foreach($cursos as $c)
                    <option value="{{ $c->id }}">{{ $c->curso }}</option>
                @endforeach
            </flux:select>

            <flux:select label="Curso Destino ({{ $anioParaPromocion + 1 }})" wire:model="curso_destino">
                <option value="">Seleccione destino...</option>
                @foreach($cursos as $c)
                    <option value="{{ $c->id }}">{{ $c->curso }}</option>
                @endforeach
            </flux:select>

            {{-- <flux:select label="Condición" wire:model="condicion">
                <option value="">Seleccione condición...</option>
                <option value="Promovida">Promovida</option>
                <option value="Reprobada">Reprobada</option>
                <option value="Egresada">Egresada</option>
                <option value="Regular">Regular (Traslado intermedio)</option>
            </flux:select> --}}
        </div>

        <div class="flex-grow overflow-auto border rounded-lg">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b sticky top-0">
                    <tr>
                        <th class="p-3 w-12 text-center">
                            <input type="checkbox"
                                wire:model.live="seleccionarTodo"
                                class="rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </th>
                        <th class="p-3 text-sm hidden font-semibold text-slate-600">ID</th>
                        <th class="p-3 text-sm font-semibold text-slate-600">Estudiante</th>
                        <th class="p-3 text-sm font-semibold text-slate-600">Estado Actual</th>
                    </tr>
                </thead>
                <tbody class="divide-y bg-white">
                    @forelse($this->estudiantes_pendientes as $est)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-3 text-center">
                            <input type="checkbox" wire:model.live="estudiantes_seleccionadas" value="{{ $est->id }}" class="rounded border-slate-300 text-orange-600">
                        </td>
                        <td class="p-3 hidden text-sm text-slate-500">{{ $est->id }}</td>
                        <td class="p-3 text-sm font-medium text-slate-700">
                            {{ $est->nombre }} {{ $est->apellido }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                {{ $est->estado }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-slate-400 italic">
                            @if(!$curso_origen)
                                Seleccione un curso de origen para listar estudiantes.
                            @else
                                No hay estudiantes pendientes de procesar en este curso para el año {{ $anioParaPromocion }}.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- @dump($estudiantes_seleccionadas) --}}

        <div class="mt-4 flex justify-between items-center bg-zinc-50 p-4 rounded-b-lg border-t">
            <div class="text-sm font-medium text-slate-600">
                {{-- Seleccionadas: <strong class="text-orange-600">{{ count($estudiantes_seleccionadas) }}</strong> --}}
            </div>
            <div class="flex gap-2">
                <flux:button variant="ghost" wire:click="cerrarModalPromocionCurso">Cancelar</flux:button>

                <flux:button variant="primary" color="orange" wire:click="promocionarSeleccionadas" class="!text-white">
                    Aplicar Cambio y Guardar Historial
                </flux:button>
            </div>
        </div>
    </div>
</flux:modal>

</div>
