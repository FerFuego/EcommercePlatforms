<?php

namespace App\Mail;

use App\Models\DeliveryDriver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public DeliveryDriver $driver;
    public string $rejectionReason;

    public function __construct(DeliveryDriver $driver, string $rejectionReason)
    {
        $this->driver = $driver;
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
            view: 'emails.driver-rejected',
        );
    }
}
