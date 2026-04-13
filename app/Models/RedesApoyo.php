<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedesApoyo extends Model
{
    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
    ];

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_red_apoyo', 'red_apoyo_id', 'estudiante_id')
                    ->withPivot('observacion', 'activo')
                    ->withTimestamps();
    }
}
