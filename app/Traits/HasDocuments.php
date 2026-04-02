<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocuments
{
    // Esta relación permite obtener los documentos adjuntos de cualquier modelo que use este Trait
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
