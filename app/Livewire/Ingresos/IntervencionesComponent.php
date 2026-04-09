<?php

namespace App\Livewire\Ingresos;

use Livewire\Attributes\On;
use App\Models\Intervencion;
use App\Models\AccionIntervencion;
use Livewire\Component;

use App\Models\Estudiante;

use App\Models\Intervencioncopia;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\NotificacionCopiaMail;
use id;

class IntervencionesComponent extends Component
{
    public $abrirModal = false;

    // Variables para el formulario del modal
    public $intervencion_id;
    public $descripcion_accion;

    // Variable para guardar la lista de acciones anteriores
    public $historialAcciones = [];

    public function render()
    {
        return view('livewire.ingresos.intervenciones-component');
    }

    #[On('abrirModal')]
    public function abrirModal($rowId)
    {
        // 1. Guardamos el ID correctamente
        $this->intervencion_id = is_array($rowId) ? $rowId['rowId'] : $rowId;

        // 2. Limpiamos la caja de texto
        $this->descripcion_accion = '';

        // 3. Cargamos el historial ANTES de abrir el modal
        $this->cargarHistorial();

        // 4. Abrimos el modal
        $this->abrirModal = true;
    }

    // Función para buscar en la BD todas las acciones de esta intervención
    public function cargarHistorial()
    {
        $this->historialAcciones = AccionIntervencion::with('usuario')
            ->where('intervencion_id', $this->intervencion_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
    }

    public function guardarAccion()
    {
        // Obligamos a que escriban al menos 5 letras
        $this->validate([
            'descripcion_accion' => 'required|min:5',
        ]);

        // Guardamos la nueva acción en la base de datos
        AccionIntervencion::create([
            'intervencion_id' => $this->intervencion_id,
            'user_id' => auth()->id(),
            'fecha' => now(),
            'descripcion' => $this->descripcion_accion,
        ]);

        // En lugar de cerrar el modal, limpiamos el campo y recargamos la lista
        $this->descripcion_accion = '';
        $this->cargarHistorial();

       // Mail::to($usuario->email)->send(new NotificacionCopiaMail($this->estudiante, $tipoRegistro, $nuevaIntervencion));

        // Refrescamos la tabla de PowerGrid en el fondo
        $this->dispatch('pg:eventRefresh-intervencionesTable');

        // Lanzamos una notificación de éxito con SweetAlert
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Excelente',
            'text' => 'Acción guardada en el historial',
            'timer' => 1500
        ]);
    }

    // public function guardarAccion()
    // {
    //     // Validar que escribió algo
    //     $this->validate(['nuevaAccionTexto' => 'required']);

    //     // Usar el modelo que me acabas de mostrar para guardar en la BD
    //     AccionIntervencion::create([
    //         'intervencion_id' => $this->intervencionSeleccionadaId,
    //         'user_id' => auth()->id(),
    //         'fecha' => now(),
    //         'descripcion' => $this->nuevaAccionTexto
    //     ]);

    //     // Limpiar el campo y actualizar la lista
    //     $this->nuevaAccionTexto = '';
    //     $this->cargarAcciones();
    // }
}
