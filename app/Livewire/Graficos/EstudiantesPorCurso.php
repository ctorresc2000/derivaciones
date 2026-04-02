<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Estudiante;
use App\Models\Curso;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class EstudiantesPorCurso extends Component
{
    public function render()
    {
        // 1. Preparamos los arreglos vacíos para guardar nuestros datos
        $nombresCursos = [];
        $cantidadEstudiantes = [];

        // 2. Buscamos todos los cursos
        $cursos = Curso::all();

        // 3. Contamos cuántos estudiantes hay en cada curso
        foreach ($cursos as $curso) {
            $total = Estudiante::where('curso_id', $curso->id)->where('estado', 'Activo')->count();

            // Solo agregamos al gráfico los cursos que tengan al menos 1 estudiante
            if ($total > 0) {
                $nombresCursos[] = $curso->curso; // Usamos tu columna 'curso'
                $cantidadEstudiantes[] = $total;
            }
        }

        // 4. ¡LA MAGIA! Construimos el gráfico en 5 líneas
        $chart = LarapexChart::barChart()
            ->setTitle('Estudiantes por Curso')
            ->setSubtitle('Cantidad de alumnos matriculados')
            ->addData($cantidadEstudiantes)
            ->setLabels($nombresCursos)
            ->setColors(['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']); // Colores modernos

        // 5. Se lo enviamos a la vista
        return view('livewire.graficos.estudiantes-por-curso', compact('chart'));
    }
}
