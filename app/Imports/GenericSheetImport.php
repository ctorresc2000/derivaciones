<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GenericSheetImport implements ToCollection, WithHeadingRow
{
    protected $modelClass;
    protected $uniqueKey;

    // Recibimos qué Modelo vamos a usar y cuál es la columna que no debe repetirse
    public function __construct($modelClass, $uniqueKey)
    {
        $this->modelClass = $modelClass;
        $this->uniqueKey = $uniqueKey;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Si la fila está vacía, la saltamos
            if (!isset($row[$this->uniqueKey])) {
                continue;
            }

            // firstOrCreate busca si ya existe el registro (ej: si ya existe esa falta).
            // Si NO existe, lo crea usando todos los datos de la fila ($row->toArray()).
            // ¡Esto evita los duplicados mágicamente!
            $this->modelClass::firstOrCreate(
                [$this->uniqueKey => $row[$this->uniqueKey]],
                $row->toArray()
            );
        }
    }
}
