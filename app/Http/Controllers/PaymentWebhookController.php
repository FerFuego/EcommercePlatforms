<?php

namespace App\Http\Controllers;

use App\Models\Cook;
use App\Models\CookSubscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle MercadoPago Webhooks/IPN
     */
    public function handleMercadoPago(Request $request)
    {
        Log::info('MercadoPago Webhook Received', $request->all());

        $topic = $request->input('topic') ?? $request->input('type');
        $id = $request->input('id') ?? ($request->input('data')['id'] ?? null);

        if (!$id) {
            return response()->json(['message' => 'No ID provided'], 400);
        }

        // We only care about payments or merchant_orders for confirmation
        if ($topic === 'payment' || $topic === 'merchant_order') {
            return $this->processMercadoPagoPayment($id, $topic);
        }

        return response()->json(['message' => 'Topic ignored'], 200);
    }

    protected function processMercadoPagoPayment($id, $topic)
    {
        $accessToken = \App\Models\Setting::get('mp_access_token');
        \MercadoPago\MercadoPagoConfig::setAccessToken($accessToken);

        try {
            if ($topic === 'payment') {
                $client = new \MercadoPago\Client\Payment\PaymentClient();
                $payment = $client->get($id);
                $externalReference = $payment->external_reference;
                $status = $payment->status;
            } else {
                // Topic is merchant_order
                $client = new \MercadoPago\Client\MerchantOrder\MerchantOrderClient();
                $order = $client->get($id);
                $externalReference = $order->external_reference;
                $status = $order->status === 'closed' ? 'approved' : 'pending';
            }

            if ($status === 'approved' && $externalReference) {
                return $this->activateSubscriptionFromReference($externalReference, 'mercadopago', $id);
            }

        } catch (\Exception $e) {
            Log::error('MP Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Payment not approved yet'], 200);
    }

    protected function activateSubscriptionFromReference($reference, $gateway, $paymentId)
    {
        // Reference format: cook_sub_{cook_id}_{plan_id}
        $parts = explode('_', $reference);
        if (count($parts) < 4) {
            return response()->json(['message' => 'Invalid reference'], 400);
        }

        $cookId = $parts[2];
        $planId = $parts[3];

        $cook = Cook::find($cookId);
        $plan = SubscriptionPlan::find($planId);

        if (!$cook || !$plan) {
            return response()->json(['message' => 'Cook or Plan not found'], 404);
        }

        // Create or Update Subscription
        $subscription = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'provider' => $gateway,
            'provider_subscription_id' => $paymentId,
            'current_period_start' => now(),
            'current_period_end' => $plan->billing_period === 'monthly' ? now()->addMonth() : now()->addYear(),
        ]);

        // Log Payment History
        SubscriptionPayment::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => 'ARS',
            'provider' => $gateway,
            'provider_payment_id' => $paymentId,
            'status' => 'approved',
            'paid_at' => now(),
        ]);

        $cook->update([
            'current_subscription_id' => $subscription->id,
            'monthly_sales_accumulated' => 0,
            'monthly_orders_accumulated' => 0,
            'is_selling_blocked' => false,
        ]);

        return response()->json(['message' => 'Subscription activated'], 200);
    }
}
