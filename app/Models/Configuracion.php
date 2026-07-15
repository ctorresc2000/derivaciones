<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $fillable = [
        'nombre_institucion',
        'domicilio',
        'telefono',
        'email',
        'logo',
    ];
}
