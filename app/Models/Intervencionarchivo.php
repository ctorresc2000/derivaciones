<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervencionarchivo extends Model
{
    protected $fillable = [
        'intervencion_id',
        'ruta',
        'nombre_original',
    ];

    public function intervencion()
    {
        return $this->belongsTo(Intervencion::class);
    }
}
