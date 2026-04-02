<x-layouts::app :title="__('Dashboard')">
    {{-- <div class="p-6">
        @livewire('global.selector-anio')
    </div> --}}
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



            {{-- <div class="p-4 w-full">
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
            </a> --}}
    </div>

</x-layouts::app>
