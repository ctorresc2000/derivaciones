<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDocuments; // 1. Importas el Trait

class Estudiante extends Model
{

    use HasDocuments; // 2. Lo usas dentro de la clase

    protected $fillable = [
        'nombre',
        'apellido',
        'rut',
        'social',
        'fecha_nacimiento',
        'domicilio',
        'email',
        'telefono',
        'curso_id',
        'observaciones',
        'estado',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function intervenciones()
    {
        return $this->hasMany(Intervencion::class, 'estudiante_id');
    }

    public function derivaciones()
    {
        return $this->hasMany(Derivarestudiante::class, 'estudiante_id');
    }

    public function redes()
    {
        return $this->belongsToMany(RedesApoyo::class, 'estudiante_red_apoyo', 'estudiante_id', 'red_apoyo_id')
                    ->withPivot('observacion', 'activo')
                    ->withTimestamps();
    }
}
