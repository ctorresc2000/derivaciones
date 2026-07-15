<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Derivarestudiante;
use App\Models\Motivointervencion;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class MotivosDerivacion extends Component
{
    public function render()
    {
        $anioActivo = session('anio_activo', date('Y'));
        // 1. Traemos todas las derivaciones
        $derivaciones = Derivarestudiante::whereYear('created_at', $anioActivo)->get();

        // 2. Agrupamos por la columna 'motivo_derivacion'
        $agrupadas = $derivaciones->groupBy('motivo_derivacion');

        $etiquetas = [];
        $cantidades = [];

        // 3. Procesamos los datos
        foreach ($agrupadas as $motivoValor => $grupo) {

            // MAGIA: Verificamos si lo que se guardó es un ID (número) o el texto directo
            if (is_numeric($motivoValor)) {
                // Si es un ID, buscamos el nombre en tu modelo Motivointervencion
                $modelo = Motivointervencion::find($motivoValor);
                $nombreMotivo = $modelo ? $modelo->motivo : 'Desconocido';
            } else {
                // Si ya es un texto, lo usamos directamente
                $nombreMotivo = empty($motivoValor) ? 'Sin especificar' : $motivoValor;
            }

            $etiquetas[] = (string) $nombreMotivo;
            $cantidades[] = $grupo->count();
        }

        // 4. Armamos el Gráfico de Barras
        // En lugar de barChart(), usa esto:
$chart = LarapexChart::donutChart()
    ->setTitle('Motivos de Derivación')
    ->setSubtitle('Causas principales de atención')
    ->addData($cantidades)
    ->setLabels($etiquetas);

        return view('livewire.graficos.motivos-derivacion', compact('chart'));
    }
}
