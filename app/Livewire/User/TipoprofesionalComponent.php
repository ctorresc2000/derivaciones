<?php

namespace App\Livewire\User;

use App\Models\Tipoprofesional;
use Livewire\Component;

class TipoprofesionalComponent extends Component
{

    public $abrirModal=false;
    public $nombre;
    public $departamento;

    public function render()
    {
        return view('livewire.user.tipoprofesional-component');
    }

        public function cerrarModal()
    {
        $this->abrirModal = false;
        $this->resetValidation();
        $this->reset('nombre', 'departamento');
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required',
            'departamento' => 'required'
        ]);

        Tipoprofesional::create([
            'tipo' => $this->nombre,
            'departamento' => $this->departamento

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
        $this->reset('nombre', 'departamento');
    }
}
