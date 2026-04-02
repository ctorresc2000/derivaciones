<?php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use App\Models\Intervencion;
use App\Models\Motivointervencion;
use App\Models\Profesional;
use App\Models\Tipointervencion;
use App\Models\User;
use App\Models\Viaingreso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class IntervencionpsicosocialComponent extends Component
{

    use WithFileUploads;

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

    // VARIABLES PARA NUESTRA TABLA DINÁMICA
    public $motivo_seleccionado_id = '';
    public $tipo_seleccionado_id = '';
    public $listaDatosAgregados = [];
    public $editando_index = null;

    public function render()
    {
        return view('livewire.estudiante.intervencionpsicosocial-component');
    }

    public function mount($id)
    {

        $this->estudiante = Estudiante::findOrFail($id);
        $this->profesionales = Profesional::all();
        $this->viaingresos=Viaingreso::orderBy('via_ingreso','asc')->get();
        $this->motivos=Motivointervencion::orderBy('motivo','asc')->get();
        $this->tipos = Tipointervencion::orderBy('tipo','asc')->get();
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
        if (empty($this->motivo_seleccionado_id) || empty($this->tipo_seleccionado_id)) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Atención',
                'text' => 'Por favor selecciona tanto el Tipo de Falta como la Medida.'
            ]);
            return;
        }

        $motivo = \App\Models\Motivointervencion::find($this->motivo_seleccionado_id);
        $tipo = \App\Models\Tipointervencion::find($this->tipo_seleccionado_id);

        if ($motivo && $tipo) {
            $nuevoDato = [
                'falta_id'      => $motivo->id,
                'falta_nombre'  => $motivo->motivo,
                'medida_id'     => $tipo->id,
                'medida_nombre' => $tipo->tipo,
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
        $this->motivo_seleccionado_id = '';
        $this->tipo_seleccionado_id= '';
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
                'usuario_id' => Auth::id(),
                'via_ingreso_id' => $this->via_ingreso_id,
                'descripcion'    => $this->descripcion_derivacion,
                'fecha'          => now(),
            ]);

            if (!empty($this->listaDatosAgregados)) {
                foreach ($this->listaDatosAgregados as $item) {
                    $intervencion->detalles()->create([
                        'falta_id'  => $item['falta_id'],
                        'medida_id' => $item['medida_id'],
                    ]);
                }
            }

            // 👇 LO NUEVO: Guardado polimórfico de múltiples archivos 👇
            if (!empty($this->archivos)) {
                foreach ($this->archivos as $archivo) {

                    // Ordenamos los archivos por ID de intervención
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
                $intervencion->copiasUsuarios()->attach($this->usuariosSeleccionados);
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
            'timer' => 1500
        ]);
    }
}
