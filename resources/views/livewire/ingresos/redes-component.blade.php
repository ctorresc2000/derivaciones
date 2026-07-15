<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Redes de Apoyo</h1>
    <hr class="mb-3">

    <div class="flex justify-end px-6">
        <flux:button wire:click="abrirModal">
            <i class="fa-solid fa-circle-plus"></i><span class="ml-3">Nueva red de apoyo</span>
        </flux:button>
    </div>

    <hr  class="mt-3 mb-3">


    @livewire('tablas.redes-apoyo-table')

    <flux:modal wire:model="modalRedes" :dismissible="false" :closable="false" class="md:w-1/2" max-width="7xl">

        <flux:card>

            <flux:heading size="4xl">
                Nueva Tipo de Falta
            </flux:heading>

            <flux:text class="text-gray-500 mb-4">
                Registra una nueva red de apoyo en el sistema.
            </flux:text>

            <form wire:submit="guardar" class="space-y-4">

                <flux:input
                    label="Red de Apoyo"
                    wire:model="nombre"
                    placeholder="Nombre de la red de apoyo"
                />

                {{-- <flux:input
                    label="Contácto"
                    wire:model="contacto"
                    placeholder="Nombre del contacto"
                />

                <flux:input
                    label="Teléfono"
                    wire:model="telefono"
                    placeholder="Número de teléfono"
                />

                <flux:input
                    label="Email"
                    wire:model="email"
                    placeholder="Dirección de correo electrónico"
                /> --}}



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
