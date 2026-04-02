<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDocuments; // 1. Importas el Trait

class Intervencion extends Model
{
    use HasDocuments; // 2. Lo usas dentro de la clase

    protected $fillable = [
        'estudiante_id',
        'usuario_id',
        'via_ingreso_id',
        'descripcion',
        'estado',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'date', // Lo convierte a un objeto Carbon (solo fecha)
    ];

    // 3. Relaciones (para poder hacer $intervencion->estudiante->nombre)
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(Detalle_intervencion::class);
    }

    public function archivos()
    {
        return $this->hasMany(Intervencionarchivo::class);
    }

    public function viaIngreso()
    {
        // Una intervención pertenece a una vía de ingreso
        return $this->belongsTo(Viaingreso::class, 'via_ingreso_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function acciones()
    {
        return $this->hasMany(AccionIntervencion::class);
    }

    // public function profesionalDerivado()
    // {
    //     // Le decimos que profesional_derivado_id pertenece a un Usuario (User)
    //     return $this->belongsTo(User::class, 'profesional_derivado_id');
    // }
}
