<?php

namespace App\Livewire\User;

use App\Models\Profesional;
use App\Models\Tipointervencion;
use App\Models\Tipoprofesional;
use Livewire\Component;

class ProfesionalesComponent extends Component
{
    public $nombre;
    public $tipo;
    public $email;
    public $observaciones;
    public $estado = 'Activo';
    public $abrirModal = false;
    public $profesionalId;
    public $tipo_profesiones=[];

    public function render()
    {
        return view('livewire.user.profesionales-component');
    }

    public function mount()
    {
        $this->tipo_profesiones=Tipoprofesional::orderBy('tipo','asc')->get();
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
        $this->resetValidation();
        $this->reset('nombre', 'tipo', 'observaciones', 'estado');
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required',
            'tipo' => 'required',
            'email'=>'required|email|unique:profesionals,email',
            'observaciones' => 'nullable',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Profesional::create([
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'email' => $this->email,
            'observacion' => $this->observaciones,
            'estado' => 'Activo',
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
        $this->reset('nombre', 'tipo', 'observaciones', 'estado', 'email');
    }
}
