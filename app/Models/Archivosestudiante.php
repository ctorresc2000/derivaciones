<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivosestudiante extends Model
{
    protected $fillable = [
        'estudiante_id',
        'ruta_archivo',
        'nombre_original',
        'extension'
    ];

    // Este archivo le pertenece a un estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }
}
