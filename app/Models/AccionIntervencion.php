<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionIntervencion extends Model
{
    protected $fillable = ['intervencion_id', 'user_id', 'fecha', 'descripcion'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function intervencion()
    {
        return $this->belongsTo(Intervencion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
