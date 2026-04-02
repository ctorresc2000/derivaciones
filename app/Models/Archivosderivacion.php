<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivosderivacion extends Model
{
    protected $fillable = [
        'derivarestudiante_id',
        'ruta_archivo',
        'nombre_original',
        'extension'
    ];

    // Un archivo pertenece a una derivación
    public function derivacion()
    {
        return $this->belongsTo(Derivarestudiante::class, 'derivarestudiante_id');
    }
}
