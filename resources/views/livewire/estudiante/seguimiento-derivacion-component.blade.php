<div>
    <flux:modal wire:model="abrirModal" :dismissible="false" :closable="false" class="w-full max-w-5xl">

        <flux:card class="w-full max-w-none">
            {{-- Encabezado --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <i class="fa-solid fa-notes-medical text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                    Seguimiento de Derivación
                </h3>
            </div>

            {{-- Zona para Registrar Nueva Acción --}}
            <div class="mb-6 p-4 bg-slate-50 dark:bg-neutral-900 rounded-xl border border-slate-200 dark:border-neutral-700">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Registrar nueva acción ({{ now()->format('d-m-Y') }}):</label>
                <div class="flex justify-end">
                    <flux:button
                        variant="subtle"
                        size="sm"
                        wire:click="mejorarTextoIAseguimiento"
                            wire:loading.attr="disabled"
                            class="text-indigo-600 border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100"
                        >
                        <span wire:loading.remove wire:target="mejorarTextoIA">
                            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Mejorar con IA
                        </span>
                        <span wire:loading wire:target="mejorarTextoIA">
                            <i class="fa-solid fa-spinner animate-spin mr-2"></i> Procesando...
                        </span>
                    </flux:button>
                </div>
                <flux:textarea wire:model="descripcion_accion" rows="3" placeholder="Escriba aquí los detalles de la nueva acción realizada..."></flux:textarea>
                @error('descripcion_accion') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror

                <div class="flex justify-end mt-3">
                    <flux:button variant="primary" wire:click="guardarAccion">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Guardar Acción
                    </flux:button>
                </div>
            </div>

            {{-- Zona del Historial --}}
            <div class="mt-6 border-t border-slate-200 dark:border-neutral-700 pt-4">
                <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-4"><i class="fa-solid fa-clock-rotate-left mr-2"></i> Historial de Acciones Previas</h4>

                @if(count($historialAcciones) > 0)
                    <div class="space-y-4 max-h-72 overflow-y-auto pr-2">
                        @foreach($historialAcciones as $accion)
                            <div class="bg-white dark:bg-neutral-800 p-4 rounded-lg border border-slate-200 dark:border-neutral-700 shadow-sm">
                                <div class="flex justify-between items-center text-sm text-slate-500 mb-2 border-b border-slate-100 dark:border-neutral-700 pb-2">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">
                                        <i class="fa-solid fa-user-pen mr-1"></i> {{ $accion->usuario->name ?? 'Usuario Desconocido' }}
                                    </span>
                                    <span>
                                        <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($accion->fecha)->format('d-m-Y') }}
                                    </span>
                                </div>
                                <p class="text-slate-800 dark:text-slate-200 text-sm whitespace-pre-line">
                                    {{ $accion->descripcion }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 bg-slate-50 dark:bg-neutral-900 rounded-lg border border-dashed border-slate-300 dark:border-neutral-700">
                        <i class="fa-solid fa-folder-open text-slate-400 text-3xl mb-2"></i>
                        <p class="text-sm text-slate-500 italic">Aún no hay registros de seguimiento para esta derivación.</p>
                    </div>
                @endif
            </div>

        </flux:card>

        <div class="flex justify-end mt-4">
            <flux:button variant="danger" wire:click="cerrarModal">
                Cerrar Ventana
            </flux:button>
        </div>

    </flux:modal>
</div>
