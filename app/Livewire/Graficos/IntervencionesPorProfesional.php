<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Intervencion;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class IntervencionesPorProfesional extends Component
{
    public function render()
    {
        $anioActivo = session('anio_activo', date('Y'));
       // 1. Cargamos el usuario Y TAMBIÉN su tipo de profesional en la misma consulta (súper rápido)
        // Ojo: 'tipoProfesional' asume que así se llama la función de relación en tu modelo User.
        $intervenciones = Intervencion::with('usuario.tipoProfesional')->whereYear('created_at', $anioActivo)->get();

        // 2. Agrupamos haciendo el "viaje" completo por las relaciones
        $agrupadas = $intervenciones->groupBy(function($intervencion) {
            // Viaje: Intervencion -> Usuario -> TipoProfesional -> columna "tipo"
            return $intervencion->usuario->tipoProfesional->tipo ?? 'Sin asignar';
        });

        $etiquetas = [];
        $cantidades = [];

        // 3. Contamos y preparamos los datos
        foreach ($agrupadas as $tipo => $grupo) {
            $etiquetas[] = ucfirst($tipo); // Ej: "Psicologo", "Convivencia"
            $cantidades[] = $grupo->count();
        }

        // 4. Armamos el Gráfico de Barras
        $chart = LarapexChart::barChart()
            ->setTitle('Intervenciones por Profesional')
            ->setSubtitle('Cantidad de atenciones según área')
            ->addData($cantidades) // 👈 ¡EL CAMBIO ESTÁ AQUÍ! Solo pasamos el arreglo directo.
            ->setXAxis($etiquetas)
            ->setHeight(400)
            ->setColors(['#10b981']);

        return view('livewire.graficos.intervenciones-por-profesional', compact('chart'));
    }
}
