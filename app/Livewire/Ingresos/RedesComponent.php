<?php

namespace App\Livewire\Ingresos;

use Livewire\Component;
use App\Models\RedesApoyo;

class RedesComponent extends Component
{

    public $abrirModal=false;
    public $nombre;
    public $contacto;
    public $telefono;
    public $email;

    public function render()
    {
        return view('livewire.ingresos.redes-component');
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
            'contacto'=>'required',
            'telefono'=>'required',
            'email'=>'required|email',
            ]);

          //  dd("Guardando red de apoyo: $this->nombre, contacto: $this->contacto, teléfono: $this->telefono");
            RedesApoyo::create([
                'nombre' => $this->nombre,
                'contacto' => $this->contacto,
                'telefono' => $this->telefono,
                'email' => $this->email,
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
        $this->reset('nombre','contacto','telefono','email');
    }
}
