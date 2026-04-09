<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Derivarestudiante;
use Livewire\Attributes\On;

class ContadorNotificaciones extends Component
{
    public $conteo = 0;

    public function mount()
    {
        $this->actualizarConteo();
    }

    #[On('actualizar-notificaciones')]
    public function actualizarConteo()
    {
        $user = auth()->user();

        // Si no hay usuario (sesión expirada), el conteo es 0
        if (!$user) {
            $this->conteo = 0;
            return;
        }

        // Iniciamos la consulta base para derivaciones pendientes
        $query = Derivarestudiante::where('estado', 'Pendiente');

        // Aplicamos el filtro de usuario solo si NO es Administrador
        $query->when($user->rol !== 'Administrador', function ($q) use ($user) {
            return $q->where('profesional_derivado_id', $user->id);
        });

        // Ejecutamos el conteo final
        $this->conteo = $query->count();
    }

    public function render()
    {
        return view('livewire.contador-notificaciones');
    }
}
