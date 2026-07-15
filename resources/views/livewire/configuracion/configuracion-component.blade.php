<div class="w-full py-10">
    <style>
        /* Evita que los tabs parpadeen al cargar la página */
        [x-cloak] { display: none !important; }
    </style>

    <h1 class="text-3xl mt-4 mb-4">Configuración</h1>
    <hr class="mb-6 border-zinc-200 dark:border-zinc-700">

    {{-- Iniciamos el controlador de Tabs nativo con Alpine.js --}}
    <div x-data="{ tab: 'general' }">

        {{-- ==========================================
             NAVEGACIÓN DE LOS TABS (Botones)
             ========================================== --}}
        <div class="flex p-1 space-x-1 bg-zinc-100 dark:bg-zinc-800/80 rounded-lg w-full sm:w-fit mb-6 border dark:border-zinc-700">
            <button
                @click="tab = 'general'"
                :class="{ 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm ring-1 ring-zinc-900/5': tab === 'general', 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300': tab !== 'general' }"
                class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fa-solid fa-gear mr-2"></i> General
            </button>
            <button
                @click="tab = 'auditoria'"
                :class="{ 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm ring-1 ring-zinc-900/5': tab === 'auditoria', 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300': tab !== 'auditoria' }"
                class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fa-solid fa-clock-rotate-left mr-2"></i> Historial de Cambios
            </button>
        </div>


        {{-- ==========================================
             TAB 1: CONFIGURACIÓN GENERAL Y CARGAS
             ========================================== --}}
        <div x-show="tab === 'general'" x-cloak>
            <flux:card class="w-full max-w-none">
                <form wire:submit="guardar" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        <div>
                            <flux:input
                                label="Institución"
                                wire:model="institucion"
                                placeholder="Institución educativa"
                                />
                        </div>
                        <div>
                            <flux:input
                                label="Dirección"
                                wire:model="domicilio"
                                placeholder="Dirección de la Institución educativa"
                            />
                        </div>
                        <div>
                            <flux:input
                                label="Teléfono"
                                wire:model="telefono"
                                placeholder="Teléfono de la Institución educativa"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-2 gap-3">
                        <div>
                            <flux:input
                                label="Email"
                                wire:model="email"
                                placeholder="Email de la Institución educativa"
                            />
                        </div>
                        <div>
                            @if($logo_actual)
                                <div class="mb-2">
                                    <span class="text-sm text-zinc-400 block mb-1">Logo Actual:</span>
                                    <img src="{{ asset('storage/' . $logo_actual) }}" alt="Logo Institución" class="h-16 w-auto rounded border border-zinc-700">
                                </div>
                            @endif
                            <flux:input
                                type="file"
                                label="Logo de la Institución"
                                wire:model="logo"
                                placeholder="logo de la Institución educativa"
                            />
                        </div>
                    </div>

                    <hr class="mb-3 border-gray-200 dark:border-zinc-700">
                    <div class="flex justify-end">
                        <div class="ml-3">
                            <flux:button type="submit" variant="primary">
                                Guardar
                            </flux:button>
                        </div>
                    </div>
                </form>



                <flux:card class="w-full max-w-none mt-8 !bg-zinc-50 dark:!bg-zinc-900/50">

    <div class="mb-6">
        <flux:heading size="lg">Importación y Carga de Datos</flux:heading>
        <flux:text class="text-sm">Herramientas para alimentar la base de conocimientos del sistema.</flux:text>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Tarjeta 1: PDF de Convivencia --}}
        <div class="flex flex-col justify-between p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div>
                <flux:heading size="md" class="mb-2">Manual de Convivencia Escolar</flux:heading>
                <flux:text class="mb-6 text-sm text-zinc-500">
                    Sube el PDF del reglamento para que la IA lo aprenda automáticamente y pueda generar respuestas o resúmenes basados en él.
                </flux:text>
            </div>

            <div class="mt-auto">
                <input type="file" id="pdf-upload" accept=".pdf" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-blue-400 cursor-pointer transition-colors"/>

                <div id="status-pdf" class="mt-3 text-sm font-medium text-blue-600 hidden">
                    <i class="fa-solid fa-spinner animate-spin mr-2"></i> Procesando contenido del PDF...
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Excel de Datos Base --}}
        <div class="flex flex-col justify-between p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div>
                <flux:heading size="md" class="mb-2">Cargar Datos Base (Excel)</flux:heading>
                <flux:text class="mb-6 text-sm text-zinc-500">
                    Sube el archivo <b>llenado.xlsx</b> para cargar automáticamente todas las vías de ingreso, faltas, medidas, motivos y tipos de intervención. <i>Los duplicados serán omitidos.</i>
                </flux:text>
            </div>

            <div class="mt-auto">
                <form wire:submit="importarLlenado" class="flex flex-col xl:flex-row items-start xl:items-end gap-4">
                    <div class="flex-1 w-full">
                        <flux:input
                            type="file"
                            wire:model="archivoLlenado"
                            accept=".xlsx, .xls, .csv"
                        />
                    </div>

                    <flux:button type="submit" variant="primary" class="w-full xl:w-auto">
                        <i class="fa-solid fa-file-import"></i><span class="ml-2">Importar Excel</span>
                    </flux:button>
                </form>
                @error('archivoLlenado')
                    <span class="text-red-500 text-sm mt-2 block font-medium">{{ $message }}</span>
                @enderror
            </div>
        </div>

    </div>
</flux:card>
            </flux:card>
        </div>


       {{-- ==========================================
             TAB 2: HISTORIAL DE AUDITORÍA
             ========================================== --}}
        <div x-show="tab === 'auditoria'" x-cloak>
            <flux:card class="w-full max-w-none">

                {{-- ENCABEZADO CON FILTROS --}}
                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
                    <flux:heading size="lg">Registro de Cambios del Sistema</flux:heading>

                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">

                        {{-- Filtro por Usuario (NUEVO) --}}
                        <div class="flex items-center gap-2 bg-zinc-50 dark:bg-zinc-800/50 p-2 rounded-lg border dark:border-zinc-700 w-full sm:w-auto">
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                <i class="fa-solid fa-user mr-1"></i> Usuario:
                            </span>
                            <select wire:model.live="usuarioFiltro"
                                    class="w-full sm:w-auto rounded-md border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 py-1.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Todos los usuarios</option>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro por Mes (EXISTENTE) --}}
                        <div class="flex items-center gap-2 bg-zinc-50 dark:bg-zinc-800/50 p-2 rounded-lg border dark:border-zinc-700 w-full sm:w-auto">
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                <i class="fa-regular fa-calendar mr-1"></i> Mes:
                            </span>
                            <input type="month" wire:model.live="mesFiltro"
                                   class="w-full sm:w-auto rounded-md border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 py-1.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">

                            {{-- Botón para limpiar AMBOS filtros al mismo tiempo --}}
                            @if($mesFiltro || $usuarioFiltro)
                                <button wire:click="$set('mesFiltro', ''); $set('usuarioFiltro', '')" class="text-red-500 hover:text-red-700 ml-1" title="Quitar todos los filtros">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- CONTENEDOR CON SCROLL FIJO (max-h-[600px] y overflow-y-auto) --}}
                <div class="max-h-[600px] overflow-y-auto pr-3 rounded-lg" style="scrollbar-width: thin;">

                    @if(count($actividades) > 0)
                        <ul class="space-y-4">
                            @foreach($actividades as $log)
                                {{-- Usamos Alpine para controlar el colapso de cada fila --}}
                                <li x-data="{ open: false }" class="p-4 bg-gray-50 dark:bg-zinc-800/50 border dark:border-zinc-700 rounded-lg text-sm transition-all">

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div>
                                            {{-- FECHA Y HORA --}}
                                            <span class="text-zinc-500 font-mono text-xs bg-zinc-200 dark:bg-zinc-700 px-2 py-1 rounded mr-2">
                                                {{ $log->created_at->format('d/m/Y H:i') }}
                                            </span>

                                            {{-- USUARIO --}}
                                            <strong class="text-blue-600 dark:text-blue-400">
                                                {{ $log->causer ? $log->causer->name : 'El Sistema' }}
                                            </strong>

                                            {{-- ACCIÓN --}}
                                            @if($log->description === 'created')
                                                <span class="text-emerald-600 dark:text-emerald-500 font-semibold mx-1">creó</span>
                                            @elseif($log->description === 'updated')
                                                <span class="text-amber-600 dark:text-amber-500 font-semibold mx-1">actualizó</span>
                                            @elseif($log->description === 'deleted')
                                                <span class="text-red-600 dark:text-red-500 font-semibold mx-1">eliminó</span>
                                            @else
                                                <span class="mx-1">{{ $log->description }}</span>
                                            @endif

                                            {{-- SUJETO --}}
                                            @if($log->subject_type === 'App\Models\Estudiante' && $log->subject)
                                                la ficha del estudiante <strong class="text-zinc-800 dark:text-zinc-200">{{ $log->subject->nombre }} {{ $log->subject->apellido }}</strong>.
                                            @else
                                                un registro en el sistema.
                                            @endif
                                        </div>

                                        {{-- BOTÓN DE DETALLES --}}
                                        @if($log->description === 'updated' && isset($log->properties['old']))
                                            <flux:button size="sm" variant="subtle" x-on:click="open = !open">
                                                <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                <span class="ml-2" x-text="open ? 'Ocultar Detalles' : 'Ver Detalles'"></span>
                                            </flux:button>
                                        @endif
                                    </div>

                                    {{-- PANEL COLAPSABLE CON LOS CAMBIOS EXACTOS --}}
                                    @if($log->description === 'updated' && isset($log->properties['old']))
                                        <div x-show="open" x-collapse x-cloak class="mt-4 pt-4 border-t dark:border-zinc-700">
                                            <p class="font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Valores modificados:</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                @foreach($log->properties['attributes'] as $columna => $nuevoValor)
                                                    @if(isset($log->properties['old'][$columna]) && $log->properties['old'][$columna] != $nuevoValor)
                                                        <div class="bg-white dark:bg-zinc-900 p-3 rounded border dark:border-zinc-800 shadow-sm">
                                                            <span class="block text-xs uppercase text-zinc-500 font-bold mb-1">{{ $columna }}</span>
                                                            <div class="flex items-center gap-2 text-sm">
                                                                <span class="text-red-500 line-through truncate max-w-[45%]">{{ $log->properties['old'][$columna] ?: '(vacío)' }}</span>
                                                                <i class="fa-solid fa-arrow-right text-zinc-400 text-xs"></i>
                                                                <span class="text-emerald-600 dark:text-emerald-400 font-medium truncate max-w-[45%]">{{ $nuevoValor ?: '(vacío)' }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-10 text-zinc-500 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <i class="fa-solid fa-clipboard-check text-4xl mb-3 text-zinc-300"></i>
                            <p>No hay registros de actividad para la fecha seleccionada.</p>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>
    </div> {{-- Script del PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script>
        document.getElementById('pdf-upload').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const status = document.getElementById('status-pdf');
            status.classList.remove('hidden');

            const reader = new FileReader();
            reader.onload = async function() {
                const typedarray = new Uint8Array(this.result);
                const pdfJS = window['pdfjs-dist/build/pdf'];
                pdfJS.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

                const pdf = await pdfJS.getDocument(typedarray).promise;
                let fullText = '';
                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const textContent = await page.getTextContent();
                    fullText += textContent.items.map(s => s.str).join(' ') + '\n';
                }

                @this.dispatch('guardarTextoManual', { texto: fullText });
                status.innerHTML = "<i class='fa-solid fa-check mr-2'></i> Manual procesado y listo.";
            };
            reader.readAsArrayBuffer(file);
        });
    </script>

</div>
