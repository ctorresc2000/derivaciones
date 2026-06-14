<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apoderado extends Model
{
    protected $fillable = [
        'apoderado',
        'estudiante_id',
        'rut',
        'direccion',
        'telefono',
        'correo',
        'estado',
        'tipo_apoderado',
        'carnet'
    ];

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class);
    }

}
