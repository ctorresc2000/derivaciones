<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudianteCurso extends Model
{
    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'ano_academico',
        'condicion',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
