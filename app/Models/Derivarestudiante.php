<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDocuments; // 1. Importas el Trait

class Derivarestudiante extends Model
{
    use HasDocuments; // 2. Lo usas dentro de la clase

    protected $fillable = [
        'fecha_derivacion',
        'user_id',
        'estudiante_id',
        'motivo_derivacion',
        'previos_derivacion',
        'profesional_derivado_id',
        'detalle_derivacion',
        'estado'
    ];

    // 👇 Agrega esta relación 👇
    public function profesional()
    {
        // Una derivación "pertenece a" un profesional
        return $this->belongsTo(User::class, 'profesional_derivado_id');
    }

    // 👇 La relación para traer los datos del estudiante 👇
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class); // Asegúrate de importar el modelo Estudiante arriba
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function acciones()
    {
        return $this->hasMany(AccionDerivacion::class, 'derivarestudiante_id');
    }

    public function profesionalDerivado()
    {
        // Esto le dice que busque el id en la tabla Users
        return $this->belongsTo(User::class, 'profesional_derivado_id');
    }

    public function motivo()
    {
        // Conecta la columna motivo_derivacion con la tabla Motivointervencion
        return $this->belongsTo(Motivointervencion::class, 'motivo_derivacion');
    }
    // public function detalles()
    // {
    //     return $this->hasMany(DetalleIntervencion::class, 'intervencion_id');
    // }

    public function falta()
    {
        return $this->belongsTo(Falta::class, 'falta_id');
    }


}
