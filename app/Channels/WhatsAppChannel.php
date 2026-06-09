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

        if (is_array($message) && isset($message['type']) && $message['type'] === 'template') {
            app(WhatsAppService::class)->sendTemplateMessage(
                $to,
                $message['name'],
                $message['components'] ?? [],
                $message['language'] ?? 'es'
            );
        } else {
            app(WhatsAppService::class)->sendMessage($to, is_array($message) ? ($message['text'] ?? '') : $message);
        }
    }
}
