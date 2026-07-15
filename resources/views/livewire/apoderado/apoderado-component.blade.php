<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Apoderados</h1>
    <hr class="mb-3">

    <div class="flex justify-end px-6">
        <flux:button wire:click="modalApoderado">
            <i class="fa-solid fa-circle-plus"></i><span class="ml-3">Nuevo Apoderado</span>
        </flux:button>
    </div>

    <hr  class="mt-3 mb-3">
    @livewire('tablas.apoderados-table')

    <flux:modal wire:model="abrirModal" :dismissible="false" :closable="false" class="w-full max-w-7xl">
        @if($creando)

            <flux:card  class="w-full max-w-none">

                <flux:heading size="4xl">
                    Nuevo Apoderado
                </flux:heading>

                <flux:text class="text-gray-500 mb-4">
                    Registra un nuevo Apoderado en el sistema.
                </flux:text>

                <form wire:submit="guardarApoderado" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4  gap-3">

                        <div>
                            <flux:input
                                label="Rut"
                                mask:dynamic="$input.replace(/[\.\-]/g, '').length > 9 ? '999.999.999-*' : ($input.replace(/[\.\-]/g, '').length > 8 ? '99.999.999-*' : '9.999.999-*')"
                                wire:model="rut"
                                placeholder="Rut del apoderado"
                                />
                        </div>
                        <div class="col-span-2">
                            <flux:input
                                label="Nombre y Apellidos del Apoderado"
                                wire:model="name"
                                placeholder="Nombre completo"
                                />
                        </div>
                        <div>
                            <flux:select label="Tipo de Apoderado" wire:model="tipo_apoderado">
                                <option value="">Seleccione</option>
                                    <option value="Titular">Titular</option>
                                    <option value="Suplente">Suplente</option>
                            </flux:select>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <flux:input
                                label="Correo Electrónico"
                                wire:model="email"
                                placeholder="email del apoderado"
                            />
                        </div>
                        <div    >
                            <flux:input
                                label="Dirección"
                                wire:model="domicilio"
                                placeholder="dirección del apoderado"
                            />
                        </div>
                        <div>
                            <flux:input
                                label="Teléfono"
                                wire:model="telefono"
                                placeholder="teléfono del apoderado"
                            />
                        </div>
                    </div>


                        {{-- 👇 LO NUEVO: Input para subir documentos del estudiante 👇 --}}
                        <div class="mt-4 mb-4">
                            <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                                Fotografía Carnet (Carnet del Apoderado)
                            </flux:text>
                            <flux:input type="file" wire:model.live="carnet_apoderado"/>
                            @error('archivo_carnet_apoderado')
                                <span class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            @if ($carnet_apoderado)
                                @if(!is_string($carnet_apoderado))
                                    <img src="{{ $carnet_apoderado->temporaryUrl() }}" alt="Previsualización" class="h-32 w-auto">
                                @else
                                    <div class="mt-4">
                                        <span class="text-xs text-gray-500 block mb-1">Carnet Actual:</span>
                                        {{-- <a href="{{ asset('storage/' . $apoderados[$index]['carnet']) }}" target="_blank" rel="noopener noreferrer" title="Ver imagen en tamaño completo"> --}}
                                            <img src="{{ asset('storage/' . $carnet_apoderado) }}" class="h-32 w-auto object-cover rounded-md shadow-sm border border-zinc-200 hover:opacity-80 transition-opacity cursor-pointer">
                                        {{-- </a> --}}
                                    </div>
                                @endif
                            @endif
                        </div>
                        {{-- 👆 FIN DE LO NUEVO 👆 --}}

                        <div class="flex justify-end mt-4">
                        <div class="mr-3">
                            <flux:button variant="danger" wire:click="cerrarModal" class="ml-3">
                                Cerrar
                            </flux:button>
                        </div>
                        <div class="ml-3">
                            {{-- Usamos type="submit" para no chocar con el formulario --}}
                            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="guardar">
                                Guardar
                            </flux:button>
                        </div>
                    </div>


                </form>

            </flux:card>
        @else

            <flux:card  class="w-full max-w-none">

                <flux:heading size="4xl">
                    Editar Apoderado
                </flux:heading>

                <flux:text class="text-gray-500 mb-4">
                    Modifica Datos del Apoderado en el sistema.
                </flux:text>

                <div class="grid grid-cols-1 md:grid-cols-4  gap-3">

                    <div>
                        <flux:input
                            label="Rut"
                            mask:dynamic="$input.replace(/[\.\-]/g, '').length > 9 ?  '999.999.999-*' : '99.999.999-*'"
                            wire:model="rut"
                            placeholder="Rut del apoderado"
                            />
                    </div>
                    <div class="col-span-2">
                        <flux:input
                            label="Nombre y Apellidos del Apoderado"
                            wire:model="name"
                            placeholder="Nombre completo"
                            />
                    </div>
                    <div>
                        <flux:select label="Tipo de Apoderado" wire:model="tipo_apoderado">
                            <option value="">Seleccione</option>
                                <option value="Titular">Titular</option>
                                <option value="Suplente">Suplente</option>
                        </flux:select>
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <flux:input
                            label="Correo Electrónico"
                            wire:model="email"
                            placeholder="email del apoderado"
                        />
                    </div>
                    <div    >
                        <flux:input
                            label="Dirección"
                            wire:model="domicilio"
                            placeholder="dirección del apoderado"
                        />
                    </div>
                    <div>
                        <flux:input
                            label="Teléfono"
                            wire:model="telefono"
                            placeholder="teléfono del apoderado"
                        />
                    </div>
                </div>


                    {{-- 👇 LO NUEVO: Input para subir documentos del estudiante 👇 --}}
                    @if($creando)
                        <div class="mt-4 mb-4">
                            <flux:text class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                                Fotografía Carnet (Carnet del Apoderado)
                            </flux:text>
                            <flux:input type="file" wire:model.live="carnet_apoderado"/>
                            @error('archivo_carnet_apoderado')
                                <span class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                    <div>
                        @if ($carnet_apoderado)
                            @if(!is_string($carnet_apoderado))
                                <img src="{{ $carnet_apoderado->temporaryUrl() }}" alt="Previsualización" class="h-32 w-auto">
                            @else
                                <div class="mt-4">
                                    <span class="text-xs text-gray-500 block mb-1">Carnet Actual:</span>
                                    {{-- <a href="{{ asset('storage/' . $apoderados[$index]['carnet']) }}" target="_blank" rel="noopener noreferrer" title="Ver imagen en tamaño completo"> --}}
                                        <img src="{{ asset('storage/' . $carnet_apoderado) }}" class="h-32 w-auto object-cover rounded-md shadow-sm border border-zinc-200 hover:opacity-80 transition-opacity cursor-pointer">
                                    {{-- </a> --}}
                                </div>
                            @endif
                        @endif
                    </div>
                    {{-- 👆 FIN DE LO NUEVO 👆 --}}

                    <div class="flex justify-end mt-4">
                        <div class="mr-3">
                            <flux:button variant="danger" wire:click="cerrarModal" class="ml-3">
                                Cerrar
                            </flux:button>
                        </div>
                        <div class="ml-3">
                            {{-- Usamos type="submit" para no chocar con el formulario --}}
                            <flux:button variant="primary" wire:loading.attr="disabled" wire:click="actualizarDatos">
                                Actualizar
                            </flux:button>
                        </div>
                    </div>

            </flux:card>

        @endif

    </flux:modal >

    <flux:modal wire:model="modalEstudiante"  :closable="false" class="w-full max-w-7xl self-start mt-10">
        <flux:card  class="w-full max-w-none">
            <flux:heading size="4xl">
                Asignar Estudiante
            </flux:heading>

            <flux:text class="text-gray-500 mb-4">
                Asigna un nuevo estudiante al Apoderado: <strong>{{$idestudianteSeleccionado?->apoderado}}</strong>
            </flux:text>
            <hr class="mb-4">
            <div>
                @livewire('tablas.asignar-estudiante-table')
            </div>
        </flux:card>

    </flux:modal>
</div>
