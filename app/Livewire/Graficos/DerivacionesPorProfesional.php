<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Derivarestudiante;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class DerivacionesPorProfesional extends Component
{
    public function render()
    {
        $anioActivo = session('anio_activo', date('Y'));
        // 1. Traemos las derivaciones con el profesional al que fueron derivados y su tipo
        // Usamos 'profesional' porque así llamaste a tu relación en el modelo Derivarestudiante
        $derivaciones = Derivarestudiante::with('profesional.tipoProfesional')->whereYear('created_at', $anioActivo)->get();

        // 2. Agrupamos haciendo el viaje completo
        $agrupadas = $derivaciones->groupBy(function($derivacion) {
            // Viaje: Derivacion -> Profesional (User) -> TipoProfesional -> columna "tipo"
            return $derivacion->profesional->tipoProfesional->tipo ?? 'Sin asignar';
        });

        $etiquetas = [];
        $cantidades = [];

        // 3. Contamos
        foreach ($agrupadas as $tipo => $grupo) {
            $etiquetas[] = ucfirst($tipo);
            $cantidades[] = $grupo->count();
        }

        // 4. Armamos el Gráfico de Barras
        $chart = LarapexChart::barChart()
            ->setTitle('Derivaciones por Área')
            ->setSubtitle('A quién se derivan los estudiantes')
            ->addData($cantidades)
            ->setXAxis($etiquetas)
            ->setHeight(400) // Misma altura que los otros para que se vea ordenado
            ->setColors(['#8b5cf6']); // Un color púrpura bonito

        return view('livewire.graficos.derivaciones-por-profesional', compact('chart'));
    }
}
