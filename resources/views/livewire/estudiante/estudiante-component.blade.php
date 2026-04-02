<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Estudiantes</h1>
    <hr class="mb-3">

    <div class="flex justify-end px-6">
        <flux:button wire:click="$set('abrirModal', true)">
            <i class="fa-solid fa-circle-plus"></i><span class="ml-3">Nuevo Estudiante</span>
        </flux:button>
        <flux:button wire:click="$set('excelModal', true)" class="ml-3" variant="primary" color="blue">
            <i class="fa-solid fa-upload"></i><span class="ml-3">Subir Excel</span>
        </flux:button>
    </div>

    <hr  class="mt-3 mb-3">

    {{-- {{$profesionales}} --}}
    @livewire('tablas.estudiante-table')

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
                           mask:dynamic="$input.replace(/[\.\-]/g, '').length > 9 ?  '999.999.999-*' : '99.999.999-*'"
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
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 mt-4">

                    <div class="flex flex-col mb-4">
                        <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider">
                            Fecha Derivación
                        </flux:text>

                        <flux:text class="text-lg font-medium text-slate-800 dark:text-white mt-1">
                            {{ now()->format('d/m/Y') }}
                        </flux:text>
                    </div>

                    <div class="flex flex-col mb-4">
                        <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider">
                            Derivado Por:
                        </flux:text>

                        <flux:text class="text-lg font-medium text-slate-800 dark:text-white mt-1">
                            {{Auth::user()->name}}
                        </flux:text>
                    </div>

                    <div class="col-span-2">
                        <flux:text  class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                            Motivo Derivación
                        </flux:text>
                        <flux:select wire:model="motivo_derivacion">
                            <option value="">Seleccione</option>
                            @foreach($motivos as $motivo)

                                <option value="{{ $motivo->id }}">{{ $motivo->motivo }}</option>

                            @endforeach
                        </flux:select>
                    </div>
                    <div class="col-span-2">
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
                    </div>

                </div>
                <div  class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-3 mt-4">
                    <div>
                        <flux:text  class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-4">
                            Detalle de la Derivacion
                        </flux:text>
                        <flux:textarea
                            wire:model="detalle_derivacion"
                            placeholder="Detalle aquí el motivo por el cual deriva a la estudiante...   "
                        />
                    </div>

                    <div>
                        <flux:text  class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-4">
                            Acciones Previas
                        </flux:text>
                        <flux:textarea
                            wire:model="previos_derivacion"
                            placeholder="Detalle aquí que acciones tomó antes de derivar a la estudiante...   "
                        />
                    </div>
                </div>
                <div>
                    <flux:text  class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                            Adjunte algún archivo <small class="text-xs">(opcional)</small>
                        </flux:text>
                    <flux:input type="file" wire:model="archivo_adjunto"/>
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

</div>
