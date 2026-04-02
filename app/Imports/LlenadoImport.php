<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LlenadoImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            // 'Nombre de la hoja' => Modelo a usar, 'Columna principal para evitar duplicados'
            'Viaingreso'         => new GenericSheetImport(\App\Models\Viaingreso::class, 'via_ingreso'),
            'Falta'              => new GenericSheetImport(\App\Models\Falta::class, 'falta'),
            'Medida'             => new GenericSheetImport(\App\Models\Medida::class, 'medida'),
            'Motivointervencion' => new GenericSheetImport(\App\Models\Motivointervencion::class, 'motivo'),
            'Tipointervencion'   => new GenericSheetImport(\App\Models\Tipointervencion::class, 'tipo'),
            'Tipoprofesional'    => new GenericSheetImport(\App\Models\Tipoprofesional::class, 'tipo'),
            'User'               => new GenericSheetImport(\App\Models\User::class, 'email'),
        ];
    }
}
