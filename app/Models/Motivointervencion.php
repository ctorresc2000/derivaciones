<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motivointervencion extends Model
{
    protected $fillable = [
        'motivo',
    ];

    public function derivaciones()
    {
        return $this->hasMany(Derivarestudiante::class, 'motivo_derivacion');
    }
}
