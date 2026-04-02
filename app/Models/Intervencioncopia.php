<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervencioncopia extends Model
{
    protected $fillable = [
        'intervencion_id',
        'usuario_id',
    ];

    // Relación con la intervención
    public function intervencion()
    {
        return $this->belongsTo(Intervencion::class);
    }

    // Relación con el usuario que recibe la copia
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
