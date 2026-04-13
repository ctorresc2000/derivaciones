<div>
    <h1 class="text-3xl mt-4 mb-4">Entrevistas</h1>
    <hr class="mb-6">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 items-center">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <strong>Entrevistado Por:</strong> {{ Auth::user()->name }}
            </h3>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                <i class="fa-solid fa-calendar"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <div class="mb-6">
                    <flux:input type="date" label="Fecha de la Entrevista" wire:model="fecha" />
                    @error('fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </h3>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-6 items-center">
            <flux:select label="Curso" wire:model.live="curso_id">
                <flux:select.option value="">Seleccione un curso...</flux:select.option>
                @foreach($cursos as $curso)
                    <flux:select.option value="{{ $curso->id }}">{{ $curso->curso }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Selector de Estudiante (aparece solo si hay curso) --}}
            @if($curso_id)
                <flux:select label="Estudiante" wire:model="estudiante_id">
                    <flux:select.option value="">Seleccione estudiante...</flux:select.option>
                    @foreach($estudiantes as $est)
                        <flux:select.option value="{{ $est->id }}">{{ $est->nombre }} {{ $est->apellido }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 items-center">

            {{-- Check de Apoderado --}}
            <flux:checkbox label="¿Es entrevista a apoderado?" wire:model.live="es_apoderado" />

            @if($es_apoderado)
                <flux:input label="Nombre del Apoderado" wire:model="nombre_apoderado" />
                @error('nombre_apoderado')
                    <span class="text-red-500 text-xs font-bold">{{ $message }}</span>
                @enderror
            @endif
    </div>
    <div class="mb-6">
        {{-- Motivo y Detalle --}}
        <flux:select label="Motivo" wire:model="motivo">
            <flux:select.option value="">Seleccione Opción...</flux:select.option>
            <flux:select.option value="Solicitud apoderado">Solicitud apoderado</flux:select.option>
            <flux:select.option value="Solicitud estudiante">Solicitud estudiante</flux:select.option>
            <flux:select.option value="Conductual">Conductual</flux:select.option>
            <flux:select.option value="Asistencia">Asistencia</flux:select.option>
            <flux:select.option value="Atrasos">Atrasos</flux:select.option>
            {{-- ... resto de opciones --}}
        </flux:select>
    </div>
    <div class="mb-6">
        <flux:textarea label="Detalle de la entrevista" wire:model="detalle" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-blue-50/50 rounded-lg border border-blue-100">
        <div>
            <flux:input
                type="email"
                label="Correo para validación (Opcional)"
                placeholder="ejemplo@correo.com"
                wire:model="email_otp"
                :disabled="$otp_verificado"
            />
            @error('email_otp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-end">
            @if(!$otp_verificado)
                <flux:button variant="filled" wire:click="enviarCodigoOTP">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Enviar Código
                </flux:button>
            @else
                <div class="flex items-center text-green-600 font-bold">
                    <i class="fa-solid fa-check-double mr-2"></i> Correo Validado
                </div>
            @endif
        </div>

        @if($mostrar_campo_codigo)
            <div class="col-span-1 md:col-span-2 mt-2 p-4 bg-white rounded border border-blue-200 shadow-sm">
                <flux:input
                    label="Ingrese el código de 6 dígitos"
                    placeholder="000000"
                    wire:model="codigo_ingresado"
                />
                @error('codigo_ingresado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <flux:button variant="primary" size="sm" class="mt-2" wire:click="validarCodigoOTP">
                    Verificar Código
                </flux:button>
            </div>
        @endif
    </div>


    {{-- AREA DE FIRMA --}}
    <div class="space-y-6">
    {{-- AREA DE FIRMA --}}
        <div class="border rounded-lg p-4 bg-white" wire:ignore> {{-- wire:ignore es vital para que Livewire no borre el dibujo --}}
            <label class="block text-sm font-bold mb-2">Firma del Entrevistado</label>
            <div class="w-full h-32 touch-none bg-zinc-50 border border-dashed border-slate-200 rounded-md">
                <canvas id="signature-pad" class="w-full h-full"></canvas>
            </div>
            <div class="mt-2 flex gap-2">
                <flux:button size="sm" type="button" onclick="signaturePad.clear()">Borrar Firma</flux:button>
            </div>
        </div>

        {{-- Botón de Guardar --}}
        <flux:button variant="primary" wire:click="procesarGuardado">
            Guardar Entrevista
        </flux:button>
    </div>

</div>


    {{-- Scripts de Firma --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePad;

    function initSignature() {
        const canvas = document.getElementById('signature-pad');
        if (!canvas) return;

        // Esto ajusta el dibujo a la resolución real de la pantalla (Retina/4K)
        const ratio = Math.max(window.devicePixelRatio || 1, 1);

        // USAR offsetWidth para capturar el tamaño del DIV contenedor
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
    }

    // Inicializar al cargar
    document.addEventListener('DOMContentLoaded', initSignature);

    // Escuchar la orden de Livewire
    window.addEventListener('solicitar-firma', event => {
        if (signaturePad.isEmpty()) {
            alert("Debe firmar para continuar");
            return;
        }

        // Obtenemos la imagen original
        const dataURL = signaturePad.toDataURL();

        // ENVIAR AL COMPONENTE
        @this.recibirFirmaYGuardar(dataURL);
    });
</script>

</div>
