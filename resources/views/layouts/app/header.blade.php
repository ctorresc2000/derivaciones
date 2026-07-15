<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="w-full bg-white dark:bg-zinc-800">
        <flux:header w-full class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            {{-- NAVEGACIÓN DE ESCRITORIO CON DROPDOWNS --}}
            <flux:navbar class="-mb-px max-lg:hidden space-x-4">

                <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    <i class="fa-solid fa-house mr-2 text-zinc-500 dark:text-zinc-400"></i>
                    {{ __('Dashboard') }}
                </flux:navbar.item>

                <flux:dropdown>
                    <flux:navbar.item variant="ghost" icon:trailing="chevron-down">
                        <i class="fa-solid fa-gear mr-2 text-zinc-500 dark:text-zinc-400"></i>
                        Administración
                    </flux:navbar.item>

                    <flux:menu>
                        @if (Auth()->user()->rol=="Administrador")
                            <flux:menu.item :href="route('usuarios')" :current="request()->routeIs('usuarios')" wire:navigate>
                                <i class="fa-solid fa-user mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Usuarios
                            </flux:menu.item>
                        @endif

                        <flux:menu.item :href="route('estudiantes')" :current="request()->routeIs('estudiantes')" wire:navigate>
                            <i class="fa-solid fa-user-graduate mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Estudiantes
                        </flux:menu.item>

                        @if (Auth()->user()->rol=="Administrador")
                            <flux:menu.item :href="route('cursos')" :current="request()->routeIs('cursos')" wire:navigate>
                                <i class='fa-solid fa-school mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400'></i> Cursos
                            </flux:menu.item>

                            <flux:menu.item :href="route('viaingreso')" :current="request()->routeIs('viaingreso')" wire:navigate>
                                <i class="fa-solid fa-arrows-to-circle mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Via de Ingreso
                            </flux:menu.item>

                            <flux:menu.item :href="route('tipoprofesional')" :current="request()->routeIs('tipoprofesional')" wire:navigate>
                                <i class="fa-solid fa-user-tie mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Tipo de Profesiones
                            </flux:menu.item>

                            <flux:menu.item :href="route('apoderados')" :current="request()->routeIs('apoderados')" wire:navigate>
                                <i class="fa-solid fa-restroom mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Apoderados
                            </flux:menu.item>

                            <flux:separator />

                            <flux:menu.item :href="route('configuracion')" :current="request()->routeIs('configuracion')" wire:navigate>
                                <i class="fa-solid fa-gears mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Configuración
                            </flux:menu.item>
                        @endif

                        <flux:menu.item class="cursor-pointer" x-on:click="$dispatch('abrir-modal-mc')">
                            <i class="fa-solid fa-magnifying-glass mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Consultar Manual (F6)
                        </flux:menu.item>

                        <flux:menu.item :href="route('entrevistas')" :current="request()->routeIs('entrevistas')" wire:navigate>
                            <i class="fa-solid fa-book mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Entrevistas
                        </flux:menu.item>

                        <flux:menu.item :href="route('cardex')" :current="request()->routeIs('cardex')" wire:navigate>
                            <i class="fa-solid fa-address-card mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Kardex
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                @if (Auth()->user()->rol=="Administrador" || Auth::user()->esTipo('Convivencia'))
                    <flux:dropdown>
                        <flux:navbar.item variant="ghost" icon:trailing="chevron-down">
                            <i class="fa-solid fa-handshake-angle mr-2 text-zinc-500 dark:text-zinc-400"></i>
                            Convivencia
                        </flux:navbar.item>

                        <flux:menu>
                            <flux:menu.item :href="route('faltas')" :current="request()->routeIs('faltas')" wire:navigate>
                                <i class="fa-solid fa-hand mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Faltas
                            </flux:menu.item>

                            <flux:menu.item :href="route('medidas')" :current="request()->routeIs('medidas')" wire:navigate>
                                <i class="fa-solid fa-ruler mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Medidas
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                @endif

                @if (Auth()->user()->rol=="Administrador" || Auth::user()->esTipo('Psicosocial'))
                    <flux:dropdown>
                        <flux:navbar.item variant="ghost" icon:trailing="chevron-down">
                            <i class="fa-solid fa-brain mr-2 text-zinc-500 dark:text-zinc-400"></i>
                            Psicosocial
                        </flux:navbar.item>

                        <flux:menu>
                            <flux:menu.item :href="route('motivointervencion')" :current="request()->routeIs('motivointervencion')" wire:navigate>
                                <i class="fa-solid fa-file-circle-question mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Motivos de Intervención
                            </flux:menu.item>

                            <flux:menu.item :href="route('tipointervencion')" :current="request()->routeIs('tipointervencion')" wire:navigate>
                                <i class="fa-solid fa-person-circle-question mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Tipos de Intervención
                            </flux:menu.item>

                            <flux:menu.item :href="route('redes')" :current="request()->routeIs('redes')" wire:navigate>
                                <i class="fa-solid fa-hospital mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Redes de Apoyo
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                @endif

                <flux:dropdown>
                    <flux:navbar.item variant="ghost" icon:trailing="chevron-down">
                        <i class="fa-solid fa-share-nodes mr-2 text-zinc-500 dark:text-zinc-400"></i>
                        Derivaciones
                    </flux:navbar.item>

                    <flux:menu>
                        <flux:menu.item :href="route('estudiantesderivados')" :current="request()->routeIs('estudiantesderivados')" wire:navigate class="flex items-center justify-between w-full">
                            <div>
                                <i class="fa-solid fa-handshake mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Estudiantes Derivados
                            </div>
                            <flux:badge size="sm" color="red" class="ml-2">
                                <div class="relative inline-block">
                                    <i class="fa-solid fa-bell text-xs text-red-600 mr-1"></i>
                                    <livewire:contador-notificaciones />
                                </div>
                            </flux:badge>
                        </flux:menu.item>

                        <flux:menu.item :href="route('intervenciones')" :current="request()->routeIs('intervenciones')" wire:navigate>
                            <i class="fa-regular fa-comments mr-2 w-5 text-center text-zinc-500 dark:text-zinc-400"></i> Intervenciones
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                {{-- Opcional: Si necesitas el selector de año de tu sidebar, puedes colocarlo aquí --}}
                <div class="hidden lg:flex items-center mr-4">
                    @livewire('global.selector-anio')
                </div>

                {{-- <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
                <flux:tooltip :content="__('Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('Repository')"
                    />
                </flux:tooltip>
                <flux:tooltip :content="__('Documentation')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits#livewire"
                        target="_blank"
                        :label="__('Documentation')"
                    />
                </flux:tooltip> --}}
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard')  }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    </body>
</html>
