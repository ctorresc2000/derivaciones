<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionDerivacion extends Model
{
    protected $fillable = ['derivarestudiante_id', 'user_id', 'fecha', 'descripcion'];

    protected $casts = [
        'fecha' => 'date',
    ];

    // Relación de vuelta a la derivación
    public function derivacion()
    {
        return $this->belongsTo(Derivarestudiante::class, 'derivarestudiante_id');
    }

    // Quién registró esta acción
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
