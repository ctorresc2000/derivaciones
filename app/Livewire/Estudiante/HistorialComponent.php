<?php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use App\Models\Intervencion;
use App\Models\Derivarestudiante;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class HistorialComponent extends Component
{
    public $estudiante;
    public $historial = [];

    // Variables para el Modal de Archivos
    public $verArchivosModal = false;
    public $documentosMostrar = [];
    public $tituloModalArchivos = '';

    public function mount($id)
    {
        // Cargamos el estudiante y sus relaciones incluyendo 'documents'
        $this->estudiante = Estudiante::with([
            'intervenciones.usuario',
            'intervenciones.detalles.falta',
            'intervenciones.documents', // Cargamos archivos de intervención
            'derivaciones.user',
            'derivaciones.profesionalDerivado',
            'derivaciones.motivo',
            'derivaciones.documents'    // Cargamos archivos de derivación
        ])->findOrFail($id);

        $this->prepararHistorial();
    }

    // public function prepararHistorial()
    // {
    //     $obtenerColorEstado = function($estado) {
    //         return match($estado) {
    //             'Pendiente', 'Abierta' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800',
    //             'En Proceso', 'Derivada' => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800',
    //             'Cerrado', 'Concluida' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
    //             default => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
    //         };
    //     };

    //     // Mapeamos las intervenciones
    //     $intervenciones = $this->estudiante->intervenciones->map(function ($item) use ($obtenerColorEstado) {
    //         return (object) [
    //             'id' => $item->id,
    //             'modelo_tipo' => 'intervencion',
    //             'cantidad_documentos' => $item->documents->count(),
    //             'tipo_registro' => 'Intervención de convivencia escolar',
    //             'fecha' => $item->fecha_incidente ?? $item->created_at,
    //             'hora' => $item->created_at->format('H:i'),
    //             'profesional' => ($item->usuario->name ?? 'Usuario Desconocido'),
    //             'detalle' => $item->descripcion,
    //             'estado' => $item->estado,
    //             'color_estado' => $obtenerColorEstado($item->estado),
    //             'icono' => 'fa-hand-holding-heart',
    //             'color' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400',
    //         ];
    //     });

    //     // Mapeamos las derivaciones
    //     $derivaciones = $this->estudiante->derivaciones->map(function ($item) use ($obtenerColorEstado) {
    //         $nombreMotivo = $item->motivo->motivo ?? 'Motivo desconocido';

    //         return (object) [
    //             'id' => $item->id,
    //             'modelo_tipo' => 'derivacion',
    //             'cantidad_documentos' => $item->documents->count(),
    //             'tipo_registro' => 'Derivación: ' . $nombreMotivo,
    //             'fecha' => $item->fecha_derivacion,
    //             'hora' => $item->created_at->format('H:i'),
    //             'profesional' => ($item->profesionalDerivado->name ?? $item->user->name ?? 'Usuario Desconocido'),
    //             'detalle' => $item->detalle_derivacion,
    //             'estado' => $item->estado,
    //             'color_estado' => $obtenerColorEstado($item->estado),
    //             'icono' => 'fa-file-export',
    //             'color' => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/40 dark:text-fuchsia-400',
    //         ];
    //     });

    //     $coleccionCompleta = $intervenciones->concat($derivaciones);
    //     $this->historial = $coleccionCompleta->sortByDesc(function ($item) {
    //         return \Carbon\Carbon::parse($item->fecha)->format('Y-m-d') . ' ' . $item->hora;
    //     })->values()->all();
    // }

    public function mostrarArchivos($id, $tipo)
    {
        if ($tipo === 'intervencion') {
            $modelo = Intervencion::with('documents')->find($id);
            $this->tituloModalArchivos = "Adjuntos: Intervención de Convivencia";
        } elseif ($tipo === 'derivacion') { // Cambiamos el 'else' por 'elseif'
            $modelo = Derivarestudiante::with('documents')->find($id);
            $this->tituloModalArchivos = "Adjuntos: Derivación Psicosocial";
        } elseif ($tipo === 'estudiante') { // Agregamos la lógica del estudiante
            $modelo = Estudiante::with('documents')->find($id);
            $this->tituloModalArchivos = "Documentos Base del Estudiante";
        }

        if (isset($modelo) && $modelo) {
            $this->documentosMostrar = $modelo->documents;
            $this->verArchivosModal = true;
        }
    }

    public function prepararHistorial()
    {
        $obtenerColorEstado = function($estado) {
            return match($estado) {
                'Pendiente', 'Abierta' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800',
                'En Proceso', 'Derivada' => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800',
                'Cerrado', 'Concluida' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                default => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            };
        };

        // Mapeamos las intervenciones (Convivencia)
        $intervenciones = $this->estudiante->intervenciones->map(function ($item) use ($obtenerColorEstado) {
            return (object) [
                'id' => $item->id,
                'modelo_tipo' => 'intervencion',
                'cantidad_documentos' => $item->documents->count(),
                'tipo_registro' => 'Intervención de convivencia escolar',
                'fecha' => $item->fecha_incidente ?? $item->created_at,
                'hora' => $item->created_at->format('H:i'),
                'profesional' => ($item->usuario->name ?? 'Usuario Desconocido'),
                'detalle' => $item->descripcion,
                'estado' => $item->estado,
                'color_estado' => $obtenerColorEstado($item->estado),
                'icono' => 'fa-hand-holding-heart',
                'color' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400',
                // DATOS ESPECÍFICOS PARA EL PDF
                'etiqueta_1' => 'Tipo de Falta',
                'valor_1' => $item->detalles->first()->falta->nombre ?? $item->detalles->first()->falta->tipo_falta ?? 'No especificada',
                'etiqueta_2' => 'Tipo de Medida',
                'valor_2' => $item->detalles->first()->medida->nombre ?? $item->detalles->first()->tipo_medida ?? 'No especificada',
            ];
        });

        // Mapeamos las derivaciones (Psicosocial)
        $derivaciones = $this->estudiante->derivaciones->map(function ($item) use ($obtenerColorEstado) {
            $nombreMotivo = $item->motivo->motivo ?? 'Motivo desconocido';

            return (object) [
                'id' => $item->id,
                'modelo_tipo' => 'derivacion',
                'cantidad_documentos' => $item->documents->count(),
                'tipo_registro' => 'Derivación: ' . $nombreMotivo,
                'fecha' => $item->fecha_derivacion,
                'hora' => $item->created_at->format('H:i'),
                'profesional' => ($item->profesionalDerivado->name ?? $item->user->name ?? 'Usuario Desconocido'),
                'detalle' => $item->detalle_derivacion,
                'estado' => $item->estado,
                'color_estado' => $obtenerColorEstado($item->estado),
                'icono' => 'fa-file-export',
                'color' => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/40 dark:text-fuchsia-400',
                // DATOS ESPECÍFICOS PARA EL PDF
                'etiqueta_1' => 'Motivo Derivación',
                'valor_1' => $nombreMotivo,
                'etiqueta_2' => 'Tipo de Intervención',
                'valor_2' => $item->tipo_intervencion ?? 'Intervención Psicosocial',
            ];
        });

        $coleccionCompleta = $intervenciones->concat($derivaciones);
        $this->historial = $coleccionCompleta->sortByDesc(function ($item) {
            return \Carbon\Carbon::parse($item->fecha)->format('Y-m-d') . ' ' . $item->hora;
        })->values()->all();
    }

    // NUEVA FUNCIÓN PARA EXPORTAR
    // public function exportarPDF()
    // {
    //     $pdf = Pdf::loadView('pdf.historial-estudiante', [
    //         'estudiante' => $this->estudiante,
    //         'historial' => $this->historial
    //     ]);

    //     // Retorna el PDF para su descarga sin salir del componente
    //     return response()->streamDownload(function () use ($pdf) {
    //         echo $pdf->output();
    //     }, 'Historial_' . $this->estudiante->rut . '.pdf');
    // }



    public function render()
    {
        return view('livewire.estudiante.historial-component');
    }
}
