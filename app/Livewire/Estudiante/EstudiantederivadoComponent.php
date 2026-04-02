<?php

namespace App\Livewire\Estudiante;

use App\Models\Viaingreso;
use Livewire\Component;
use WireUi\Attributes\Mount;

class EstudiantederivadoComponent extends Component
{
    public $viaingresos;

    public function render()
    {
        return view('livewire.estudiante.estudiantederivado-component');
    }

    // public function mount()
    // {
    //     $this->viaingresos=Viaingreso::orderBy('via_ingreso','asc')->get();
    // }




}
