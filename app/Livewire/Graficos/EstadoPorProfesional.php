<?php

namespace App\Livewire\Graficos;

use Livewire\Component;
use App\Models\Derivarestudiante;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class EstadoPorProfesional extends Component
{
    public function render()
    {
        $anioActivo = session('anio_activo', date('Y'));

        // 1. Traemos las derivaciones del año con su profesional asignado
        $derivaciones = Derivarestudiante::with('profesional')
            ->whereYear('created_at', $anioActivo)
            ->get();

        // 2. Agrupamos por el ID del profesional
        $agrupadas = $derivaciones->groupBy('profesional_derivado_id');

        $nombres = [];
        $pendientes = [];
        $enProceso = [];
        $cerradas = [];

        // 3. Contamos los estados y FILTRAMOS los que están en cero
        foreach ($agrupadas as $grupo) {
            // Hacemos el conteo antes de guardarlos
            $cantPendiente = $grupo->where('estado', 'Pendiente')->count();
            $cantEnProceso = $grupo->where('estado', 'En Proceso')->count();
            $cantCerrada = $grupo->where('estado', 'Cerrado')->count();

            // LA MAGIA: Solo si la suma de sus casos activos es mayor a 0, lo mostramos
            if (($cantPendiente + $cantEnProceso + $cantCerrada) > 0) {
                $nombres[] = $grupo->first()->profesional->name ?? 'Sin Asignar';
                $pendientes[] = $cantPendiente;
                $enProceso[] = $cantEnProceso;
                $cerradas[] = $cantCerrada;
            }
        }

        // 4. Armamos el Gráfico
        $chart = LarapexChart::barChart()
            ->setTitle('Carga de Trabajo por Funcionario')
            ->setSubtitle('Detalle de estados (Pendiente, En Proceso, Cerrada)')
            ->setDataset([
                ['name' => 'Pendientes', 'data' => $pendientes],
                ['name' => 'En Proceso', 'data' => $enProceso],
                ['name' => 'Cerradas', 'data' => $cerradas],
            ])
            ->setXAxis($nombres)
            ->setHeight(350)
            ->setColors(['#ef4444', '#f59e0b', '#10b981']);

        return view('livewire.graficos.estado-por-profesional', compact('chart'));
    }
}
