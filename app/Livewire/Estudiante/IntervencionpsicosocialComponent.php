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
            'descripcion_derivacion' => 'required',
        ]);

        DB::transaction(function () {
            $intervencion = Intervencion::create([
                'estudiante_id' => $this->estudiante->id,
                'usuario_id' => Auth::user()->id,
                'fecha' => now(),
                'via_ingreso_id' => $this->via_ingreso_id,
                'descripcion' => $this->descripcion_derivacion,
                'tipo_intervencion' => 'Psicosocial',
                'estado' => 'Abierta',
            ]);

            foreach ($this->listaDatosAgregados as $item) {
                $intervencion->detalles()->create([
                    'motivo_intervencion_id' => $item['motivo_id'],
                    'tipo_intervencion_id' => $item['tipo_id'],
                    'falta_id' => null,
                    'medida_id' => null,
                ]);
            }

           // dd($this->listaDatosAgregados);

            if (!empty($this->usuariosSeleccionados)) {
                $usuariosDestino = User::whereIn('id', $this->usuariosSeleccionados)->get();
                $tipoRegistro = 'Intervención Psicosocial';

                foreach ($usuariosDestino as $usuario) {
                    if ($usuario->email) {
                        // Importante: Cargar las relaciones correctas para el Mail
                        $intervencion->load('detalles.motivoIntervencion', 'detalles.tipoIntervencion');
                        Mail::to($usuario->email)->send(new NotificacionCopiaMail($this->estudiante, $tipoRegistro, $intervencion, $this->listaDatosAgregados));
                    }
                }
            }
        });

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Guardado Exitósamente',
            'timer' => 2500
        ]);

        return redirect()->route('estudiantes');
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

// Asegúrate de que agregarDato use los nombres de llaves correctos para la tabla

}
