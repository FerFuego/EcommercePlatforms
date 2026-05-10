<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token');

        $all = $request->query->all();
        $mode = $all['hub.mode'] ?? $all['hub_mode'] ?? null;
        $token = $all['hub.verify_token'] ?? $all['hub_verify_token'] ?? null;
        $challenge = $all['hub.challenge'] ?? $all['hub_challenge'] ?? null;

        Log::info('WhatsApp webhook verify', [
            'mode' => $mode,
            'token' => $token,
            'challenge' => $challenge,
        ]);

        if ($mode === 'subscribe' && $token === $verifyToken && $challenge) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
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

        // Handle incoming messages if needed
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
