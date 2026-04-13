<?php

namespace App\Http\Controllers;

use App\Models\Entrevista;
use Barryvdh\DomPDF\Facade\Pdf;

class EntrevistaExportController extends Controller
{
    public function download(Entrevista $entrevista)
    {
        // Cargamos relaciones para evitar consultas extra en la vista
        $entrevista->load(['estudiante', 'curso', 'user']);

        $pdf = Pdf::loadView('pdf.entrevista-pdf', compact('entrevista'));

        // Configuración para permitir imágenes base64 (la firma)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('entrevista-'.$entrevista->estudiante->apellido.'.pdf');
    }
}
