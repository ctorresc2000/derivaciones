<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class NotificacionDerivacion extends Mailable
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $tipoRegistro;
    public $registro;
    public $datosVista;

    // Recibimos los datos del estudiante desde tu componente
    public function __construct($estudiante)
    {
        $this->estudiante = $estudiante;

    }

    // Configuramos el "Asunto" del correo
    public function envelope(): Envelope
    {
        $nombreRemitente = ($this->tipoRegistro === 'Derivación');

        return new Envelope(
            from: new Address(config('mail.from.address'), $nombreRemitente),
            subject: 'Nueva Derivación Asignada: ' . $this->estudiante->nombre . ' ' . $this->estudiante->apellido,
        );
    }

    // Le decimos a Laravel qué vista (HTML) usar para el cuerpo del correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion-derivacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
