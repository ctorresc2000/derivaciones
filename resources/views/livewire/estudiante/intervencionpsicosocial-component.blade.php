<div> {{-- ÚNICA RAÍZ --}}

    {{-- 1. BLOQUE DE INFORMACIÓN DEL ESTUDIANTE --}}
    <div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">
        <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                <i class="fa-solid fa-address-card text-lg"></i>
            </div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-white">
                <strong>INTERVENCIÓN PSICOSOCIAL.</strong>
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
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $estudiante->rut ?: 'No registrado' }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-graduation-cap mr-1"></i> Curso
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $estudiante->curso ? $estudiante->curso->curso : 'Sin curso asignado' }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-envelope mr-1"></i> Email
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $estudiante->email ?: 'No registrado' }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-phone mr-1"></i> Teléfono
                </span>
                <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $estudiante->telefono ?: 'No registrado' }}</span>
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

    {{-- 2. BLOQUE DE REGISTRO --}}
    <div class="p-5 bg-slate-50 dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-inner my-2 mx-4">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 items-center">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                    <strong>Intervenido Por:</strong> {{ Auth::user()->name }}
                </h3>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <i class="fa-solid fa-calendar"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                    <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
                </h3>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div x-data="{}" @pedir-nueva-via.window="
                Swal.fire({
                    title: 'Nueva Vía de Ingreso',
                    text: 'Escribe la nueva vía de ingreso:',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Guardar'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $wire.guardarNuevaVia(result.value);
                    } else {
                        $wire.set('via_ingreso_id', '');
                    }
                });
            ">
                <flux:select label="Via de Ingreso:" wire:model.live="via_ingreso_id">
                    <option value="">Selecciona una vía</option>
                    @foreach($viaingresos as $via)
                        <option value="{{ $via->id }}">{{ $via->via_ingreso }}</option>
                    @endforeach
                    <option value="otro">Otro...</option>
                </flux:select>
            </div>
            <flux:input type="file" wire:model="archivos" label="Adjuntar documento" multiple />
        </div>

        {{-- TABLA DINÁMICA --}}
        <div class="mb-6 bg-slate-50 dark:bg-zinc-900/50 p-5 rounded-lg border border-slate-200 dark:border-zinc-800">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4 border-b pb-2">
                <i class="fa-solid fa-hand-holding-heart mr-1"></i> Registro de Motivos y Atención
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-4 mb-6 items-end">
                <flux:select label="Motivo Intervención" wire:model="motivo_seleccionado_id">
                    <option value="">Selecciona Motivo</option>
                    @foreach($motivos as $motivo)
                        <option value="{{ $motivo->id }}">{{ $motivo->motivo }}</option>
                    @endforeach
                </flux:select>

                <flux:select label="Tipo de Atención" wire:model="tipo_seleccionado_id">
                    <option value="">Selecciona Tipo</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                    @endforeach
                </flux:select>

                <flux:button variant="primary" wire:click="agregarDato">
                    <i class="fa-solid fa-plus"></i><span class="ml-2">Agregar</span>
                </flux:button>
            </div>

            @if(count($listaDatosAgregados) > 0)
                <div class="rounded-lg border overflow-hidden bg-white dark:bg-zinc-800">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-slate-100 dark:bg-zinc-900/80">
                            <tr>
                                <th class="px-6 py-3">Motivo</th>
                                <th class="px-6 py-3 border-l">Tipo de Atención</th>
                                <th class="px-6 py-3 text-center border-l">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listaDatosAgregados as $index => $item)
                                <tr class="border-b dark:border-zinc-700 hover:bg-slate-50">
                                    <td class="px-6 py-3">{{ $item['motivo_nombre'] }}</td>
                                    <td class="px-6 py-3 border-l">{{ $item['tipo_nombre'] }}</td>
                                    <td class="px-6 py-3 text-center border-l">
                                        <flux:button size="sm" variant="subtle" wire:click="eliminarDato({{ $index }})" class="text-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </flux:button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-slate-400 border-2 border-dashed rounded-lg">
                    <p>Aún no has agregado motivos.</p>
                </div>
            @endif
        </div>

        <div class="mb-6">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2 border-b pb-2">
                <i class="fa-solid fa-paragraph"></i> Descripción
            </h3>
            <flux:textarea wire:model="descripcion_derivacion" placeholder="Escriba aquí..." />
        </div>

        {{-- FOOTER / NOTIFICACIONES --}}
        <div class="mt-4 p-4 rounded-lg bg-zinc-800/50 border border-zinc-700/50">
            <h3 class="text-sm font-semibold text-white mb-3">Enviar copia a:</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($listaUsuarios as $usuario)
                    <label class="flex items-center gap-3 p-2 rounded-md hover:bg-zinc-700/30 cursor-pointer border border-transparent hover:border-zinc-600 transition-colors">
                        <input type="checkbox" wire:model="usuariosSeleccionados" value="{{ $usuario->id }}" class="rounded bg-zinc-800 text-blue-500">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-zinc-200">{{ $usuario->name }}</span>
                            <span class="text-xs text-zinc-400">{{ $usuario->email }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
            <div class="flex justify-end mt-4 pt-4 border-t border-zinc-700">
                <flux:button wire:click="guardarDerivacion" wire:loading.attr="disabled">
                    <flux:icon.loading wire:loading />
                    <i class="fa-solid fa-floppy-disk" wire:loading.remove></i>
                    <span class="ml-3">Guardar</span>
                </flux:button>
            </div>
        </div>
    </div>

</div> {{-- FIN ÚNICA RAÍZ --}}
