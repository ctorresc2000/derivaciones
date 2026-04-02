<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\AccionDerivacion;

class SeguimientoDerivacionComponent extends Component
{
    public $abrirModal = false;

    public $derivacion_id;
    public $descripcion_accion;
    public $historialAcciones = [];

    // Atrapamos el evento desde estudiantederivadoTable
    #[\Livewire\Attributes\On('abrirModalSeguimiento')]
    public function cargarDatosModal($rowId)
    {
        $this->derivacion_id = is_array($rowId) ? $rowId['rowId'] : $rowId;
        $this->descripcion_accion = '';

        $this->cargarHistorial();
        $this->abrirModal = true;
    }

    public function cargarHistorial()
    {
        $this->historialAcciones = AccionDerivacion::with('usuario')
            ->where('derivarestudiante_id', $this->derivacion_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
    }

    public function guardarAccion()
    {
        $this->validate([
            'descripcion_accion' => 'required|min:5',
        ]);

        AccionDerivacion::create([
            'derivarestudiante_id' => $this->derivacion_id,
            'user_id' => auth()->id(),
            'fecha' => now(),
            'descripcion' => $this->descripcion_accion,
        ]);

        // Limpiamos y recargamos la lista visual
        $this->descripcion_accion = '';
        $this->cargarHistorial();

        // Opcional: Refrescar la tabla de fondo
        $this->dispatch('pg:eventRefresh-estudiantederivadoTable');
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Excelente',
            'text' => 'Acción guardada en el historial',
            'timer' => 1500
        ]);
    }

    public function render()
    {
        return view('livewire.estudiante.seguimiento-derivacion-component');
    }
}
