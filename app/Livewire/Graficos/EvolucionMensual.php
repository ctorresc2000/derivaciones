<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Derivarestudiante;
use App\Models\Intervencion;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class EvolucionMensual extends Component
{
    public function render()
    {
        $anio = session('anio_activo', date('Y'));

        // Agrupamos por mes (1 al 12)
        $derivaciones = Derivarestudiante::whereYear('created_at', $anio)->get()->groupBy(fn($d) => $d->created_at->format('n'));
        $intervenciones = Intervencion::whereYear('created_at', $anio)->get()->groupBy(fn($i) => $i->created_at->format('n'));

        $dataDerivaciones = [];
        $dataIntervenciones = [];
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        // Llenamos los 12 meses, si no hay datos ponemos 0
        for ($i = 1; $i <= 12; $i++) {
            $dataDerivaciones[] = isset($derivaciones[$i]) ? $derivaciones[$i]->count() : 0;
            $dataIntervenciones[] = isset($intervenciones[$i]) ? $intervenciones[$i]->count() : 0;
        }

        // Gráfico de líneas cruzadas
        $chart = LarapexChart::lineChart()
            ->setTitle('Evolución Anual')
            ->setSubtitle('Derivaciones vs Intervenciones en ' . $anio)
            // 👇 Usamos el método oficial setDataset() 👇
            ->setDataset([
                [
                    'name' => 'Derivaciones',
                    'data' => $dataDerivaciones
                ],
                [
                    'name' => 'Intervenciones',
                    'data' => $dataIntervenciones
                ]
            ])
            ->setXAxis($meses)
            ->setHeight(350)
            ->setColors(['#8b5cf6', '#10b981']);

        return view('livewire.graficos.evolucion-mensual', compact('chart'));
    }
}
