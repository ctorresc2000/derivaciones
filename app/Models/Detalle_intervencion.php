<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_intervencion extends Model
{
    protected $fillable = [
        'intervencion_id',
        'falta_id',
        'medida_id',
        'motivo_intervencion_id',
        'tipo_intervencion_id',
        //'detalle'
    ];

    // --- RELACIONES PARA PSICOSOCIAL ---

    public function motivo()
    {
        // Relaciona el ID de la tabla detalle con el modelo Motivointervencion
        return $this->belongsTo(Motivointervencion::class, 'motivo_intervencion_id');
    }

    public function tipo()
    {
        // Relaciona el ID de la tabla detalle con el modelo Tipointervencion
        return $this->belongsTo(Tipointervencion::class, 'tipo_intervencion_id');
    }

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

    public function motivoIntervencion()
    {
        return $this->belongsTo(Motivointervencion::class, 'motivo_intervencion_id');
    }

    public function tipoIntervencion()
    {
        return $this->belongsTo(Tipointervencion::class, 'tipo_intervencion_id');
    }
}
