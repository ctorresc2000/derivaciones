<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NotificacionCopiaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $tipoRegistro;
    public $registro;
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

    public function attachments(): array
    {
        $files = [];

        // Obtenemos los documentos asociados mediante la relación morphMany del Trait
        // Asumiendo que el modelo Document tiene un campo 'ruta_archivo' o 'path'
        foreach ($this->registro->documents as $doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                $files[] = Attachment::fromStorageDisk('public', $doc->file_path)
                    ->as($doc->name ?? 'adjunto.pdf');
            }
        }

        return $files;
    }
}
