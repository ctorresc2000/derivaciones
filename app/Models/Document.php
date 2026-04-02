<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'mime_type',
        'size',
    ];

    // Esta función le dice a Laravel que este documento pertenece a "algo" (estudiante, intervención, etc.)
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
