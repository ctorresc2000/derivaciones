<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Agregamos "implements ShouldBroadcastNow"
class NuevaNotificacionEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        // Aquí podrías pasar datos, pero por ahora solo queremos avisar que algo pasó
    }

    public function broadcastOn(): array
    {
        // Creamos un canal público llamado 'canal-notificaciones'
        return [
            new Channel('canal-notificaciones'),
        ];
    }
}
