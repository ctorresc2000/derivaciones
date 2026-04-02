<?php

namespace App\Livewire\Cursos;

use App\Models\Curso;
use Livewire\Component;

class CursosComponent extends Component
{
    public $abrirModal = false;
    public $curso;
    public $estado = 'Activo';
    public $descripcion;

    public function render()
    {
        return view('livewire.cursos.cursos-component');
    }

     public function guardar()
    {
        $this->validate([
            'curso'=>'required',
            'descripcion'=>'required',
        ]);

        Curso::create([
            'curso' => $this->curso,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
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
        $this->reset('curso', 'descripcion', 'estado');
    }

}
