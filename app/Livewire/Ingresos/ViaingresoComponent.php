<?php

namespace App\Livewire\Ingresos;

use App\Models\Viaingreso;
use Livewire\Component;

class ViaingresoComponent extends Component
{
    public $nombre;
    public $abrirModal = false;
    public function render()
    {
        return view('livewire.ingresos.viaingreso-component');
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
        $this->resetValidation();
        $this->reset('nombre');
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required',
        ]);

        Viaingreso::create([
            'via_ingreso' => $this->nombre,

        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Guardado Exitósamente',
            'timer' => 1500
        ]);

        $this->abrirModal = false;
        $this->dispatch('refreshTable');
        $this->resetValidation();
        $this->reset('nombre');
    }
}
