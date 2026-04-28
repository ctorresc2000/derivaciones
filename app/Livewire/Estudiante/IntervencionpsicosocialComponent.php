<?php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use App\Models\Motivointervencion;
use App\Models\Tipointervencion;
use App\Models\Intervencion;
use App\Models\Profesional;
use App\Models\User;
use App\Models\Viaingreso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionCopiaMail;
use App\Traits\HasDocuments;
use Illuminate\Support\Facades\Http;

class IntervencionpsicosocialComponent extends Component
{
    use WithFileUploads;
    use HasDocuments;

    public $estudiante;
    public $derivacion_destino;
    public $descripcion_derivacion;
    public $profesionales = [];
    public $viaingresos = [];
    public $archivos;
    public $motivos = [];
    public $tipos = [];
    public $listaUsuarios = [];
    public $usuariosSeleccionados = [];
    public $via_ingreso_id;

    public $motivo_seleccionado_id = '';
    public $tipo_seleccionado_id = '';
    public $listaDatosAgregados = [];
    public $editando_index = null;

    public $mejorando = false;

    public function render()
    {
        return view('livewire.estudiante.intervencionpsicosocial-component');
    }

    public function mount($id)
    {
        $this->estudiante = Estudiante::findOrFail($id);
        $this->profesionales = Profesional::all();
        $this->viaingresos = Viaingreso::all();
        $this->motivos = Motivointervencion::all();
        $this->tipos = Tipointervencion::all();
        $this->listaUsuarios = User::all();
    }

    public function agregarDato()
    {
        // 1. Validar que se seleccionaron ambos IDs
        if (!$this->motivo_seleccionado_id || !$this->tipo_seleccionado_id) {
            return;
        }

        // 2. Buscar directamente en el Modelo (importa los modelos arriba)
        // Esto evita depender de si la colección $this->motivos sigue viva o no
        $motivo = \App\Models\Motivointervencion::find($this->motivo_seleccionado_id);
        $tipo = \App\Models\Tipointervencion::find($this->tipo_seleccionado_id);

        if ($motivo && $tipo) {
            // 3. Agregar al array con las llaves que espera tu Blade
            $this->listaDatosAgregados[] = [
                'motivo_id'     => $this->motivo_seleccionado_id,
                'motivo_nombre' => $motivo->motivo, // Ajusta si la columna se llama diferente
                'tipo_id'       => $this->tipo_seleccionado_id,
                'tipo_nombre'   => $tipo->tipo,   // Ajusta si la columna se llama diferente
                //'detalle'       => $this->detalle_registro,
            ];

            //dd($this->listaDatosAgregados);

            // 4. Limpiar los selectores
            $this->reset(['motivo_seleccionado_id', 'tipo_seleccionado_id']);
        }
    }

    public function quitarDato($index)
    {
        unset($this->listaDatosAgregados[$index]);
        $this->listaDatosAgregados = array_values($this->listaDatosAgregados);
    }

    public function guardarDerivacion()
    {
        $this->validate([
            'via_ingreso_id' => 'required',
            'descripcion_derivacion' => 'required|min:5',
        ]);

        try {
            DB::transaction(function () {
                // 1. Crear la Intervención
                $intervencion = Intervencion::create([
                    'estudiante_id'  => $this->estudiante->id,
                    'usuario_id'     => auth()->id(),
                    'via_ingreso_id' => $this->via_ingreso_id,
                    'descripcion'    => $this->descripcion_derivacion,
                    'fecha'          => now(),
                ]);

                // 2. Guardar Detalles específicos de Psicosocial
                if (!empty($this->listaDatosAgregados)) {
                    foreach ($this->listaDatosAgregados as $item) {
                        $intervencion->detalles()->create([
                            'motivo_intervencion_id' => $item['motivo_id'],
                            'tipo_intervencion_id'   => $item['tipo_id'],
                            'falta_id'               => null,
                            'medida_id'              => null,
                        ]);
                    }
                }

                // 3. GUARDADO DE ARCHIVOS (Metodología HasDocuments)
                if (!empty($this->archivos)) {
                    foreach ($this->archivos as $archivo) {
                        $rutaGuardada = $archivo->store("documents/intervenciones/{$intervencion->id}", 'public');

                        $intervencion->documents()->create([
                            'name'      => $archivo->getClientOriginalName(),
                            'file_path' => $rutaGuardada,
                            'mime_type' => $archivo->getClientMimeType(),
                            'size'      => $archivo->getSize(),
                        ]);
                    }
                }

                // 4. Envío de correos
                if (!empty($this->usuariosSeleccionados)) {
                    $usuariosDestino = User::whereIn('id', $this->usuariosSeleccionados)->get();
                    $tipoRegistro = 'Intervención Psicosocial';

                    foreach ($usuariosDestino as $usuario) {
                        if ($usuario->email) {
                            $intervencion->load('detalles.motivoIntervencion', 'detalles.tipoIntervencion');
                            Mail::to($usuario->email)->send(new NotificacionCopiaMail(
                                $this->estudiante,
                                $tipoRegistro,
                                $intervencion,
                                $this->listaDatosAgregados
                            ));
                        }
                    }
                }
            });

            // 5. Feedback y Redirección
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Felicitaciones',
                'text' => 'Registro Psicosocial Guardado Exitósamente',
                'timer' => 2500
            ]);

            return redirect()->route('estudiantes');

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo guardar: ' . $e->getMessage(),
            ]);
        }
    }

    // En IntervencionpsicosocialComponent.php

    public function updatedViaIngresoId($value)
    {
        if ($value === 'otro') {
            $this->dispatch('pedir-nueva-via');
        }
    }

    public function guardarNuevaVia($nombre)
    {
        $nuevaVia = Viaingreso::create(['via_ingreso' => $nombre]);
        $this->viaingresos = Viaingreso::all(); // Recargar lista
        $this->via_ingreso_id = $nuevaVia->id;
    }

    public function mejorarTextoIA()
    {
        if (empty($this->descripcion_derivacion)) return;
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
                        'content' => 'Mejora la redacción y ortografía de este texto, manteniéndolo formal: ' . $this->descripcion_derivacion
                    ]
                ],
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $this->descripcion_derivacion = $response->json()['choices'][0]['message']['content'];
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

// Asegúrate de que agregarDato use los nombres de llaves correctos para la tabla

}
