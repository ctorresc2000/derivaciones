<div class="min-h-screen bg-slate-900 flex flex-col items-center justify-center p-4">

    {{-- Tarjeta Estilo App Móvil: Cambiamos a max-h-[90vh] para que no supere la pantalla --}}
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] w-full">

        {{-- Cabecera (Fija: flex-shrink-0) --}}
        <div class="bg-indigo-600 p-6 text-center text-white flex-shrink-0">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-camera text-3xl"></i>
            </div>
            <h2 class="text-xl font-bold">Escáner Móvil</h2>
            <p class="text-indigo-200 text-sm mt-1">
                Entrevista de: <strong>{{ $entrevista->estudiante->nombre }} {{ $entrevista->estudiante->apellido }}</strong>
            </p>
        </div>

        {{-- Cuerpo del Escáner (Con Scroll: overflow-y-auto) --}}
        <div class="p-6 flex-grow flex flex-col items-center justify-center text-center space-y-6 overflow-y-auto">

            <p class="text-slate-500 text-sm">
                Presiona el botón para abrir tu cámara. Puedes tomar múltiples fotos de la entrevista física.
            </p>

            {{-- EL BOTÓN MÁGICO --}}
            <label class="relative cursor-pointer bg-emerald-500 hover:bg-emerald-600 text-white rounded-full w-32 h-32 flex flex-col items-center justify-center shadow-lg transition-transform active:scale-95 flex-shrink-0">
                <i class="fa-solid fa-camera-retro text-4xl mb-2"></i>
                <span class="font-bold text-sm uppercase">Tomar Foto</span>

                {{--
                    accept="image/*" -> Solo permite fotos
                    capture="environment" -> Fuerza a abrir la cámara trasera en celulares
                --}}
                <input type="file" wire:model="fotos_movil" accept="image/*" capture="environment" multiple class="hidden">
            </label>

            {{-- Indicador de Carga --}}
            <div wire:loading wire:target="fotos_movil" class="text-indigo-600 font-bold animate-pulse flex-shrink-0">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Procesando foto...
            </div>

            {{-- Previsualización (Si ya tomó fotos) --}}
            @if(!empty($fotos_movil))
                <div class="w-full bg-slate-50 border rounded-lg p-3 flex-shrink-0">
                    <p class="text-xs font-bold text-slate-500 mb-2 uppercase">Fotos listas para subir: {{ count($fotos_movil) }}</p>
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach($fotos_movil as $foto)
                            <img src="{{ $foto->temporaryUrl() }}" class="h-16 w-16 object-cover rounded shadow border border-slate-200">
                        @endforeach
                    </div>
                </div>
            @endif

            @error('fotos_movil.*')
                <span class="text-red-500 text-xs font-bold flex-shrink-0">{{ $message }}</span>
            @enderror

        </div>

        {{-- Pie de página / Botón de subida (Fijo: flex-shrink-0) --}}
        <div class="p-4 bg-slate-50 border-t flex-shrink-0">
            <button wire:click="subirFotos"
                    @if(empty($fotos_movil)) disabled @endif
                    class="w-full py-4 rounded-xl font-bold text-white transition-colors {{ empty($fotos_movil) ? 'bg-slate-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 shadow-md' }}">
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Subir a la Plataforma
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('cardex') }}" class="text-sm text-slate-500 hover:text-slate-800 underline">
                    Volver al sistema
                </a>
            </div>
        </div>

    </div>
</div>
