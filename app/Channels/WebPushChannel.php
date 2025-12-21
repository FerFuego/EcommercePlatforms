<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class WebPushChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWebPush')) {
            return;
        }

        $data = $notification->toWebPush($notifiable);

        // Aquí se integraría con Firebase Cloud Messaging (FCM) o Web Push API nativa
        // \App\Services\PushService::send($notifiable->fcm_token, $data);

        \Illuminate\Support\Facades\Log::info("WebPush Notification sent to user {$notifiable->id}: " . json_encode($data));
    }
}
