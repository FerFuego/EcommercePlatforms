<?php

namespace App\Mail;

use App\Models\DeliveryDriver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public DeliveryDriver $driver;

    public function __construct(DeliveryDriver $driver)
    {
        $this->driver = $driver;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a Cocinarte! Tu cuenta de repartidor fue aprobada 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-approved',
        );
    }
}
