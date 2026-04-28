<div class="w-full py-10">

    <h1 class="text-3xl mt-4 mb-4">Configuración</h1>
    <hr class="mb-3">


    {{-- <span class="font-bold text-white">
     {{ $configuracion ? $configuracion->nombre_institucion : 'Liceo por defecto' }}
    </span> --}}


    <flux:card  class="w-full max-w-none">


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
                        label="Direccion"
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

             <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">

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


            <hr class="mb-3">
            <div class="flex justify-end">

                <div class="ml-3">
                    <flux:button type="submit" variant="primary">
                        Guardar
                    </flux:button>
                </div>


            </div>


        </form>

        <div class="mt-8 p-6 bg-white dark:bg-zinc-900 border rounded-xl shadow-sm">
            <flux:heading size="md">Manual de Convivencia Escolar</flux:heading>
            <flux:text class="mb-4">Sube el PDF del reglamento para que la IA lo aprenda automáticamente.</flux:text>

            <input type="file" id="pdf-upload" accept=".pdf" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"/>

            <div id="status-pdf" class="mt-3 text-sm font-medium text-blue-600 hidden">
                <i class="fa-solid fa-spinner animate-spin mr-2"></i> Procesando contenido del PDF...
            </div>
        </div>

        <div class="my-8 border-t border-gray-200 dark:border-zinc-700"></div>

        <div class="bg-gray-50 dark:bg-zinc-800/50 p-6 rounded-lg border border-gray-100 dark:border-zinc-700">
            <h2 class="text-xl font-bold mb-2">Cargar Datos Base (Excel)</h2>
            <flux:text class="text-gray-500 mb-4">
                Sube el archivo <b>llenado.xlsx</b> para cargar automáticamente todas las vías de ingreso, faltas, medidas, motivos y tipos de intervención en sus respectivas tablas. <i>Los datos duplicados serán omitidos.</i>
            </flux:text>

            <form wire:submit="importarLlenado" class="flex items-end gap-4">
                <div class="flex-1 max-w-md">
                    <flux:input
                        type="file"
                        wire:model="archivoLlenado"
                        accept=".xlsx, .xls, .csv"
                    />
                </div>

                <flux:button type="submit" variant="primary">
                    <i class="fa-solid fa-file-import"></i><span class="ml-2">Importar Excel</span>
                </flux:button>
            </form>
            @error('archivoLlenado') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

    </flux:card>

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
                // Enviar a Livewire
                @this.dispatch('guardarTextoManual', { texto: fullText });
                status.innerText = "✅ Manual procesado y listo.";
            };
            reader.readAsArrayBuffer(file);
        });
    </script>


</div>
