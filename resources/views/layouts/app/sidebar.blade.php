<!DOCTYPE html>
<html lang="es" class="dark">
    <head>
        @include('partials.head')

    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>
            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            <div class="p-2">
                @livewire('global.selector-anio')
            </div>




            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Principal')" class="grid">
                    <flux:sidebar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        <i class="fa-solid fa-house"></i>
                        <span class="ml-3">Dashboard</span>
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Administración')" class="grid">
                    @if (Auth()->user()->rol=="Administrador")
                        <flux:sidebar.item :href="route('usuarios')" :current="request()->routeIs('usuarios')" wire:navigate>
                            <i class="fa-solid fa-user"></i>
                            <span class="ml-3">Usuarios</span>
                        </flux:sidebar.item>
                    @endif

                        <flux:sidebar.item  :href="route('estudiantes')" :current="request()->routeIs('estudiantes')" wire:navigate>
                            <i class="fa-solid fa-user-graduate"></i>
                            <span class="ml-3">Estudiantes</span>
                        </flux:sidebar.item>

                        {{-- <flux:sidebar.item  :href="route('profesionales')" :current="request()->routeIs('profesionales')" wire:navigate>
                            <i class="fa-solid fa-person"></i>
                            <span class="ml-3">Profesionales</span>
                        </flux:sidebar.item> --}}
                    @if (Auth()->user()->rol=="Administrador")
                        <flux:sidebar.item :href="route('cursos')" :current="request()->routeIs('cursos')" wire:navigate>
                            <i class='fa-solid fa-school'></i>
                            <span class="ml-3">Cursos</span>
                        </flux:sidebar.item>

                        <flux:sidebar.item :href="route('viaingreso')" :current="request()->routeIs('viaingreso')" wire:navigate>
                            <i class="fa-solid fa-arrows-to-circle"></i>
                            <span class="ml-3">Via de Ingreso</span>
                        </flux:sidebar.item>

                        <flux:sidebar.item :href="route('tipoprofesional')" :current="request()->routeIs('tipoprofesional')" wire:navigate>
                            <i class="fa-solid fa-user-tie"></i>
                            <span class="ml-3">Tipo de Profesiones</span>
                        </flux:sidebar.item>

                        <flux:sidebar.item :href="route('apoderados')" :current="request()->routeIs('apoderados')" wire:navigate>
                            <i class="fa-solid fa-restroom"></i>
                            <span class="ml-3">Apoderados</span>
                        </flux:sidebar.item>

                        <flux:sidebar.item :href="route('configuracion')" :current="request()->routeIs('configuracion')" wire:navigate>
                            <i class="fa-solid fa-gear"></i>
                            <span class="ml-3">Configuración</span>
                        </flux:sidebar.item>
                    @endif
                        <flux:sidebar.item icon="magnifying-glass" class="cursor-pointer" x-on:click="$dispatch('abrir-modal-mc')">
                            Consultar Manual (F6)
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('entrevistas')" :current="request()->routeIs('entrevistas')" wire:navigate>
                            <i class="fa-solid fa-book"></i>
                            <span class="ml-3">Entrevistas</span>
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('cardex')" :current="request()->routeIs('cardex')" wire:navigate>
                            <i class="fa-solid fa-address-card"></i>
                            <span class="ml-3">Kardex</span>
                        </flux:sidebar.item>
                </flux:sidebar.group>

                @if (Auth()->user()->rol=="Administrador" || Auth::user()->esTipo('Convivencia'))
                    <flux:sidebar.group :heading="__('Convivencia')" class="grid">

                        <flux:sidebar.item :href="route('faltas')" :current="request()->routeIs('faltas')" wire:navigate>
                            <i class="fa-solid fa-hand"></i>
                            <span class="ml-3">Faltas</span>
                        </flux:sidebar.item>

                        <flux:sidebar.item :href="route('medidas')" :current="request()->routeIs('medidas')" wire:navigate>
                            <i class="fa-solid fa-ruler"></i>
                            <span class="ml-3">Medidas</span>
                        </flux:sidebar.item>

                    </flux:sidebar.group>
                @endif
                @if (Auth()->user()->rol=="Administrador" || Auth::user()->esTipo('Psicosocial'))
                    <flux:sidebar.group :heading="__('Psicosocial')" class="grid">

                        <flux:sidebar.item :href="route('motivointervencion')" :current="request()->routeIs('motivointervencion')" wire:navigate>
                            <i class="fa-solid fa-file-circle-question"></i>
                            <span class="ml-3">Motivos de Intervención</span>
                        </flux:sidebar.item>

                        <flux:sidebar.item :href="route('tipointervencion')" :current="request()->routeIs('tipointervencion')" wire:navigate>
                            <i class="fa-solid fa-person-circle-question"></i>
                            <span class="ml-3">Tipos de Intervención</span>
                        </flux:sidebar.item>

                        <flux:sidebar.item :href="route('redes')" :current="request()->routeIs('redes')" wire:navigate>
                            <i class="fa-solid fa-hospital"></i>
                            <span class="ml-3">Redes de Apoyo</span>
                        </flux:sidebar.item>

                    </flux:sidebar.group>
                @endif

            </flux:sidebar.nav>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Derivaciones')" class="grid">
                    <flux:sidebar.item  :href="route('estudiantesderivados')" :current="request()->routeIs('estudiantesderivados')" wire:navigate>
                        <i class="fa-solid fa-handshake"></i>
                        <span class="ml-3">Derivaciones</span>

                        <flux:badge size="sm" color="red" class="ml-auto">
                            <div class="relative inline-block">
                                <i class="fa-solid fa-bell text-xl text-gray-600"></i>
                                <livewire:contador-notificaciones />
                            </div>
                        </flux:badge>
                    </flux:sidebar.item>

                    <flux:sidebar.item :href="route('intervenciones')" :current="request()->routeIs('intervenciones')" wire:navigate>
                        <i class="fa-regular fa-comments"></i>
                        <span class="ml-3">Intervenciones</span>
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav> --}}

            {{-- <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" /> --}}
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />


            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />


                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}


        <flux:footer class=" border-t text-center py-4 text-sm text-gray-500">
            © {{ date('Y') }} Sistema de Derivaciones - Todos los derechos reservados<br>
            Creado por <strong>Christian Torres</strong> y <strong>Fabián Astorga</strong>
        </flux:footer>


        @fluxScripts

            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('swal', (event) => {
                        Swal.fire(event[0]);
                    });
                });
            </script>

            {{-- Toast de Notificación con Alpine.js --}}
        <div x-data="{ mostrar: false, mensaje: '' }"
             @notificacion.window="mensaje = $event.detail.mensaje; mostrar = true; setTimeout(() => mostrar = false, 3000)"
             x-show="mostrar"
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-5 right-5 z-50 flex items-center gap-3 rounded-lg bg-emerald-500 px-5 py-3 text-white shadow-xl dark:bg-emerald-600">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium text-sm" x-text="mensaje"></span>
        </div>

        {{-- Componente Modal (Debe estar aquí para ser global) --}}
        @livewire('configuracion.consulta-manual-modal')

        <script>
            /**
             * Definimos la función fuera para poder referenciarla
             * tanto en la carga inicial como en las navegaciones.
             */
            function inicializarAtajoF6() {
                // Primero eliminamos cualquier listener previo para evitar duplicados
                window.removeEventListener('keydown', manejarTeclaF6);
                window.addEventListener('keydown', manejarTeclaF6);
            }

            function manejarTeclaF6(e) {
                if (e.key === 'F6') {
                    e.preventDefault();
                    console.log('F6 presionado: Despachando evento...');
                    Livewire.dispatch('abrir-modal-mc');
                }
            }

            // 1. Ejecución inmediata al cargar la página por primera vez
            inicializarAtajoF6();

            // 2. Ejecución cada vez que Livewire cambia de sección (Dashboard, etc.)
            document.addEventListener('livewire:navigated', () => {
                inicializarAtajoF6();
            });
        </script>

        <script>
            window.addEventListener('swal-confirm', event => {
                Swal.fire({
                    title: event.detail[0].title,
                    text: event.detail[0].text,
                    icon: event.detail[0].icon,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: event.detail[0].confirmButtonText,
                    cancelButtonText: event.detail[0].cancelButtonText,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Llamamos al método de Livewire que pasamos en 'method'
                        Livewire.dispatch(event.detail[0].method);
                    }
                });
            });
        </script>

    </body>
</html>
