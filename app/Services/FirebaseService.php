<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Models\UserPushToken;
use Log;

class FirebaseService
{
    /**
     * Send a push notification to a specific user.
     *
     * @param int $userId
     * @param string $title
     * @param string $body
     * @param array $data
     * @return void
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $tokens = UserPushToken::where('user_id', $userId)->pluck('token')->toArray();

        if (empty($tokens)) {
            Log::info("No tokens found for user ID: $userId");
            return;
        }

        $messaging = Firebase::messaging();

        $notification = Notification::create($title, $body);

        // Add icon if available in data
        if (isset($data['icon'])) {
            $notification = $notification->withImageUrl($data['icon']); // Some SDKs use this for icon
        }

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        try {
            $report = $messaging->sendMulticast($message, $tokens);

            if ($report->hasFailures()) {
                Log::warning("FCM delivery had some failures. Check Firebase console for details.");
            }
        } catch (\Exception $e) {
            Log::error("Firebase sending failed: " . $e->getMessage());
        }
    }

    /**
     * Send a push notification to multiple tokens.
     *
     * @param array $tokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return void
     */
    public function sendToTokens(array $tokens, $title, $body, $data = [])
    {
        if (empty($tokens))
            return;

        $messaging = Firebase::messaging();
        $notification = Notification::create($title, $body);

        // Add icon if available in data
        if (isset($data['icon'])) {
            $notification = $notification->withImageUrl($data['icon']);
        }

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        try {
            $messaging->sendMulticast($message, $tokens);
        } catch (\Exception $e) {
            Log::error("Firebase sending to tokens failed: " . $e->getMessage());
        }
    }
}
