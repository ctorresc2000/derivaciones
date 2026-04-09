<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionCopiaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $tipoRegistro;
    public $registro; // Aquí guardaremos la intervención o derivación completa
    public $datosVista;

    public function __construct($estudiante, $tipoRegistro, $registro, $datosVista = [])
    {
        $this->estudiante = $estudiante;
        $this->tipoRegistro = $tipoRegistro;
        $this->registro = $registro;
        $this->datosVista = $datosVista;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación: ' . $this->tipoRegistro . ' - ' . $this->estudiante->nombre . ' ' . $this->estudiante->apellido,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion-copia',
        );
    }
}
