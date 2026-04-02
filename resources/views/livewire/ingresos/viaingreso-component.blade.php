<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Via de Ingreso</h1>
    <hr class="mb-3">

    <div class="flex justify-end px-6">
        <flux:button wire:click="$set('abrirModal', true)">
            <i class="fa-solid fa-circle-plus"></i><span class="ml-3">Nueva Via de Ingreso</span>
        </flux:button>
    </div>

    <hr  class="mt-3 mb-3">


    @livewire('tablas.viaingreso-table')

    <flux:modal wire:model="abrirModal" :dismissible="false" :closable="false" class="md:w-1/2" max-width="7xl">

        <flux:card>

            <flux:heading size="4xl">
                Nueva Via de Ingreso
            </flux:heading>

            <flux:text class="text-gray-500 mb-4">
                Registra una nueva via de ingreso en el sistema.
            </flux:text>

            <form wire:submit="guardar" class="space-y-4">

                <flux:input
                    label="Nombre de la via de ingreso"
                    wire:model="nombre"
                    placeholder="Nombre de la via de ingreso"
                />


                <div class="flex justify-end">
                    <div calss="mr-3">
                        {{-- <flux:button variant="danger" wire:click="$set('abrirModal', false)" class="ml-3"> --}}
                        <flux:button variant="danger" wire:click="cerrarModal" class="ml-3">
                            Cerrar
                        </flux:button>
                    </div>
                    <div class="ml-3">
                        <flux:button type="submit" variant="primary">
                            Guardar
                        </flux:button>
                    </div>

                </div>

            </form>

        </flux:card>

    </flux:modal >



</div>
