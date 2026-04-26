<?php

namespace App\Mail;

use App\Models\Cook;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CookApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cook $cook;

    public function __construct(Cook $cook)
    {
        $this->cook = $cook;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a Cocinarte! Tu cuenta fue aprobada 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cook-approved',
        );
    }
}
