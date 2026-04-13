<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoFirmaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $codigo) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de Autorización - Entrevista Escolar',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-otp', // Esta es la vista que crearemos ahora
        );
    }
}
