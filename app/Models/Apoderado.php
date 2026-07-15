<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RegistraAuditoria;

class Apoderado extends Model
{
    use RegistraAuditoria;

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
