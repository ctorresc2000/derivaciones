<?php

namespace App\Imports;

use App\Models\Estudiante;
use App\Models\Curso;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation; // 👈 1. Herramienta para validar
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // 👈 2. Herramienta para saltar errores
use Maatwebsite\Excel\Concerns\SkipsFailures;  // 👈 3. Activa el salto de filas
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

// Agregamos WithValidation y SkipsOnFailure a la clase
class EstudiantesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures; // 👈 Activamos la magia para que no se caiga al encontrar un duplicado

    private $cursos;

    public function __construct()
    {
        $cursosDb = Curso::all();
        $this->cursos = [];
        foreach($cursosDb as $curso) {
            // TRUCO INFALIBLE: Quitamos todos los espacios y pasamos a minúscula
            // Ej: " 1° A " se convierte internamente en "1°a"
            $nombreLimpio = mb_strtolower(str_replace(' ', '', $curso->curso));
            $this->cursos[$nombreLimpio] = $curso->id;
        }
    }

    public function model(array $row)
    {
        // 1. Buscamos el curso aplicando el mismo truco al texto del Excel
        $cursoExcelLimpio = isset($row['curso']) ? mb_strtolower(str_replace(' ', '', $row['curso'])) : '';
        $cursoId = $this->cursos[$cursoExcelLimpio] ?? null;

        // 2. Fechas (ya lo teníamos arreglado)
        $fechaNacimiento = null;
        if (!empty($row['fecha_nacimiento'])) {
            if (is_numeric($row['fecha_nacimiento'])) {
                $fechaNacimiento = Date::excelToDateTimeObject($row['fecha_nacimiento'])->format('Y-m-d');
            } else {
                try {
                    $fechaNacimiento = Carbon::parse($row['fecha_nacimiento'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaNacimiento = null;
                }
            }
        }

        // 3. Guardamos
        return new Estudiante([
            'rut'              => $row['rut'],
            'nombre'           => $row['nombre'],
            'apellido'         => $row['apellido'],
            'email'            => $row['email'] ?? null,
            'telefono'         => $row['telefono'] ?? null,
            'domicilio'        => $row['domicilio'] ?? null,
            'fecha_nacimiento' => $fechaNacimiento,
            'curso_id'         => $cursoId,
            'social'           => $row['social'] ?? null,
            'observaciones'    => $row['observacion'] ?? null,
            'estado'           => $row['estado'] ?? 'Activo',
        ]);
    }

    // 👇 4. REGLAS DE VALIDACIÓN: El secreto para saltar duplicados 👇
    public function rules(): array
    {
        return [
            // Le decimos: "El rut debe ser único en la tabla estudiantes"
            // Si Laravel detecta que ya existe, cancela esta fila y pasa a la siguiente.
            'rut' => 'unique:estudiantes,rut',

            // Lo mismo para el email (si es que viene en el Excel)
            'email' => 'nullable|unique:estudiantes,email',
        ];
    }
}
