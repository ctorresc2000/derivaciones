<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipoprofesional extends Model
{
    protected $table = 'tipo_profesionals';

    protected $fillable = [
        'tipo',
        'departamento'
    ];

    // Un Tipo tiene Muchos Usuarios (Profesionales)
    public function usuarios()
    {
        return $this->hasMany(User::class, 'tipo_profesional_id');
    }
}
