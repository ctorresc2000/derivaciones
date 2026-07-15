<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Derivarestudiante;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class EstadoDerivaciones extends Component
{
    public function render()
    {
        $anio = session('anio_activo', date('Y'));
        $derivaciones = Derivarestudiante::whereYear('created_at', $anio)->get()->groupBy('estado');

        $etiquetas = [];
        $cantidades = [];

        foreach ($derivaciones as $estado => $grupo) {
            $etiquetas[] = $estado ? ucfirst($estado) : 'Desconocido';
            $cantidades[] = $grupo->count();
        }

        // Gráfico de Dona para ver porcentajes del total
        $chart = LarapexChart::donutChart()
            ->setTitle('Estado de Derivaciones')
            ->setSubtitle('Eficiencia de atención')
            ->addData($cantidades)
            ->setLabels($etiquetas)
            ->setHeight(350)
            ->setColors(['#f59e0b', '#3b82f6', '#10b981', '#ef4444']); // Ámbar, Azul, Verde, Rojo

        return view('livewire.graficos.estado-derivaciones', compact('chart'));
    }
}
