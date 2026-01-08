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

        // Get tokens for the user
        $tokens = \App\Models\UserPushToken::where('user_id', $notifiable->id)->pluck('token')->toArray();

        if (empty($tokens)) {
            \Illuminate\Support\Facades\Log::info("No push tokens found for user {$notifiable->id}");
            return;
        }

        $firebaseService = new \App\Services\FirebaseService();
        $firebaseService->sendToTokens(
            $tokens,
            $data['title'] ?? 'Notificación',
            $data['body'] ?? '',
            $data['data'] ?? []
        );

        \Illuminate\Support\Facades\Log::info("WebPush Notification sent to user {$notifiable->id} via Firebase");
    }
}
