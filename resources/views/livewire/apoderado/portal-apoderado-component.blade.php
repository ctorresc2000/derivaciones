{{-- <div class="py-12 mx-auto sm:px-6 lg:px-8"> --}}
<div>

    @if($paso == 1)
        <div class="w-sm mx-auto">
            <flux:card class="space-y-6">
                <div class="text-center">
                    <flux:heading size="xl">Ingreso de Apoderados</flux:heading>
                    <flux:subheading>Ingrese el RUT del Estudiante.</flux:subheading>
                </div>

                <div class="space-y-4">
                    <flux:input
                        wire:model.defer="rutEstudiante"
                        label="RUT del estudiante"
                        placeholder="Ej: 211234567"
                        mask:dynamic="$input.replace(/[\.\-]/g, '').length > 9 ? '999.999.999-*' : ($input.replace(/[\.\-]/g, '').length > 8 ? '99.999.999-*' : '9.999.999-*')"
                    />

                    <flux:button wire:click="ingresar" variant="primary" class="w-full">
                        Ingresar
                    </flux:button>
                </div>
            </flux:card>
        </div>
    @endif

    @if($paso == 2)
        <div>

            <flux:card class="w-full !max-w-7xl space-y-6 mb-8">
                <div class="flex justify-between items-center border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <flux:heading size="xl">Actualizar Ficha del Estudiante</flux:heading>
                    <flux:button wire:click="$set('paso', 1)" variant="ghost" size="sm">Volver</flux:button>
                </div>

                {{-- Datos fijos que no se pueden modificar --}}
                <div class="flex gap-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-300 rounded-lg text-sm border border-indigo-100 dark:border-indigo-800/30">
                    <div><span class="font-bold opacity-75 mr-1"><i class="fa-solid fa-id-card"></i> RUT:</span> {{ $estudiante->rut }}</div>
                    <div><span class="font-bold opacity-75 mr-1"><i class="fa-solid fa-graduation-cap"></i> Curso Actual:</span> {{ $estudiante->curso->curso ?? 'Sin curso asignado' }}</div>
                </div>

                {{-- Formulario Editable del Estudiante --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="datosEstudiante.nombre" label="Nombres del Estudiante" />

                    <flux:input wire:model="datosEstudiante.apellido" label="Apellidos del Estudiante" />

                    <flux:input wire:model="datosEstudiante.social" label="Nombre Social (Opcional)" placeholder="Si aplica" />

                    <flux:input type="date" wire:model="datosEstudiante.fecha_nacimiento" label="Fecha de Nacimiento" />

                    <flux:input type="email" wire:model="datosEstudiante.email" label="Correo Electrónico (Estudiante)" />

                    <flux:input wire:model="datosEstudiante.telefono" label="Teléfono (Estudiante)" placeholder="Ej: +56912345678" />

                    <div class="md:col-span-2">
                        <flux:input wire:model="datosEstudiante.domicilio" label="Dirección / Domicilio Completo" placeholder="Calle, Número, Comuna..." />
                    </div>
                </div>
            </flux:card>

            <flux:heading size="lg">Registro de Apoderados</flux:heading>

            @foreach($apoderados as $index => $apoderado)
                <flux:card class="relative mt-4">

                    @if(count($apoderados) > 1)
                        <div class="absolute top-4 right-4">
                            <flux:button wire:click="removerApoderado({{ $index }})" variant="danger" size="sm" icon="trash" />
                        </div>
                    @endif

                    <flux:heading size="md" class="mb-6 border-b pb-2">Apoderado {{ $index + 1 }}</flux:heading>
                    <flux:text>Al ingresar su rut, si este existe, aparecerán sus datos</flux:text>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <flux:input
                            wire:model.lazy="apoderados.{{ $index }}.rut"
                            wire:blur="buscarApoderadoBD({{ $index }})"
                            label="RUT (Presione Tab para autocompletar)"
                            mask:dynamic="$input.replace(/[\.\-]/g, '').length > 9 ?  '999.999.999-*' : '99.999.999-*'"
                        />

                        <flux:input
                            wire:model="apoderados.{{ $index }}.apoderado"
                            label="Nombre Completo"
                        />

                        <flux:input
                            wire:model="apoderados.{{ $index }}.telefono"
                            label="Teléfono"
                        />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <flux:input
                            type="email"
                            wire:model="apoderados.{{ $index }}.correo"
                            label="Correo Electrónico"
                        />


                        <flux:input
                            wire:model="apoderados.{{ $index }}.direccion"
                            label="Dirección"
                        />

                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                        <div class=" bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
                            <flux:input
                                type="file"
                                wire:model="apoderados.{{ $index }}.carnet"
                                label="Fotografía del Carnet"
                                accept="image/*"
                            />

                            <div wire:loading wire:target="apoderados.{{ $index }}.carnet" class="mt-2 text-sm text-indigo-500">
                                Cargando imagen...
                            </div>
                        </div>

                       @if(isset($apoderados[$index]['carnet']))
                            @if(!is_string($apoderados[$index]['carnet']))
                                <div class="mt-4">
                                    <span class="text-xs text-indigo-500 font-bold block mb-1">Previsualización (Sin guardar):</span>
                                    <img src="{{ $apoderados[$index]['carnet']->temporaryUrl() }}" class="h-32 w-auto object-cover rounded-md shadow-sm border border-zinc-200">
                                </div>

                            @else
                                <div class="mt-4">
                                    <span class="text-xs text-gray-500 block mb-1">Carnet Actual:</span>
                                    {{-- <a href="{{ asset('storage/' . $apoderados[$index]['carnet']) }}" target="_blank" rel="noopener noreferrer" title="Ver imagen en tamaño completo"> --}}
                                        <img src="{{ asset('storage/' . $apoderados[$index]['carnet']) }}" class="h-32 w-auto object-cover rounded-md shadow-sm border border-zinc-200 hover:opacity-80 transition-opacity cursor-pointer">
                                    {{-- </a> --}}
                                </div>
                            @endif
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
            <flux:button wire:click="agregarApoderado" variant="subtle" icon="plus">
                Agregar otro Apoderado
            </flux:button>

            <flux:button wire:click="guardar" variant="primary" icon="check">
                Guardar Registros
            </flux:button>
        </div>
    @endif
</div>
