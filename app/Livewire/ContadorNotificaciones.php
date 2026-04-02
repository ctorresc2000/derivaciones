<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Derivarestudiante;
use App\Models\Intervencion;
use Livewire\Attributes\On;

class ContadorNotificaciones extends Component
{
    public $conteo = 0;

    public function mount()
    {
        $this->actualizarConteo();
    }

    // El atributo #[On] hace que el componente "escuche" cuando otros componentes avisen de un cambio
    #[On('actualizar-notificaciones')]
    public function actualizarConteo()
    {
        // 1. Contamos las derivaciones pendientes
        $derivacionesPendientes = Derivarestudiante::where('estado', 'Pendiente')->count();

        // 2. Contamos las intervenciones abiertas
       // $intervencionesAbiertas = Intervencion::where('estado', 'Abierta')->count();

        // 3. Sumamos todo
        $this->conteo = $derivacionesPendientes; //+ $intervencionesAbiertas;

        /* * 💡 NOTA PARA EL FUTURO (Filtrado por usuario):
         * Cuando quieras que solo le aparezca al usuario logueado, cambiarás las consultas a algo así:
         * Derivarestudiante::where('estado', 'Pendiente')->where('profesional_derivado_id', auth()->id())->count();
         */
    }

    public function render()
    {
        return view('livewire.contador-notificaciones');
    }
}
