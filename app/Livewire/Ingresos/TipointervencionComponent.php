<?php

namespace App\Livewire\Ingresos;

use App\Models\Tipointervencion;
use Livewire\Component;

class TipointervencionComponent extends Component
{

    public $abrirModal=false;
    public $nombre;

    public function render()
    {
        return view('livewire.ingresos.tipointervencion-component');
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

        Tipointervencion::create([
            'tipo' => $this->nombre,

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
