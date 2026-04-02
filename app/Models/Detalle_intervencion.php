<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_intervencion extends Model
{
    protected $fillable = [
        'intervencion_id',
        'falta_id',
        'medida_id',
    ];

    public function intervencion()
    {
        return $this->belongsTo(Intervencion::class);
    }

    public function falta()
    {
        return $this->belongsTo(Falta::class);
    }

    public function medida()
    {
        return $this->belongsTo(Medida::class);
    }
}
