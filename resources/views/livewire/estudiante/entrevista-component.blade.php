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
                <flux:input label="Nombre y Rut del Apoderado separado por guión" wire:model="nombre_apoderado" placeholder="Ej. Juanito Pérez - 12345678-9"/>
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
            <flux:select.option value="Otro">Otro...</flux:select.option>
            {{-- ... resto de opciones --}}
        </flux:select>
    </div>

    {{-- <div class="mb-6">
        <flux:textarea label="Detalle de la entrevista" wire:model="detalle" />
    </div> --}}

    {{-- Botón para mejorar texto con IA --}}
    <div class="mb-6">
    <div class="flex justify-between items-center mb-2">
        <label class="block text-sm font-semibold text-zinc-700">Detalle de la entrevista</label>

        <div class="flex gap-2">
            {{-- Botón de Dictado por Voz --}}
            {{-- <flux:button
                variant="subtle"
                size="sm"
                onclick="toggleDictado()"
                id="btn-dictado"
                class="text-red-600 border-red-200 bg-red-50/50 hover:bg-red-100 transition-colors duration-300"
            >
                <i id="icon-dictado" class="fa-solid fa-microphone mr-1"></i>
                <span id="text-dictado">Dictar</span>
            </flux:button> --}}

            {{-- Botón de Mejorar con IA --}}
            <flux:button
                variant="subtle"
                size="sm"
                wire:click="mejorarTextoIA"
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
    </div>

    <flux:textarea
        id="detalle-entrevista"
        wire:model="detalle"
        placeholder="Escriba el detalle de la entrevista..."
        rows="6"
    />
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

{{-- Botón para abrir el modal de firma
    <div class="mt-4 ml-4">
        <flux:button icon="pencil-square" wire:click="$set('modalFirma', true)" primary variant="filled" color="indigo">
            Abrir Panel de Firma (Pantalla Completa)
        </flux:button>
    </div>
--}}
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
        {{-- <div class="border rounded-lg p-4 bg-white" wire:ignore>
            <label class="block text-sm font-bold mb-2">Firma del Entrevistado</label>
            <div class="w-full h-32 touch-none bg-zinc-50 border border-dashed border-slate-200 rounded-md">
                <canvas id="signature-pad" class="w-full h-full"></canvas>
            </div>
            <div class="mt-2 flex gap-2">
                <flux:button size="sm" type="button" onclick="signaturePad.clear()">Borrar Firma</flux:button>
            </div>
        </div> --}}

        {{-- Botón de Guardar --}}
        <flux:button variant="primary" wire:click="procesarGuardado">
            Guardar Entrevista
        </flux:button>
    </div>

   {{-- Modal de Firma Adaptable (Controlado por Alpine)
    <flux:modal wire:model="modalFirma" class="w-full h-full md:w-[800px] md:h-[600px] p-0">

        <div x-data="firmaDigital()" class="flex flex-col h-full bg-white dark:bg-zinc-900 shadow-2xl">

            <div class="p-4 border-b flex justify-between items-center bg-slate-50 dark:bg-zinc-800">
                <flux:heading size="lg">Panel de Firma Digital</flux:heading>
                <flux:button icon="x-mark" variant="ghost" wire:click="$set('modalFirma', false)" />
            </div>

            <div class="flex-grow p-4 bg-slate-100 dark:bg-zinc-950 overflow-hidden flex flex-col">
                <div x-ref="wrapper" class="flex-grow w-full bg-white border-2 border-dashed border-slate-300 rounded-lg shadow-inner overflow-hidden relative">
                    <canvas x-ref="canvas"
                        class="absolute top-0 left-0 touch-none"
                        style="cursor: crosshair;">
                    </canvas>
                </div>
            </div>

            <div class="p-4 border-t flex gap-4 bg-white dark:bg-zinc-800">
                <flux:button variant="ghost" class="flex-1" @click="limpiarFirma()">
                    <i class="fa-solid fa-eraser mr-2"></i> Limpiar
                </flux:button>
                <flux:button variant="primary" color="green" class="flex-1" @click="guardarFirma()">
                    <i class="fa-solid fa-check mr-2"></i> Confirmar Firma
                </flux:button>
            </div>

        </div>
    </flux:modal>--}}

</div>


    {{-- Scripts de Firma --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
   {{-- Script de Signature Pad --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    {{-- Lógica de Alpine.js --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('firmaDigital', () => ({
                pad: null,
                resizeObserver: null,

                init() {
                    this.pad = new SignaturePad(this.$refs.canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        velocityFilterWeight: 0.7
                    });

                    // El ResizeObserver mide los píxeles REALES del contenedor, ignorando animaciones
                    this.resizeObserver = new ResizeObserver((entries) => {
                        for (let entry of entries) {
                            const width = entry.contentRect.width;
                            const height = entry.contentRect.height;

                            // Si el modal está visible y tiene tamaño, ajustamos
                            if (width > 0 && height > 0) {
                                this.ajustarCanvas(width, height);
                            }
                        }
                    });

                    // Observamos el DIV padre, NO el canvas
                    this.resizeObserver.observe(this.$refs.wrapper);

                    // Limpiamos la firma si el modal se cierra
                    this.$watch('$wire.modalFirma', (abierto) => {
                        if (!abierto) this.pad.clear();
                    });
                },

                ajustarCanvas(width, height) {
                    const canvas = this.$refs.canvas;
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const data = this.pad.toData(); // Guardar trazos previos

                    // 1. EL SECTRETO PARA EVITAR DESFASES: Forzamos el CSS en píxeles duros
                    canvas.style.width = width + 'px';
                    canvas.style.height = height + 'px';

                    // 2. Ajustamos la resolución interna para pantallas HD (Móviles/Retina)
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;

                    // 3. Escalamos el contexto
                    canvas.getContext("2d").scale(ratio, ratio);

                    this.pad.clear();
                    if (data && data.length > 0) {
                        this.pad.fromData(data); // Restauramos trazos
                    }
                },

                limpiarFirma() {
                    this.pad.clear();
                },

                guardarFirma() {
                    if (this.pad.isEmpty()) {
                        Swal.fire('Atención', 'Por favor, realice una firma primero.', 'warning');
                        return;
                    }

                    this.$wire.set('firma_digital', this.pad.toDataURL());
                    this.$wire.set('modalFirma', false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Firma guardada',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            }));
        });

    //Dictdo por voz

    let recognition;
    let dictando = false;

    function toggleDictado() {
        const btn = document.getElementById('btn-dictado');
        const icon = document.getElementById('icon-dictado');
        const label = document.getElementById('text-dictado');
        const textarea = document.getElementById('detalle-entrevista');

        if (!dictando) {
            // Configuración del motor de voz
            window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!window.SpeechRecognition) {
                alert("Tu navegador no soporta dictado por voz. Usa Chrome o Edge.");
                return;
            }

            recognition = new SpeechRecognition();
            recognition.lang = 'es-ES';
            recognition.interimResults = true; // Fundamental para ver resultados previos
            recognition.continuous = true;     // No se detiene en pausas

            recognition.onstart = () => {
                dictando = true;
                // Feedback visual: Botón rojo y parpadeo
                btn.classList.add('bg-red-600', 'text-white');
                icon.classList.add('animate-pulse');
                label.innerText = "Detener";
            };

            recognition.onresult = (event) => {
                let textoFinal = '';

                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    // Solo procesamos los resultados finales para evitar duplicados "fantasma"
                    if (event.results[i].isFinal) {
                        const transcripcion = event.results[i][0].transcript;

                        // Concatenamos al valor actual del textarea
                        const espacio = textarea.value.length > 0 ? ' ' : '';
                        const nuevoTexto = textarea.value + espacio + transcripcion;

                        // Actualización inmediata del DOM
                        textarea.value = nuevoTexto;

                        // Sincronización con la propiedad 'detalle' en Livewire
                        @this.set('detalle', nuevoTexto);
                    }
                }

                // Mantiene el scroll al final si el texto excede el tamaño
                textarea.scrollTop = textarea.scrollHeight;
            };

            recognition.onerror = (event) => {
                console.error("Error en dictado:", event.error);
                if(event.error === 'not-allowed') {
                    alert("Acceso al micrófono denegado. Revisa la configuración de seguridad del navegador.");
                }
                stopDictado();
            };

            recognition.onend = () => {
                // Si el dictado sigue activo pero el motor se apaga (por silencio largo), reiniciamos
                if (dictando) recognition.start();
            };

            recognition.start();
        } else {
            stopDictado();
        }
    }

    function stopDictado() {
        if (recognition) {
            recognition.onend = null; // Evitamos reinicio automático al apagar
            recognition.stop();
        }
        dictando = false;

        const btn = document.getElementById('btn-dictado');
        const icon = document.getElementById('icon-dictado');
        const label = document.getElementById('text-dictado');

        // Restaurar estado visual original
        btn.classList.remove('bg-red-600', 'text-white');
        icon.classList.remove('animate-pulse');
        label.innerText = "Dictar";
    }
</script>

</div>
