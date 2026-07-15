<?php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use Livewire\Component;

class HistorialComponent extends Component
{
    public $estudiante;
    public $intervenciones = [];
    public $derivaciones = [];
    public $activeTab = 'intervenciones'; // Controla la pestaña activa
    public $verArchivosModal = false;
    public $documentosMostrar = [];

    public function mount($id)
    {
        $this->estudiante = Estudiante::with([
            'intervenciones.usuario.tipoProfesional',
            'intervenciones.detalles.falta',
            'intervenciones.detalles.medida',
            'intervenciones.detalles.motivo',
            'intervenciones.detalles.tipo',
            'intervenciones.viaIngreso',
            'intervenciones.archivos',
            'derivaciones.user',
            'derivaciones.motivo',
            'derivaciones.acciones.usuario',
            'derivaciones.documents',
            'derivaciones.profesional',
            'redes',
        ])->findOrFail($id);

        $this->prepararDatos();
    }

    public function prepararDatos()
    {
        $obtenerColor = function($estado) {
            return match($estado) {
                'Abierta', 'Pendiente' => 'bg-green-100 text-green-800 border-green-200',
                'Derivada', 'En Proceso' => 'bg-blue-100 text-blue-800 border-blue-200',
                default => 'bg-slate-100 text-slate-800 border-slate-200',
            };
        };

        // Procesar Intervenciones
        $this->intervenciones = $this->estudiante->intervenciones->map(function ($item) use ($obtenerColor) {
            return [
                'id'           => $item->id,
                'fecha'        => $item->fecha->format('d/m/Y'),
                'via'          => $item->viaIngreso->via_ingreso ?? 'N/A',
                'profesional'  => $item->usuario->name,
                'area'         => $item->usuario->tipoProfesional->departamento ?? 'Convivencia',
                'descripcion'  => $item->descripcion,
                'estado'       => $item->estado,
                'color_estado' => $obtenerColor($item->estado),
                'detalles'     => $item->detalles,
                'acciones' => $item->acciones,
                'documentos'   => $item->documents,

            ];
        })->sortByDesc('id')->values()->all();

        // Procesar Derivaciones
        $this->derivaciones = $this->estudiante->derivaciones->map(function ($item) use ($obtenerColor) {
            return [
                'id'           => $item->id,
                'fecha'        => $item->created_at->format('d/m/Y'),
                'profesional_derivado'  => $item->profesional->name ?? 'N/A',
                'motivo'       => $item->motivo->motivo ?? 'General',
                'profesional'  => $item->user->name,
                'tipo'         => $item->tipo_intervencion,
                'detalle'      => $item->detalle_derivacion,
                'conclusiones' => $item->conclusiones,
                'estado'       => $item->estado,
                'color_estado' => $obtenerColor($item->estado),
                'documentos'   => $item->documents,
                'acciones'     => $item->acciones,
            ];
        })->sortByDesc('id')->values()->all();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function abrirModalArchivos($id, $tipo = 'intervencion')
    {
        // Buscamos en la colección correspondiente
        $modelo = ($tipo === 'intervencion') ? $this->estudiante->intervenciones : $this->estudiante->derivaciones;
        $registro = $modelo->find($id);

        if ($registro) {
            // IMPORTANTE: Ahora ambos usan la relación 'documents' del Trait
            $this->documentosMostrar = $registro->documents;
            $this->verArchivosModal = true;
        }
    }

    public function render()
    {
        return view('livewire.estudiante.historial-component');
    }
}
