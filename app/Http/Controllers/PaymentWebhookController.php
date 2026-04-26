<?php

namespace App\Http\Controllers;

use App\Models\Cook;
use App\Models\Order;
use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }
    /**
     * Handle MercadoPago Webhooks/IPN
     */
    public function handleMercadoPago(Request $request)
    {
        $payload = $request->all();
        Log::info('MercadoPago Webhook Received', $payload);

        $type = $payload['type'] ?? $payload['topic'] ?? null;
        $data = $payload['data'] ?? $payload;
        $id = $data['id'] ?? null;

        if (!$id) {
            return response()->json(['message' => 'No ID provided'], 400);
        }

        // 1. Check if it's a product order payment
        // (Assuming orders have a specific external_reference pattern like 'ORD_')
        // We'll let the existing logic handle it or delegate to an OrderService in the future.
        if ($type === 'payment' || $type === 'merchant_order') {
            // Check if this payment belongs to a subscription
            // The SubscriptionService will check if 'preapproval_id' exists in the payment details
            $handled = $this->subscriptionService->handleWebhook($payload);

            if ($handled) {
                return response()->json(['status' => 'success', 'context' => 'subscription']);
            }

            // If not handled by subscription, it might be a product order
            return $this->processProductOrder($id, $type);
        }

        // 2. Subscription lifecycle events (preapproval)
        if (in_array($type, ['preapproval', 'subscription_preapproval', 'subscription_authorized_payment'])) {
            $this->subscriptionService->handleWebhook($payload);
            return response()->json(['status' => 'success', 'context' => 'subscription_lifecycle']);
        }

        return response()->json(['message' => 'Topic ignored'], 200);
    }

    /**
     * Placeholder/Legacy logic for processing product orders (not subscriptions)
     */
    protected function processProductOrder($id, $topic)
    {
        // For now, we'll just log it. If the app has product orders, 
        // they should be handled here or in an OrderService.
        Log::info("Processing non-subscription payment/order: $id (Topic: $topic)");
        return response()->json(['message' => 'Payment processed (non-subscription)'], 200);
    }
}
