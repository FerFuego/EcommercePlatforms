<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        $to = $notifiable->routeNotificationFor('whatsapp', $notification);

        if (!$to) {
            $to = $notifiable->phone; // Fallback to user phone
        }

        if (!$to || !$message) {
            return;
        }

        // Aquí se llamaría al servicio de WhatsApp (Twilio, Evolution API, etc.)
        // app(\App\Services\WhatsAppService::class)->sendMessage($to, $message);

        \Illuminate\Support\Facades\Log::info("WhatsApp Notification sent to {$to}: {$message}");
    }
}
