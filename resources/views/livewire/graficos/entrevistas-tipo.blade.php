<div class="w-full">
    {!! $chart->container() !!}

    <div class="flex justify-center mt-4">
        <a href="{{ route('cardex') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-address-card"></i>
            Ir al Kardex Completo
        </a>
    </div>

    {{-- <div class="flex justify-center mt-4">
        <button type="button"
                wire:click="cargarDetalles"
                class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-800 rounded-md text-sm font-medium transition-colors">
            Más Información
        </button>
    </div> --}}

    {{-- MODAL GRATUITO (Alpine.js) --}}
    <div x-data="{ open: false }"
         @abrir-modal-detalle.window="open = true"
         x-show="open"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         style="display: none;">

        <div @click.away="open = false" class="bg-white dark:bg-neutral-900 rounded-xl shadow-xl w-full max-w-4xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-neutral-800 dark:text-white">Detalle de Entrevistas</h2>
                <button @click="open = false; window.dispatchEvent(new Event('modal-closed'));"
                        class="text-neutral-500 hover:text-neutral-700">
                    ✕
                </button>
            </div>

            <div class="max-h-[400px] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-neutral-50 dark:bg-neutral-800">
                        <tr>
                            <th class="p-3 text-sm font-semibold text-neutral-600 dark:text-neutral-300">Estudiante</th>
                            <th class="p-3 text-sm font-semibold text-neutral-600 dark:text-neutral-300">Curso</th> <th class="p-3 text-sm font-semibold text-neutral-600 dark:text-neutral-300">Fecha</th>
                            <th class="p-3 text-sm font-semibold text-neutral-600 dark:text-neutral-300">Entrevistador</th>
                            <th class="p-3 text-sm font-semibold text-neutral-600 dark:text-neutral-300">Tipo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($detalleEntrevistas as $item)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                            <td class="p-3 text-sm text-neutral-700 dark:text-neutral-300">{{ $item->estudiante->nombre ?? 'N/A' }}</td>
                            <td class="p-3 text-sm text-neutral-700 dark:text-neutral-300">{{ $item->curso->curso ?? 'N/A' }}</td> <td class="p-3 text-sm text-neutral-700 dark:text-neutral-300">{{ $item->fecha ? $item->fecha->format('d/m/Y') : 'N/A' }}</td>
                            <td class="p-3 text-sm text-neutral-700 dark:text-neutral-300">{{ $item->profesional->name ?? 'N/A' }}</td>
                            <td class="p-3 text-sm text-neutral-700 dark:text-neutral-300">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->es_apoderado ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $item->es_apoderado ? 'Apoderado' : 'Estudiante' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ $chart->cdn() }}"></script>
    {{ $chart->script() }}

    <script>
        // Escucha cuando el modal se cierra para forzar a ApexCharts a recalcular
        window.addEventListener('modal-closed', () => {
            window.dispatchEvent(new Event('resize'));
        });
    </script>
</div>
