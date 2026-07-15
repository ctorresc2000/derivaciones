<?php

namespace App\Models;

use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\RegistraAuditoria;
use App\Traits\HasDocuments;

class Entrevista extends Model
{
    use RegistraAuditoria;
    use HasDocuments;

    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'user_id',
        'es_apoderado',
        'nombre_apoderado',
        'motivo',
        'detalle',
        'fecha',
        'otp_email_verified',
        'otp_codigo',
        'otp_email',
        'otp_verified_at',
        'firma',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'fecha' => 'date',
        'es_apoderado' => 'boolean',
        'otp_verified_at' => 'datetime',

    ];

    /**
     * Relación: La entrevista pertenece a un estudiante.
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * Relación: La entrevista se asocia a un curso (contexto escolar).
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Relación: El usuario (profesional/psicólogo) que realizó la entrevista.
     */
    public function profesional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        // Es vital especificar 'user_id' porque así se llama tu columna en la migración
        return $this->belongsTo(User::class, 'user_id');
    }
}
