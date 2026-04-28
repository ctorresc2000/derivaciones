<div>
    {{-- INTERCENCIÓN CONIVENCIA --}}

    <div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

        <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                <i class="fa-solid fa-address-card text-lg"></i>
            </div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-white">
                <strong>INTERVENCIÓN CONVIVENCIA.</strong>
            </h1>
            <br>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <strong>Información Estudiante:</strong> {{ $estudiante->nombre }} {{ $estudiante->apellido }} / <strong>Nombre Social :</strong> {{ $estudiante->social ?: 'No registrado' }}
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 items-center">

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-id-card mr-1"></i> RUT
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $estudiante->rut ?: 'No registrado' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-graduation-cap mr-1"></i> Curso
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $estudiante->curso ? $estudiante->curso->curso : 'Sin curso asignado' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-envelope mr-1"></i> Email
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $estudiante->email ?: 'No registrado' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-phone mr-1"></i> Teléfono
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $estudiante->telefono ?: 'No registrado' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-cake-candles mr-1"></i> Fecha Nacimiento
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $estudiante->fecha_nacimiento ? \Carbon\Carbon::parse($estudiante->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-location-dot mr-1"></i> Dirección
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">
                    {{ $estudiante->domicilio ?: 'No registrada' }}
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-toggle-on mr-1"></i> Estado
                </span>
                <div>
                    @if($estudiante->estado === 'Activo')
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
                {{ $estudiante->observaciones ?: 'El estudiante no tiene observaciones registradas actualmente.' }}
            </p>
        </div>

    </div>

    <div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 items-center">

            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <strong>Intervenido Por:</strong> {{ Auth::user()->name }}
            </h3>
            </div>

            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <i class="fa-solid fa-calendar"></i>
                </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
            </h3>
            </div>
        </div>

        <div  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-6 items-center">

            {{-- <flux:select label="Derivar a:" wire:model="derivacion_destino">
                <option value="">Selecciona un destino de derivación</option>
                @foreach($profesionales as $profesional)
                    @if ($profesional->estado==="Activo")
                        <option value="{{ $profesional->id }}">{{ $profesional->nombre }} - {{ $profesional->tipo }}</option>
                    @endif
                @endforeach
            </flux:select> --}}

            {{-- Envolvemos en Alpine para escuchar el evento de SweetAlert --}}
            <div x-data="{}"
                 @pedir-nueva-via.window="
                    Swal.fire({
                        title: 'Nueva Vía de Ingreso',
                        text: 'Escribe la nueva vía de ingreso:',
                        input: 'text',
                        inputPlaceholder: 'Ej. Derivación externa...',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        inputValidator: (value) => {
                            if (!value) {
                                return '¡Debes escribir un nombre!'
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            // Enviamos el texto ingresado a nuestro PHP
                            $wire.guardarNuevaVia(result.value);
                        } else {
                            // Si cancela, reseteamos el select
                            $wire.set('via_ingreso_id', '');
                        }
                    });
                 ">

                    <flux:select label="Via de Ingreso:" wire:model.live="via_ingreso_id">
                        <option value="">Selecciona una via de ingreso</option>
                        @foreach($viaingresos as $viaingreso)
                            <option value="{{ $viaingreso->id }}">{{ $viaingreso->via_ingreso }}</option>
                        @endforeach
                            <option value="otro">Otro...</option>
                    </flux:select>
            </div>

            {{-- <flux:input label="Motivo de Derivación" wire:model="motivo_derivacion" placeholder="Escribe el motivo de la derivación aquí..." /> --}}

            <flux:input type="file" wire:model="archivos" label="Desea adjuntar algún documento" multiple />

        </div>

        <div class="mb-6 bg-slate-50 dark:bg-zinc-900/50 p-5 rounded-lg border border-slate-200 dark:border-zinc-800">

            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-200 dark:border-zinc-700 pb-2">
                <i class="fa-solid fa-scale-balanced mr-1"></i> Registro de Faltas y Medidas
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1fr_1fr_auto] gap-4 mb-6 items-end">
                <div>
                    <flux:select label="Tipo de Falta" wire:model="falta_seleccionada_id">
                        <option value="">Selecciona un tipo de Falta</option>
                        @foreach($faltas as $falta)
                            <option value="{{ $falta->id }}">{{ $falta->falta }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:select label="Tipo de Medida" wire:model="medida_seleccionada_id">
                        <option value="">Selecciona un tipo de Medida</option>
                        @foreach($medidas as $medida)
                            <option value="{{ $medida->id }}">{{ $medida->medida }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="flex gap-2">
                    @if($editando_index !== null)
                        <flux:button variant="warning" wire:click="agregarDato">
                            <i class="fa-solid fa-pen-to-square"></i><span class="ml-2">Actualizar</span>
                        </flux:button>
                        <flux:button variant="subtle" wire:click="limpiarFormularioDatos" tooltip="Cancelar edición">
                            <i class="fa-solid fa-xmark"></i>
                        </flux:button>
                    @else
                        <flux:button variant="primary" wire:click="agregarDato">
                            <i class="fa-solid fa-plus"></i><span class="ml-2">Agregar</span>
                        </flux:button>
                    @endif
                </div>
            </div>

            @if(count($listaDatosAgregados) > 0)
                <div class="rounded-lg border border-slate-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-800 shadow-sm">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-zinc-300">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-100 dark:bg-zinc-900/80 dark:text-zinc-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-5/12">Tipo de Falta</th>
                                <th scope="col" class="px-6 py-3 w-5/12 border-l border-slate-200 dark:border-zinc-700">Medida Aplicada</th>
                                <th scope="col" class="px-6 py-3 w-2/12 text-center border-l border-slate-200 dark:border-zinc-700">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listaDatosAgregados as $index => $item)
                                <tr class="border-b dark:border-zinc-700 last:border-0 hover:bg-slate-50 dark:hover:bg-zinc-700/30 transition-colors {{ $editando_index === $index ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                    <td class="px-6 py-3 font-medium text-slate-800 dark:text-slate-200">
                                        {{ $item['falta_nombre'] }}
                                    </td>
                                    <td class="px-6 py-3 border-l border-slate-200 dark:border-zinc-800">
                                        {{ $item['medida_nombre'] }}
                                    </td>
                                    <td class="px-6 py-3 text-center border-l border-slate-200 dark:border-zinc-800">
                                        <div class="flex justify-center gap-2">
                                            {{-- <flux:button size="sm" variant="subtle" wire:click="editarDato({{ $index }})" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                                <i class="fa-solid fa-pen"></i>
                                            </flux:button> --}}

                                            <flux:button size="sm" variant="subtle" wire:click="eliminarDato({{ $index }})" class="text-red-600 hover:text-red-700 dark:text-red-400">
                                                <i class="fa-solid fa-trash"></i>
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-slate-400 dark:text-zinc-500 text-sm border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-lg">
                    <i class="fa-solid fa-clipboard-list text-2xl mb-2 opacity-50"></i>
                    <p>Aún no has agregado faltas ni medidas a esta Intervención.</p>
                </div>
            @endif

        </div>


        <div  class="grid grid-cols-1 items-center">

            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-200 dark:border-zinc-700 pb-2">
                <i class="fa-solid fa-paragraph"></i> Descripción de la Intervención
            </h3>
            <div class="flex justify-end">
                <flux:button
                    variant="subtle"
                    size="sm"
                    wire:click="mejorarTextoIA"
                        wire:loading.attr="disabled"
                        class="text-indigo-600 border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100"
                    >
                    <span wire:loading.remove wire:target="mejorarTextoIA">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Mejorar con IA
                    </span>
                    <span wire:loading wire:target="mejorarTextoIA">
                        <i class="fa-solid fa-spinner animate-spin mr-2"></i> Procesando...
                    </span>
                </flux:button>
            </div>

            <flux:textarea  wire:model="descripcion_derivacion" placeholder="Describa la derivació acá." />

        </div>

        <div class="mt-4 p-4 rounded-lg bg-zinc-800/50 border border-zinc-700/50">

            <div class="mb-3 flex items-center gap-2 border-b border-zinc-700 pb-2">
                <i class="fa-regular fa-envelope text-zinc-400"></i>
                <h3 class="text-sm font-semibold text-white">Enviar copia de esta intervención a:</h3>
                <span class="text-xs text-zinc-400 ml-2">(Opcional)</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-3">
                @foreach($listaUsuarios as $usuario)
                    <label class="flex items-center gap-3 p-2 rounded-md hover:bg-zinc-700/30 cursor-pointer transition-colors border border-transparent hover:border-zinc-600">

                        <input
                            type="checkbox"
                            wire:model="usuariosSeleccionados"
                            value="{{ $usuario->id }}"
                            class="rounded border-zinc-600 bg-zinc-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-zinc-800 w-4 h-4 cursor-pointer"
                        >

                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-zinc-200">{{ $usuario->name }}</span>
                            <span class="text-xs text-zinc-400">{{ $usuario->email }}</span>
                        </div>

                    </label>
                @endforeach
            </div>
            <hr class="mt-2 mb-2">
            <div class="flex justify-end px-6">
                <flux:button wire:click="guardarDerivacion" wire:loading.attr="disabled">
                    <flux:icon.loading wire:loading />
                    <i class="fa-solid fa-floppy-disk" wire:loading.remove></i>
                    <span class="ml-3">Guardar</span>
                </flux:button>
            </div>

        </div>

    </div>

</div>
