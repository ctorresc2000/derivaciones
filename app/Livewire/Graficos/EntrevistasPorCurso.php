<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Intervencion; // Cambia esto si usas otro modelo para entrevistas
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class EntrevistasPorCurso extends Component
{
    public function render()
    {
        $anioActivo = session('anio_activo', date('Y'));

        // 1. Traemos las entrevistas/intervenciones con sus relaciones
        $entrevistas = Intervencion::with(['estudiante.curso', 'usuario'])
            ->whereYear('created_at', $anioActivo)
            ->get();

        // 2. Extraemos todos los cursos y profesionales únicos para armar la cuadrícula
        $cursosNombres = [];
        $profesionalesNombres = [];

        foreach ($entrevistas as $entrevista) {
            $curso = $entrevista->estudiante->curso->curso ?? 'Sin Curso';
            $profesional = $entrevista->usuario->name ?? 'Sin Asignar';

            if (!in_array($curso, $cursosNombres)) $cursosNombres[] = $curso;
            if (!in_array($profesional, $profesionalesNombres)) $profesionalesNombres[] = $profesional;
        }

        // Ordenamos los cursos alfabéticamente/numéricamente para que se vean ordenados en el gráfico
        sort($cursosNombres);

        $dataset = [];

        // 3. Cruzamos los datos: ¿Cuántas entrevistas hizo CADA profesional en CADA curso?
        foreach ($profesionalesNombres as $profesional) {
            $dataPorCurso = [];

            foreach ($cursosNombres as $curso) {
                // Filtramos y contamos
                $cantidad = $entrevistas->filter(function($entrevista) use ($profesional, $curso) {
                    $c = $entrevista->estudiante->curso->curso ?? 'Sin Curso';
                    $p = $entrevista->usuario->name ?? 'Sin Asignar';

                    return $c === $curso && $p === $profesional;
                })->count();

                $dataPorCurso[] = $cantidad;
            }

            // Solo agregamos al profesional a la leyenda si hizo al menos 1 entrevista en total
            if (array_sum($dataPorCurso) > 0) {
                $dataset[] = [
                    'name' => $profesional, // Este es el nombre que aparecerá al pasar el mouse
                    'data' => $dataPorCurso
                ];
            }
        }

        // 4. Armamos el Gráfico de Barras
        $chart = LarapexChart::barChart()
            ->setTitle('Entrevistas por Curso')
            ->setSubtitle('Detalle por profesional a cargo')
            ->setDataset($dataset)
            ->setXAxis($cursosNombres)
            ->setHeight(350);

        return view('livewire.graficos.entrevistas-por-curso', compact('chart'));
    }
}
