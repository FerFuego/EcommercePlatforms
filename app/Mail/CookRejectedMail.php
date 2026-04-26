<?php

namespace App\Mail;

use App\Models\Cook;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CookRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cook $cook;
    public string $rejectionReason;

    public function __construct(Cook $cook, string $rejectionReason)
    {
        $this->cook = $cook;
        $this->rejectionReason = $rejectionReason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualización sobre tu solicitud en Cocinarte',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cook-rejected',
        );
    }
}
