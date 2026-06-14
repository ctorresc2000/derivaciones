<?php

namespace App\Livewire\Estudiante;

use App\Mail\CodigoFirmaMail;
use App\Models\Curso;
use App\Models\Entrevista;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class EntrevistaComponent extends Component
{
    public $curso_id, $estudiante_id, $es_apoderado = false, $nombre_apoderado;
    public $motivo, $detalle, $firma;
    public $fecha;
    public $estudiantes = [];
    public $mejorando = false;
    //public $cursos=[];

    // Nuevas propiedades para OTP
    public $email_otp;
    public $codigo_ingresado;
    public $otp_verificado = false;
    public $mostrar_campo_codigo = false;
    public $modalFirma = false;

    public function mount()
    {
        // Asigna la fecha actual al cargar el formulario
        $this->fecha = now()->format('Y-m-d');
    }

    public function enviarCodigoOTP()
    {
        $this->validate(['email_otp' => 'required|email']);

        $codigo = rand(100000, 999999);
        Cache::put('otp_' . $this->email_otp, $codigo, now()->addMinutes(30));

        // ENVÍO REAL
        try {
            //Mail::to($this->email_otp)->send(new CodigoFirmaMail($codigo));
            Mail::to($this->email_otp)->send(new CodigoFirmaMail($codigo, $this->detalle));

            $this->mostrar_campo_codigo = true;
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Correo Enviado',
                'text' => 'Se ha enviado el código a ' . $this->email_otp,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error de envío',
                'text' => 'No se pudo enviar el correo. Revisa la configuración .env',
            ]);
        }
    }

    public function validarCodigoOTP()
    {
        $codigoGuardado = Cache::get('otp_' . $this->email_otp);

        if ($codigoGuardado && $this->codigo_ingresado == $codigoGuardado) {
            $this->otp_verificado = true;
            $this->mostrar_campo_codigo = false;
            // Opcional: Podrías concatenar esto al detalle o guardarlo en un campo nuevo
            $this->detalle .= "\n\n--- Validado mediante OTP al correo: {$this->email_otp} ---";

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Correo Validado']);
        } else {
            $this->addError('codigo_ingresado', 'El código es incorrecto o ha expirado.');
        }
    }

    public function updatedCursoId($value)
    {
        // Al cambiar el curso, cargamos sus estudiantes
        $this->estudiantes = Estudiante::where('curso_id', $value)->get();
        $this->estudiante_id = null;
    }

    public function guardar()
    {

       // dd($this->curso_id, $this->estudiante_id, $this->es_apoderado, $this->nombre_apoderado, $this->motivo, $this->detalle, $this->fecha, $this->firma);
        $this->validate([
            'curso_id' => 'required',
            'estudiante_id' => 'required',
            'motivo' => 'required',
            'es_apoderado' => 'boolean',
            // REGLA CLAVE: requerido si es_apoderado es true
            'nombre_apoderado' => 'required_if:es_apoderado,true',
            'detalle' => 'required',
            'fecha' => 'required',
            //'firma' => 'required', // Obligamos a firmar
        ], [
            // Mensaje personalizado para que sea claro para el usuario
            'nombre_apoderado.required_if' => 'El nombre del apoderado es obligatorio si marcó la casilla.',
        ]);

        $nombreGuardar = $this->es_apoderado ? $this->nombre_apoderado : null;

        Entrevista::create([
            'curso_id' => $this->curso_id,
            'estudiante_id' => $this->estudiante_id,
            'user_id' => auth()->id(),
            'es_apoderado' => $this->es_apoderado,
            'nombre_apoderado' => $nombreGuardar,
            'motivo' => $this->motivo,
            'detalle' => $this->detalle,
            'fecha' => $this->fecha,
            'firma' => $this->firma,

            'otp_codigo'       => $this->otp_verificado ? Cache::get('otp_' . $this->email_otp) : null,
            'otp_email'        => $this->otp_verificado ? $this->email_otp : null,
            'otp_verified_at'  => $this->otp_verificado ? now() : null,
        ]);

        if($this->otp_verificado) {
            \Illuminate\Support\Facades\Cache::forget('otp_' . $this->email_otp);
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Entrevista Guardada Exitósamente',
            'timer' => 1500
        ]);

        return redirect()->route('cardex');
    }

    public function updatedEsApoderado($value)
    {
        if (!$value) {
            $this->nombre_apoderado = null;
        }
    }

    public function procesarGuardado()
    {
        // Disparamos un evento para que el navegador capture la firma
        $this->dispatch('solicitar-firma');
    }

    // Esta función recibirá la firma desde el JS
    public function recibirFirmaYGuardar($base64)
    {
        $this->firma = $base64;
        $this->guardar(); // Llama a tu función original que crea el registro
    }

    public function render()
    {

        return view('livewire.estudiante.entrevista-component', [
            'cursos' => Curso::orderBy('curso')->get(), // Verifica que 'nombre' sea el campo real
        ]);
    }

    public function mejorarTextoIA()
    {
        if (empty($this->detalle)) return;
        $this->mejorando = true;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // Modelo actualizado y vigente
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un corrector de estilo profesional para reportes escolares.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Mejora la redacción y ortografía de este texto, manteniéndolo formal: ' . $this->detalle
                    ]
                ],
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $this->detalle = $response->json()['choices'][0]['message']['content'];
                $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
            } else {
                // Si Groq devuelve error, aquí veremos qué modelo sugiere usar
                $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido';
                throw new \Exception($errorDetail);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Fallo la IA', 'text' => $e->getMessage()]);
        }

        $this->mejorando = false;
    }

    public function abrirModalFirma()
    {
        $this->modalFirma = true;
        $this->dispatch('modal-firma-abierto');
    }
}
