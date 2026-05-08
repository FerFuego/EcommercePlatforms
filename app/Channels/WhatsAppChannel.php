<?php

namespace App\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        $to = $notifiable->routeNotificationFor('whatsapp', $notification);

        if (!$to) {
            $to = $notifiable->phone;
        }

        if (!$to || !$message) {
            return;
        }

        app(WhatsAppService::class)->sendMessage($to, $message);
    }
}
