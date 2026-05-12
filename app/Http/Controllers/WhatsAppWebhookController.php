<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token');

        $mode = $_GET['hub_mode'] ?? $_GET['hub.mode'] ?? null;
        $token = $_GET['hub_verify_token'] ?? $_GET['hub.verify_token'] ?? null;
        $challenge = $_GET['hub_challenge'] ?? $_GET['hub.challenge'] ?? null;

        Log::info('WhatsApp verify', [
            'mode' => $mode,
            'token' => $token,
            'token_expected' => $verifyToken,
            'challenge' => $challenge,
        ]);

        if ($mode === 'subscribe' && $token === $verifyToken && $challenge) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('WhatsApp webhook event', $payload);

        $entry = $payload['entry'][0] ?? null;
        if (!$entry) {
            return response()->json(['status' => 'ok'], 200);
        }

        $changes = $entry['changes'][0] ?? null;
        if (!$changes) {
            return response()->json(['status' => 'ok'], 200);
        }

        $value = $changes['value'] ?? [];
        $messages = $value['messages'] ?? [];
        $statuses = $value['statuses'] ?? [];

        foreach ($statuses as $status) {
            Log::info('WhatsApp message status update', [
                'message_id' => $status['id'] ?? null,
                'status' => $status['status'] ?? null,
                'timestamp' => $status['timestamp'] ?? null,
            ]);
        }

        foreach ($messages as $message) {
            $from = $message['from'] ?? 'unknown';
            $text = $message['text']['body'] ?? '';
            $msgId = $message['id'] ?? '';

            Log::info("WhatsApp message from {$from}: {$text}", [
                'message_id' => $msgId,
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
