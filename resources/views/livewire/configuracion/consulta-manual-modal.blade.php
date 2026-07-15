<div>
    <flux:modal wire:model="abierto" class="md:w-[700px]">
        <div class="space-y-6">
            <flux:heading size="lg">Consulta Asistida por IA</flux:heading>

            {{-- LO NUEVO: Selector de Motor IA --}}
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <flux:radio.group wire:model="tipoConsulta" label="¿Qué necesitas saber?" variant="segmented">
                    <flux:radio value="resumen" label="¿Qué dice el manual?" icon="book-open" />
                    <flux:radio value="sancion" label="¿Qué sanción aplicar?" icon="exclamation-triangle" />
                </flux:radio.group>
            </div>
            <div>
                <flux:radio.group wire:model="motor" label="Motor de Inteligencia Artificial" variant="segmented">
                    <flux:radio value="gemini" label="Gemini" icon="sparkles" />
                    <flux:radio value="groq" label="Groq" icon="bolt" />
                </flux:radio.group>
            </div>

            <flux:textarea
                wire:model="pregunta"
                placeholder="Describe el caso o situación aquí..."
                rows="3"
                wire:keydown.enter="consultar"
            />

            <div class="flex justify-end">
                <flux:button wire:click="consultar" variant="primary">
                    <span wire:loading.remove wire:target="consultar">Consultar</span>
                    <span wire:loading wire:target="consultar"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Analizando...</span>
                </flux:button>
            </div>

            @if($respuesta)
                <div class="p-4 bg-blue-50 dark:bg-zinc-900 border border-blue-200 dark:border-zinc-700 rounded-lg text-sm">
                    <div class="flex items-center gap-2 mb-2">
                        @if($tipoConsulta == 'resumen')
                            <flux:icon.book-open class="text-blue-600" />
                            <strong class="text-blue-800 dark:text-blue-400">Resumen del Manual:</strong>
                        @else
                            <flux:icon.exclamation-triangle class="text-amber-600" />
                            <strong class="text-amber-800 dark:text-amber-400">Orientación de Sanción:</strong>
                        @endif

                        {{-- Pequeño indicador visual de qué IA respondió --}}
                        <span class="ml-auto text-xs font-semibold px-2 py-1 bg-white dark:bg-zinc-800 rounded text-slate-500 border dark:border-zinc-700 uppercase">
                            Respuesta generada por {{ $motor }}
                        </span>
                    </div>
                    <p class="leading-relaxed text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">{{ $respuesta }}</p>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
