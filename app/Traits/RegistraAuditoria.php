<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait RegistraAuditoria
{
    // 1. Importamos la magia de Spatie
    use LogsActivity;

    // 2. Centralizamos la configuración para todos los modelos que usen este Trait
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Vigila todos los campos ($fillable)
            ->logOnlyDirty() // Solo guarda si hay cambios reales
            ->dontSubmitEmptyLogs(); // Evita guardar basura
    }
}
