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

    public $modalCursos = false;

    public $profesores;
    public $user_id;

    public function abrirModal()
    {
        $this->modalCursos = true;
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
        $this->resetValidation();
        $this->reset('curso', 'descripcion', 'estado', 'user_id');
    }

    public function mount() {
        $this->profesores = \App\Models\User::all(); // O filtra por rol si es necesario
    }

    public function render()
    {
        return view('livewire.cursos.cursos-component');
    }

     public function guardar()
    {
        $this->validate([
            'curso'=>'required',
            'descripcion'=>'required',
            'user_id'=>'nullable'
        ]);

        Curso::create([
            'curso' => $this->curso,
            'descripcion' => $this->descripcion,
            'user_id' => $this->user_id,
            'estado' => $this->estado,
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Guardado Exitósamente',
            'timer' => 1500
        ]);

        $this->cerrarModal();
        $this->dispatch('refreshTable');
        $this->resetValidation();
        $this->reset('curso', 'descripcion', 'estado');
    }



}
