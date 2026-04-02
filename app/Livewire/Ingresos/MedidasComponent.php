<?php

namespace App\Livewire\Ingresos;

use App\Models\Medida;
use Livewire\Component;

class MedidasComponent extends Component
{
    public $abrirModal=false;
    public $nombre;
    public function render()
    {
        return view('livewire.ingresos.medidas-component');
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

        Medida::create([
            'medida' => $this->nombre,

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
