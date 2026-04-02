<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Intervencion;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class ViasIngresoIntervenciones extends Component
{
    public function render()
    {
        $anioActivo = session('anio_activo', date('Y'));

        // 1. Traemos las intervenciones con su vía de ingreso
        $intervenciones = Intervencion::with('viaIngreso')->whereYear('created_at', $anioActivo)->get();

        // 2. Agrupamos por el nombre de la vía de ingreso (columna 'via_ingreso')
        $agrupadas = $intervenciones->groupBy(function($intervencion) {
            return $intervencion->viaIngreso->via_ingreso ?? 'Sin especificar';
        });

        $etiquetas = [];
        $cantidades = [];

        // 3. Contamos cuántas hay por cada vía
        foreach ($agrupadas as $via => $grupo) {
            $etiquetas[] = $via;
            $cantidades[] = $grupo->count();
        }

        // 4. Armamos el Gráfico de Barras
        $chart = LarapexChart::barChart()
            ->setTitle('Vías de Ingreso')
            ->setSubtitle('Origen de las Intervenciones')
            ->addData($cantidades)
            ->setXAxis($etiquetas)
            ->setHeight(400) // Mantenemos la simetría
            ->setColors(['#f43f5e']); // Color Rose/Carmesí

        return view('livewire.graficos.vias-ingreso-intervenciones', compact('chart'));
    }
}
