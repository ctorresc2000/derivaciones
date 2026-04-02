<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Profesional</h1>
    <hr class="mb-3">

    <div class="flex justify-end px-6">
        <flux:button wire:click="$set('abrirModal', true)">
            <i class="fa-solid fa-circle-plus"></i><span class="ml-3">Nuevo Profesional</span>
        </flux:button>
    </div>

    <hr  class="mt-3 mb-3">


    @livewire('tablas.profesional-table')

    <flux:modal wire:model="abrirModal" :dismissible="false" :closable="false" class="md:w-1/2" max-width="7xl">

        <flux:card>

            <flux:heading size="4xl">
                Nuevo Profesional
            </flux:heading>

            <flux:text class="text-gray-500 mb-4">
                Registra un nuevo profesional en el sistema.
            </flux:text>

            <form wire:submit="guardar" class="space-y-4">

                <flux:input
                    label="Nombre del profesional"
                    wire:model="nombre"
                    placeholder="Nombre del profesional"
                />
                <flux:text  class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">
                    Tipo de Profesión
                </flux:text>

                <flux:select wire:model="tipo">
                    <option value="">Seleccione</option>
                    @foreach($tipo_profesiones as $tipo_profesion)
                        <option value="{{ $tipo_profesion->id }}">{{ $tipo_profesion->tipo }}</option>
                    @endforeach
                </flux:select>

                {{-- <flux:input
                    label="Tipo de profesional"
                    wire:model="tipo"
                    placeholder="Ej. Psicólogo, Orientador, Inspector, etc."
                /> --}}

                <flux:input
                    label="Email de Profesional"
                    type="email"
                    wire:model="email"
                    placeholder="email de profesional"
                />

                <flux:input
                    label="Observaciones"
                    wire:model="observaciones"
                    placeholder="Observaciones"
                />

                <div class="flex justify-end">
                    <div calss="mr-3">
                        {{-- <flux:button variant="danger" wire:click="$set('abrirModal', false)" class="ml-3"> --}}
                        <flux:button variant="danger" wire:click="cerrarModal" class="ml-3">
                            Cerrar
                        </flux:button>
                    </div>
                    @if ($profesionalId)
                        <div class="ml-3">
                            <flux:button wire:click="actualizar" variant="primary">
                                Actualizar
                            </flux:button>
                        </div>
                    @else
                        <div class="ml-3">
                            <flux:button type="submit" variant="primary">
                                Guardar
                            </flux:button>
                        </div>
                    @endif

                </div>

            </form>

        </flux:card>

    </flux:modal >



</div>
