<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    //protected $table = 'profesionales';

    protected $fillable = [
        'nombre',
        'tipo',
        'email',
        'observacion',
        'estado',
    ];

    // 👇 Agrega esta relación inversa 👇
    public function derivaciones()
    {
        // Un profesional "tiene muchas" derivaciones
        return $this->hasMany(Derivarestudiante::class, 'profesional_derivado_id');
    }
}
