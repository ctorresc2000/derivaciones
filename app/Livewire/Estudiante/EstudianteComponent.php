<?php

namespace App\Livewire\Estudiante;

use App\Imports\EstudiantesImport;
use App\Models\Curso;
use App\Models\Derivarestudiante;
use App\Models\Estudiante;
use App\Models\EstudianteCurso;
use App\Models\Motivointervencion;
use App\Models\User;
use App\Models\Viaingreso;
use App\Traits\HasDocuments;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class EstudianteComponent extends Component
{
    use WithFileUploads;
    use HasDocuments;

    // --- PROPIEDADES ORIGINALES ---
    public $name, $email, $social, $curso_id, $observaciones, $rut, $apellido, $domicilio, $telefono, $fecha_nacimiento;
    public $estado = 'Activo';
    public $estudianteId;
    public $abrirModal = false;
    public $derivarModal = false;
    public $cursos = [];
    public $profesionales = [];
    public ?Estudiante $estudianteSeleccionado = null;

    // Propiedades para Derivación
    public $estudianteParaDerivarId;
    public $motivo_derivacion, $profesional_derivado_id, $detalle_derivacion, $adjunto_derivacion;

    // --- PROPIEDADES DE ACCIONES MASIVAS E IA ---
    public $modalRedes = false;
    public $estudianteSeleccionadoRedes;
    public $red_id, $observacion_red;
    public $selectedIds = [];

    // --- NUEVAS PROPIEDADES: PROMOCIÓN ASISTIDA ---
    public $modalPromocionCurso = false;
    public $curso_origen;
    public $curso_destino;
    public $condicion;
    public $estudiantes_seleccionadas = [];

    // Listener para Redes de Apoyo
    protected $listeners = ['abrirModalRedes'];

    public function mount()
    {
        $this->fecha_nacimiento = now()->format('Y-m-d');
        $this->cursos = Curso::all();
        $this->profesionales = User::where('estado', 'Activo')
            ->whereNotNull('tipo_profesional_id')
            ->orderBy('name', 'asc')->get();
    }

    /**
     * RESTAURADO: Botón Derivar (Derivación interna)
     */
    #[On('abrirModalDerivacion')]
    public function abrirModalDerivacion($rowId)
    {
        $this->reset(['motivo_derivacion', 'profesional_derivado_id', 'detalle_derivacion', 'adjunto_derivacion']);
        $this->estudianteParaDerivarId = $rowId;
        $this->estudianteSeleccionado = Estudiante::find($rowId);
        $this->derivarModal = true;
    }

    /**
     * RESTAURADO: Botón Redes de Apoyo
     */
    #[On('abrirModalRedes')]
    public function abrirModalRedes($estudianteId)
    {
        $this->estudianteSeleccionadoRedes = Estudiante::find($estudianteId);
        $this->modalRedes = true;
    }

    /**
     * LÓGICA DE PROMOCIÓN (Tabla reactiva del modal)
     */
    public function getEstudiantesPendientesProperty()
    {
        if (!$this->curso_origen) return [];

        $anioActivo = session('anio_activo', date('Y'));

        return Estudiante::where('curso_id', $this->curso_origen)
            ->whereDoesntHave('estudianteCursos', function ($query) use ($anioActivo) {
                $query->where('anio_academico', $anioActivo);
            })
            ->get();
    }

    public function promocionarSeleccionadas()
    {
        $this->validate([
            'curso_destino' => 'required',
            'condicion' => 'required',
            'estudiantes_seleccionadas' => 'required|array|min:1',
        ]);

        $anioParaPromocion = session('anio_activo', date('Y'));

        try {
            DB::beginTransaction();
            foreach ($this->estudiantes_seleccionadas as $estudianteId) {
                EstudianteCurso::create([
                    'estudiante_id'  => $estudianteId,
                    'curso_id'       => $this->curso_destino,
                    'anio_academico' => $anioParaPromocion,
                    'condicion'      => $this->condicion,
                ]);

                Estudiante::where('id', $estudianteId)->update(['curso_id' => $this->curso_destino]);
            }
            DB::commit();

            $this->reset(['estudiantes_seleccionadas', 'condicion']);
            $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Éxito!', 'timer' => 1500]);
            $this->dispatch('pg:eventRefresh-estudianteTable');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    #[On('iniciarNuevoAnio')]
    public function abrirModalPromocionCurso() { $this->modalPromocionCurso = true; }

    public function cerrarModalPromocionCurso() {
        $this->modalPromocionCurso = false;
        $this->reset(['curso_origen', 'curso_destino', 'estudiantes_seleccionadas', 'condicion']);
    }

    // Función para cerrar el modal de derivación
    public function cerrarModalDerivacion()
    {
        $this->derivarModal = false;
        $this->reset(['motivo_derivacion', 'profesional_derivado_id', 'detalle_derivacion', 'adjunto_derivacion', 'estudianteParaDerivarId']);
    }

    // Función para cerrar el modal de redes
    public function cerrarModalRedes()
    {
        $this->modalRedes = false;
        $this->reset(['red_id', 'observacion_red', 'estudianteSeleccionadoRedes']);
    }

    // Función para cerrar el modal de edición/creación
    public function cerrarModal()
    {
        $this->abrirModal = false;
        $this->reset(['name', 'apellido', 'rut', 'email', 'social', 'curso_id', 'domicilio', 'telefono', 'fecha_nacimiento', 'observaciones', 'estudianteId']);
    }

    public function render()
    {
        // Obtenemos el año activo de la sesión para los labels
        $anioParaPromocion = session('anio_activo', date('Y'));

        return view('livewire.estudiante.estudiante-component', [
            'redes' => \App\Models\RedesApoyo::all(),
            'vias' => \App\Models\Viaingreso::all(),
            'cursos_lista' => \App\Models\Curso::orderBy('curso')->get(),
            'motivos' => \App\Models\Motivointervencion::orderBy('motivo', 'asc')->get(), // <--- ESTA ES LA QUE FALTA
            'anioParaPromocion' => $anioParaPromocion
        ]);
    }
}
