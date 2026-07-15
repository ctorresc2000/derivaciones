<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RegistraAuditoria;

class Curso extends Model
{
    use HasFactory;
    use RegistraAuditoria;

    protected $fillable = [
        'curso',
        'descripcion',
        'estado',
        'user_id',
    ];

    public function profesorJefe()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
