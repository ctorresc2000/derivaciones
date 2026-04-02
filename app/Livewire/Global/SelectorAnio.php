<?php

namespace App\Livewire\Global;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class SelectorAnio extends Component
{
    public $anioSeleccionado;

    public function mount()
    {
        // 1. Cuando carga, busca el año en la sesión. Si no hay ninguno, usa el año actual (ej. 2026).
        $this->anioSeleccionado = session('anio_activo', date('Y'));
    }

    // 2. Esta función "mágica" de Livewire se ejecuta automáticamente cuando el select cambia
    public function updatedAnioSeleccionado($nuevoAnio)
    {
        // Guardamos el nuevo año en la memoria privada del usuario
        session(['anio_activo' => $nuevoAnio]);

        // Recargamos la página actual para que todas las tablas y gráficos lean el nuevo año
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        // 3. Generamos una lista de años (desde el 2024 hasta 2 años en el futuro)
        $anios = range(2024, date('Y') + 2);

        return view('livewire.global.selector-anio', compact('anios'));
    }
}
