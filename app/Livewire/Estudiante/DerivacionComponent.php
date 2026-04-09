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

class DerivacionComponent extends Component
{

    use WithFileUploads;

    public $estudiante;
    public $derivacion_destino;
    public $descripcion_derivacion;
    public $profesionales = [];
    public $viaingresos = [];
    public $archivos;
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

    public function render()
    {
        return view('livewire.estudiante.derivacion-component');
    }

    public function mount($id)
    {
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
            'descripcion_derivacion' => 'required|min:10',
        ]);

        DB::transaction(function () {

            $intervencion = Intervencion::create([
                'estudiante_id'  => $this->estudiante->id,
                'usuario_id'     => auth()->id(),
                'via_ingreso_id' => $this->via_ingreso_id,
                'descripcion'    => $this->descripcion_derivacion,
                'fecha'          => now(),
            ]);

            if (!empty($this->listaDatosAgregados)) {
                foreach ($this->listaDatosAgregados as $item) {
                    $intervencion->detalles()->create([
                        'falta_id'  => $item['falta_id'],
                        'medida_id' => $item['medida_id'],
                        'motivo_intervencion_id' => null, // Aseguramos que vaya vacío
                        'tipo_intervencion_id' => null,
                    ]);
                }
            }


            // 👇 LO NUEVO: Guardado polimórfico de múltiples archivos 👇
            if (!empty($this->archivos)) {
                foreach ($this->archivos as $archivo) {

                    // Se crea una carpeta con el ID de la intervención para mantener el orden
                    $rutaFisica = $archivo->store("documents/intervenciones/{$intervencion->id}", 'public');

                    $intervencion->documents()->create([
                        'name'      => $archivo->getClientOriginalName(),
                        'file_path' => $rutaFisica,
                        'mime_type' => $archivo->getClientMimeType(),
                        'size'      => $archivo->getSize(),
                    ]);
                }
            }

            if (!empty($this->usuariosSeleccionados)) {
                // 1. Buscamos a los usuarios
                $usuariosDestino = User::whereIn('id', $this->usuariosSeleccionados)->get();

                // 2. Definimos el título según el contexto (Convivencia en este caso)
                $tipoRegistro = 'Intervención de Convivencia Escolar';

                // CARGAMOS LOS DETALLES: Esto trae las faltas y medidas de la DB
                $intervencion->load('detalles.falta', 'detalles.medida');
                //dd($this->listaDatosAgregados);

                foreach ($usuariosDestino as $usuario) {
                    if ($usuario->email) {
                        // Cargamos las relaciones para que el correo pueda mostrarlas
                        $intervencion->load('detalles.falta', 'detalles.medida');

                        Mail::to($usuario->email)->send(new NotificacionCopiaMail($this->estudiante, $tipoRegistro, $intervencion,$this->listaDatosAgregados));
                    }
                }

                // 3. Envío de correos
                // foreach ($usuariosDestino as $usuario) {
                //     if (!empty($usuario->email)) {
                //         // Pasamos: 1. El estudiante, 2. El título, 3. El objeto de la intervención recién creada
                //         Mail::to($usuario->email)->send(new NotificacionCopiaMail($this->estudiante, $tipoRegistro, $intervencion));
                //     }
                // }
            }

        });

        $this->reset([
            'via_ingreso_id',
            'descripcion_derivacion',
            'listaDatosAgregados',
            'usuariosSeleccionados',
            'archivos'
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Guardado Exitósamente',
            'timer' => 2500
        ]);

        redirect()->route('estudiantes');
    }
}
