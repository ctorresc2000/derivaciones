<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Entrevista; // Usamos tu modelo
use App\Models\Curso;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class EntrevistasTipo extends Component
{
    public $detalleEntrevistas = [];

    protected $listeners = ['abrirModalDetalle' => 'cargarDetalles'];

    public function render()
    {
        $anio = session('anio_activo', date('Y'));

        // Cargamos entrevistas con relaciones necesarias
        $entrevistas = Entrevista::with(['estudiante', 'profesional', 'curso'])
            ->whereYear('fecha', $anio)
            ->get();

        $cursos = Curso::whereNotIn('curso', [
                'Egresadas Párvulos',
                'Egresadas Gastronomía',
                'Egresadas Vestuario'
            ])
            ->orderBy('curso')
            ->get();

        $nombresCursos = [];
        $estudiantesData = [];
        $apoderadosData = [];

        foreach ($cursos as $curso) {
            $nombresCursos[] = $curso->curso;

            // Si es_apoderado es false, es entrevista a estudiante
            $estudiantesData[] = $entrevistas->where('curso_id', $curso->id)
                                             ->where('es_apoderado', false)->count();

            // Si es_apoderado es true, es entrevista a apoderado
            $apoderadosData[] = $entrevistas->where('curso_id', $curso->id)
                                             ->where('es_apoderado', true)->count();
        }

        $chart = LarapexChart::barChart()
            ->setTitle('Entrevistas por Curso')
            ->setSubtitle('Comparativa Estudiantes vs Apoderados')
            ->setDataset([
                ['name' => 'Entrevistas Estudiantes', 'data' => $estudiantesData],
                ['name' => 'Entrevistas Apoderados', 'data' => $apoderadosData]
            ])
            ->setXAxis($nombresCursos)
            ->setHeight(350)
            ->setColors(['#3b82f6', '#f59e0b']); // Azul y Ámbar

        return view('livewire.graficos.entrevistas-tipo', compact('chart'));
    }

    public function cargarDetalles()
    {
        $this->detalleEntrevistas = Entrevista::with(['estudiante', 'profesional'])->latest()->get();
        // Disparamos el evento al navegador
        $this->dispatch('abrir-modal-detalle');
    }
}
