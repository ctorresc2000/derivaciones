<?php

namespace App\Livewire\Ingresos;

use App\Models\Falta;
use Livewire\Component;

class FaltasComponent extends Component
{
    public $abrirModal=false;
    public $nombre;
    public $tipo;

    public function render()
    {
        return view('livewire.ingresos.faltas-component');
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
            'tipo'=>'required',
        ]);

        Falta::create([
            'falta' => $this->nombre,
            'tipo_falta'=>$this->tipo,

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
        $this->reset('nombre','tipo');
    }
}
