<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Usuarios</h1>
    <hr class="mb-3">

    <div class="flex justify-end px-6">
        <flux:button wire:click="$set('abrirModal', true)">
            <i class="fa-solid fa-circle-plus"></i><span class="ml-3">Nuevo Usuario</span>
        </flux:button>
    </div>

    <hr  class="mt-3 mb-3">


    @livewire('tablas.user-table')

    <flux:modal wire:model="abrirModal" :dismissible="false" :closable="false" class="md:w-1/2" max-width="7xl">

        <flux:card>

            <flux:heading size="4xl">
                Nuevo Usuario
            </flux:heading>

            <flux:text class="text-gray-500 mb-4">
                Registra un nuevo usuario en el sistema.
            </flux:text>

            <form wire:submit="guardar" class="space-y-4">

                <flux:input
                    label="Nombre del usuario"
                    wire:model="name"
                    placeholder="Nombre del usuario"
                />

                <flux:input
                    label="Email del usuario"
                    wire:model="email"
                    placeholder="email del usuario"
                />

                <flux:select label="Tipo de Profesional" wire:model="tipo_de_profesional">
                    <option value="">Seleccione</option>
                    @foreach ($tipo_profesionales as $tipo_profesional)
                        <option value="{{$tipo_profesional->id}}">{{$tipo_profesional->tipo}}</option>
                    @endforeach
                </flux:select>

                @if (!$userId)

                    <flux:input
                        label="Password del usuario"
                        wire:model="password"
                        placeholder="Password del usuario"
                        type="password"
                        viewable
                    />

                    <flux:input
                        label="Reingrese el password del usuario"
                        wire:model="password_confirmation"
                        placeholder="vuelva a escribir la password del usuario"
                        type="password"
                        viewable
                    />
                @endif


                <flux:select label="Rol" wire:model="rol">
                    <option value="">Seleccione</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Usuario">Usuario</option>
                </flux:select>

                <div class="flex justify-end">
                    <div calss="mr-3">
                        {{-- <flux:button variant="danger" wire:click="$set('abrirModal', false)" class="ml-3"> --}}
                        <flux:button variant="danger" wire:click="cerrarModal" class="ml-3">
                            Cerrar
                        </flux:button>
                    </div>
                    @if ($userId)
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

    <flux:modal  wire:model="cambiarPasword" :dismissible="false" :closable="false" class="md:w-1/2" max-width="7xl">
        <flux:card>
            <div class="mb-4">
                <flux:heading size="4xl">
                    Cambiar Password
                </flux:heading>
            </div>

            <flux:input
                label="Password del usuario"
                wire:model="password"
                placeholder="Password del usuario"
                type="password"
                viewable
            />

            <flux:input
                label="Reingrese el password del usuario"
                wire:model="password_confirmation"
                placeholder="vuelva a escribir la password del usuario"
                type="password"
                viewable
            />

            <div class="flex justify-end mt-4">
                <div calss="mr-3">
                    {{-- <flux:button variant="danger" wire:click="$set('abrirModal', false)" class="ml-3"> --}}
                    <flux:button variant="danger" wire:click="cerrarModal" class="ml-3">
                        Cerrar
                    </flux:button>
                </div>

                <div class="ml-3">
                    <flux:button wire:click="actualizarPassword" variant="primary">
                        Actualizar
                    </flux:button>
                </div>

            </div>
        </flux:card>
    </flux:modal>




</div>
