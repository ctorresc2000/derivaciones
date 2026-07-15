{{-- <x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-2">

            <div class="flex flex-col justify-between h-full overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

                <div class="p-4 w-full">
                    @livewire('graficos.intervenciones-por-profesional')
                </div>
                <a href="#" class="flex items-center justify-center gap-1 w-full py-2 text-center text-sm font-semibold transition-colors
                    /* 👇 COLORES PARA MODO CLARO 👇 */
                    bg-emerald-500 hover:bg-emerald-600 text-white
                    /* 👇 COLORES PARA MODO OSCURO 👇 */
                    dark:bg-emerald-600 dark:hover:bg-emerald-700
                    /* 👆 Usamos emerald-500/600 que coincide perfecto con tu #10b981 👆 */
                ">
                    Más Información
                    <i class="fa-solid fa-circle-arrow-right"></i>
                </a>

            </div>

            <div class="flex flex-col justify-between h-full overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

                <div class="p-4 w-full">
                    @livewire('graficos.derivaciones-por-profesional')
                </div>
                <a href="#" class="flex items-center justify-center gap-1 w-full py-2 text-center text-sm font-semibold transition-colors
                    /* Colores Modo Claro */
                    bg-purple-500 hover:bg-purple-600 text-white
                    /* Colores Modo Oscuro */
                    dark:bg-purple-600 dark:hover:bg-purple-700
                ">
                    Más información
                    <i class="fa-solid fa-circle-arrow-right"></i>
                </a>

            </div>


        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-2">
            <div class="flex flex-col justify-between h-full overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="p-4 w-full">
                    @livewire('graficos.vias-ingreso-intervenciones')
                </div>

                <a href="#" class="flex items-center justify-center gap-1 w-full py-2 text-center text-sm font-semibold transition-colors
                    /* Colores Modo Claro */
                    bg-rose-500 hover:bg-rose-600 text-white
                    /* Colores Modo Oscuro */
                    dark:bg-rose-600 dark:hover:bg-rose-700
                ">
                    Más Información
                    <i class="fa-solid fa-circle-arrow-right"></i>
                </a>
            </div>
            <div class="flex flex-col justify-between h-full overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="p-4 w-full">
                    @livewire('graficos.motivos-derivacion')
                </div>

                <a href="#" class="flex items-center justify-center gap-1 w-full py-2 text-center text-sm font-semibold transition-colors
                    /* Colores Modo Claro */
                    bg-amber-500 hover:bg-amber-600 text-white
                    /* Colores Modo Oscuro */
                    dark:bg-amber-600 dark:hover:bg-amber-700
                ">
                    Más Información
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 ml-1">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm4.28 10.28a.75.75 0 000-1.06l-3-3a.75.75 0 10-1.06 1.06l1.72 1.72H8.25a.75.75 0 000 1.5h5.69l-1.72 1.72a.75.75 0 101.06 1.06l3-3z" clip-rule="evenodd" />
                    </svg>
                </a>

            </div>
        </div>
    </div>

</x-layouts::app> --}}

<x-layouts::app :title="__('Dashboard')">

    @php
        // Consultas rápidas para los números superiores (KPIs)
        $anio = session('anio_activo', date('Y'));
        $totalDerivaciones = \App\Models\Derivarestudiante::whereYear('created_at', $anio)->count();
        $totalPendientes = \App\Models\Derivarestudiante::whereYear('created_at', $anio)->where('estado', 'Pendiente')->count();
        $totalIntervenciones = \App\Models\Intervencion::whereYear('created_at', $anio)->count();
        $estudiantesActivos = \App\Models\Estudiante::where('estado', 'Activo')->count();
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- NIVEL 1: TARJETAS KPI (Indicadores Clave de Rendimiento) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="p-5 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900 flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="fa-solid fa-handshake text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Derivaciones Totales</p>
                    <h3 class="text-2xl font-bold text-neutral-800 dark:text-white">{{ $totalDerivaciones }}</h3>
                </div>
            </div>

            <div class="p-5 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900 flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Casos Pendientes</p>
                    <h3 class="text-2xl font-bold text-neutral-800 dark:text-white">{{ $totalPendientes }}</h3>
                </div>
            </div>

            <div class="p-5 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900 flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-comments text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Intervenciones</p>
                    <h3 class="text-2xl font-bold text-neutral-800 dark:text-white">{{ $totalIntervenciones }}</h3>
                </div>
            </div>

            <div class="p-5 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900 flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="fa-solid fa-user-graduate text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Estudiantes Activos</p>
                    <h3 class="text-2xl font-bold text-neutral-800 dark:text-white">{{ $estudiantesActivos }}</h3>
                </div>
            </div>
        </div>

        {{-- NIVEL 2: GRÁFICOS PRINCIPALES (Los nuevos) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                @livewire('graficos.evolucion-mensual')
            </div>
            <div class="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                @livewire('graficos.estado-derivaciones')
            </div>
        </div>

        {{-- NIVEL 3: ANÁLISIS DETALLADO --}}
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900 p-4" x-data="{ activeTab: 'profesionales' }">

            {{-- Navegación de las Pestañas --}}
            <div class="flex flex-wrap border-b border-neutral-200 dark:border-neutral-700 mb-6 gap-6">
                <button @click="activeTab = 'profesionales'"
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'profesionales', 'border-transparent text-neutral-500 hover:text-neutral-700': activeTab !== 'profesionales' }"
                        class="flex items-center gap-2 py-3 border-b-2 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-user-tie"></i> Gestión de Equipo
                </button>
                <button @click="activeTab = 'motivos'"
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'motivos', 'border-transparent text-neutral-500 hover:text-neutral-700': activeTab !== 'motivos' }"
                        class="flex items-center gap-2 py-3 border-b-2 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-chart-pie"></i> Motivos y Origen
                </button>
                <button @click="activeTab = 'poblacion'"
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'poblacion', 'border-transparent text-neutral-500 hover:text-neutral-700': activeTab !== 'poblacion' }"
                        class="flex items-center gap-2 py-3 border-b-2 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-school"></i> Población Escolar
                </button>
            </div>

            {{-- Contenido de las Pestañas --}}
            <div>

                {{-- PESTAÑA 1: GESTIÓN DE EQUIPO --}}
                <div x-show="activeTab === 'profesionales'" x-transition style="display: none;" class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-2 border border-slate-100 dark:border-zinc-800 rounded-lg bg-white dark:bg-neutral-900">
                            @livewire('graficos.derivaciones-por-profesional')
                        </div>
                        <div class="p-2 border border-slate-100 dark:border-zinc-800 rounded-lg bg-white dark:bg-neutral-900">
                            @livewire('graficos.intervenciones-por-profesional')
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-2 border border-slate-100 dark:border-zinc-800 rounded-lg bg-white dark:bg-neutral-900">
                            @livewire('graficos.estado-por-profesional')
                        </div>
                        <div class="p-2 border border-slate-100 dark:border-zinc-800 rounded-lg bg-white dark:bg-neutral-900">
                            @livewire('graficos.entrevistas-tipo')
                        </div>
                    </div>
                </div>

                {{-- PESTAÑA 2: MOTIVOS Y ORIGEN --}}
                <div x-show="activeTab === 'motivos'" x-transition style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-2 border border-slate-100 dark:border-zinc-800 rounded-lg">
                        @livewire('graficos.motivos-derivacion')
                    </div>
                    <div class="p-2 border border-slate-100 dark:border-zinc-800 rounded-lg">
                        @livewire('graficos.vias-ingreso-intervenciones')
                    </div>
                </div>

                {{-- PESTAÑA 3: POBLACIÓN ESCOLAR --}}
                <div x-show="activeTab === 'poblacion'" x-transition style="display: none;" class="p-2 border border-slate-100 dark:border-zinc-800 rounded-lg">
                    @livewire('graficos.estudiantes-por-curso')
                </div>

            </div>
        </div>
    </div>
</x-layouts::app>
