<?php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use App\Models\Falta;
use App\Models\Intervencion;
use App\Models\Medida;
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

class DerivacionComponent extends Component
{
    use HasDocuments; // 2. Lo usas dentro de la clase
    use WithFileUploads;

    public $estudiante;
    public $derivacion_destino;
    public $descripcion_derivacion;
    public $profesionales = [];
    public $viaingresos = [];
    public $archivos;
    public $fechaEntrevista;
    public $faltas = [];
    public $medidas = [];
    public $listaUsuarios = [];
    public $usuariosSeleccionados = [];
    public $via_ingreso_id;

    // VARIABLES PARA NUESTRA TABLA DINÁMICA
    public $falta_seleccionada_id = '';
    public $medida_seleccionada_id = '';
    public $listaDatosAgregados = [];
    public $editando_index = null;
    public $mejorando = false;

    public function render()
    {
        return view('livewire.estudiante.derivacion-component');
    }

    public function mount($id)
    {
        $this->fechaEntrevista = now()->format('Y-m-d');
        $this->estudiante = Estudiante::findOrFail($id);
        $this->profesionales = Profesional::all();
        $this->viaingresos=Viaingreso::orderBy('via_ingreso','asc')->get();
        $this->faltas=Falta::orderBy('falta','asc')->get();
        $this->medidas = Medida::orderBy('medida','asc')->get();
        $this->listaUsuarios = User::where('estado', 'Activo')->get();
    }

    public function updatedViaIngresoId($value)
    {
        if ($value === 'otro') {
            $this->dispatch('pedir-nueva-via');
        }
    }

    public function guardarNuevaVia($nombreVia)
    {
        $nombreVia = trim($nombreVia);

        if (!empty($nombreVia)) {
            $nuevaVia = \App\Models\Viaingreso::create([
                'via_ingreso' => $nombreVia
            ]);

            $this->viaingresos = \App\Models\Viaingreso::all();
            $this->via_ingreso_id = $nuevaVia->id;
            $this->dispatch('notificacion', mensaje: 'Vía de ingreso creada con éxito');
        }
    }

    public function agregarDato()
    {
        if (empty($this->falta_seleccionada_id) || empty($this->medida_seleccionada_id)) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Atención',
                'text' => 'Por favor selecciona tanto el Tipo de Falta como la Medida.'
            ]);
            return;
        }

        $falta = \App\Models\Falta::find($this->falta_seleccionada_id);
        $medida = \App\Models\Medida::find($this->medida_seleccionada_id);

        if ($falta && $medida) {
            $nuevoDato = [
                'falta_id'      => $falta->id,
                'falta_nombre'  => $falta->falta,
                'medida_id'     => $medida->id,
                'medida_nombre' => $medida->medida,
            ];

            if ($this->editando_index !== null) {
                $this->listaDatosAgregados[$this->editando_index] = $nuevoDato;
                $this->editando_index = null;
            } else {
                $this->listaDatosAgregados[] = $nuevoDato;
            }

            $this->limpiarFormularioDatos();
        }
    }

    public function eliminarDato($index)
    {
        unset($this->listaDatosAgregados[$index]);
        $this->listaDatosAgregados = array_values($this->listaDatosAgregados);

        if ($this->editando_index === $index) {
            $this->limpiarFormularioDatos();
        }
    }

    public function limpiarFormularioDatos()
    {
        $this->falta_seleccionada_id = '';
        $this->medida_seleccionada_id = '';
        $this->editando_index = null;
    }

    public function guardarDerivacion()
    {
        $this->validate([
            'via_ingreso_id' => 'required',
            'descripcion_derivacion' => 'required|min:5',
        ]);

        try {
            DB::transaction(function () {
                // 1. Crear Intervención
                $intervencion = Intervencion::create([
                    'estudiante_id'  => $this->estudiante->id,
                    'usuario_id'     => auth()->id(), // Asegúrate que el campo sea 'usuario_id' o 'user_id'
                    'via_ingreso_id' => $this->via_ingreso_id,
                    'descripcion'    => $this->descripcion_derivacion,
                    'fecha'          => $this->fechaEntrevista,
                ]);

                // 2. Guardar Detalles
                if (!empty($this->listaDatosAgregados)) {
                    foreach ($this->listaDatosAgregados as $item) {
                        $intervencion->detalles()->create([
                            'falta_id'  => $item['falta_id'],
                            'medida_id' => $item['medida_id'],
                        ]);
                    }
                }

                // 3. Guardar Archivos (HasDocuments)
                if (!empty($this->archivos)) {
                    foreach ($this->archivos as $archivo) {
                        $rutaGuardada = $archivo->store("documents/intervenciones/{$intervencion->id}", 'public');
                        $intervencion->documents()->create([
                            'name'      => $archivo->getClientOriginalName(),
                            'file_path' => $rutaGuardada, // Verifica si tu modelo usa 'file_path' o 'ruta_archivo'
                            'mime_type' => $archivo->getClientMimeType(),
                            'size'      => $archivo->getSize(),
                        ]);
                    }
                }

                // 4. Envío de Correos
                if (!empty($this->usuariosSeleccionados)) {
                    // CARGA CRÍTICA: Cargar relaciones antes de enviar
                    $intervencion->load(['documents', 'user', 'viaIngreso']);

                    $usuariosDestino = User::whereIn('id', $this->usuariosSeleccionados)->get();
                    $tipoRegistro = 'Intervención de Convivencia Escolar';

                    foreach ($usuariosDestino as $usuario) {
                        if ($usuario->email) {
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

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Felicitaciones',
                'text' => 'Registro Guardado Exitosamente',
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
}
