<?php

namespace App\Services;

use App\Models\Cook;
use App\Models\CookSubscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    protected $mpService;

    public function __construct(MercadoPagoService $mpService)
    {
        $this->mpService = $mpService;
    }

    /**
     * Initiate the subscription process for a cook and a plan.
     */
    public function initiateSubscription(Cook $cook, SubscriptionPlan $plan)
    {
        return DB::transaction(function () use ($cook, $plan) {
            // 1. Check if there's an active subscription
            $currentSubscription = $cook->currentSubscription;

            if ($currentSubscription && $currentSubscription->status === 'active') {
                if ($currentSubscription->plan_id == $plan->id) {
                    return [
                        'status' => 'already_active',
                        'message' => 'Ya tienes este plan activo.'
                    ];
                }

                // For now, if they want to change, we'll suggest cancelling first 
                // or we could automate it. User said: "Upgrade/Downgrade: Cancel old -> Create new"
                $this->cancelSubscription($cook);
            }

            // 2. Create local subscription record as pending
            $subscription = CookSubscription::create([
                'cook_id' => $cook->id,
                'plan_id' => $plan->id,
                'provider' => 'mercadopago',
                'status' => 'pending',
            ]);

            // 3. Coordinate with Mercado Pago (Pre-approval)
            $siteUrl = rtrim(Setting::get('site_url') ?: config('app.url'), '/');

            $frequency = $plan->billing_period === 'monthly' ? 1 : 12;

            $mpData = [
                "payer_email" => $cook->user->email,
                "back_url" => $siteUrl . "/cook/subscription/success",
                "reason" => "Suscripción Cocinarte: " . $plan->name,
                "external_reference" => (string) $cook->id,
            ];

            if ($plan->mp_plan_id) {
                $mpData["preapproval_plan_id"] = $plan->mp_plan_id;
            } else {
                $frequency = $plan->billing_period === 'monthly' ? 1 : 12;
                $mpData["auto_recurring"] = [
                    "frequency" => $frequency,
                    "frequency_type" => "months",
                    "transaction_amount" => (float) $plan->price,
                    "currency_id" => "ARS"
                ];
            }

            $mpSubscription = $this->mpService->createSubscription($mpData);

            if (!$mpSubscription || !isset($mpSubscription->init_point)) {
                throw new \Exception('No se pudo crear la suscripción en Mercado Pago.');
            }

            // 4. Update local record with provider ID
            $subscription->update([
                'provider_subscription_id' => $mpSubscription->id,
            ]);

            return [
                'status' => 'success',
                'init_point' => $mpSubscription->init_point,
                'subscription_id' => $subscription->id
            ];
        });
    }

    /**
     * Finalize and activate a subscription after provider confirmation.
     */
    public function activateSubscription(string $providerSubscriptionId)
    {
        $mpSubscription = $this->mpService->getSubscription($providerSubscriptionId);

        if (!$mpSubscription || $mpSubscription->status !== 'authorized') {
            Log::warning("SubscriptionService: Attempted to activate non-authorized subscription: $providerSubscriptionId");
            return false;
        }

        $subscription = CookSubscription::where('provider_subscription_id', $providerSubscriptionId)->first();

        if (!$subscription) {
            Log::error("SubscriptionService: Local subscription not found for MP ID: $providerSubscriptionId");
            return false;
        }

        $subscription->update([
            'status' => 'active',
            'current_period_end' => isset($mpSubscription->next_payment_date)
                ? \Carbon\Carbon::parse($mpSubscription->next_payment_date)
                : now()->addMonth(), // Fallback
        ]);

        // Update cook's current_subscription_id
        $subscription->cook->update([
            'current_subscription_id' => $subscription->id
        ]);

        return true;
    }

    /**
     * Cancel the current subscription for a cook.
     */
    public function cancelSubscription(Cook $cook)
    {
        $subscription = $cook->currentSubscription;

        if (!$subscription) {
            return false;
        }

        if ($subscription->provider === 'mercadopago' && $subscription->provider_subscription_id) {
            $this->mpService->updateSubscription($subscription->provider_subscription_id, [
                'status' => 'cancelled'
            ]);
        }

        $subscription->update(['status' => 'cancelled']);

        // Optional: Remove from cook or keep as historical record
        // $cook->update(['current_subscription_id' => null]);

        return true;
    }

    /**
     * Handle webhook events to maintain subscription status (renewals, failures).
     */
    public function handleWebhook(array $payload)
    {
        $type = $payload['type'] ?? $payload['topic'] ?? null;
        $data = $payload['data'] ?? $payload;
        $id = $data['id'] ?? null;

        if (!$id)
            return false;

        Log::info("SubscriptionService: Processing webhook type [$type] for ID [$id]");

        switch ($type) {
            case 'subscription_preapproval':
            case 'preapproval':
                return $this->activateSubscription($id);

            case 'subscription_authorized_payment':
                return $this->handleAuthorizedPayment($id);

            case 'payment':
                // Could be a recurring payment or one-time
                return $this->handleGenericPayment($id);
        }

        return false;
    }

    /**
     * Handle the 'subscription_authorized_payment' event (recurring charge).
     */
    protected function handleAuthorizedPayment(string $paymentId)
    {
        // Fetch detailed payment info from MP
        $payment = $this->mpService->getPayment($paymentId);

        if (!$payment || $payment->status !== 'approved') {
            return false;
        }

        // The preapproval_id is usually in the payment details or external_reference
        $preapprovalId = $payment->preapproval_id ?? null;
        $subscription = null;

        if ($preapprovalId) {
            $subscription = CookSubscription::where('provider_subscription_id', $preapprovalId)->first();
        }

        // Fallback: If no preapproval_id but we have external_reference (cook_id)
        if (!$subscription && isset($payment->external_reference) && is_numeric($payment->external_reference)) {
            $subscription = CookSubscription::where('cook_id', $payment->external_reference)
                ->where('provider', 'mercadopago')
                ->whereIn('status', ['active', 'pending'])
                ->latest()
                ->first();
                
            if ($subscription) {
                Log::info("SubscriptionService: Found subscription via external_reference (cook_id: {$payment->external_reference})");
            }
        }

        if (!$subscription) {
            Log::error("SubscriptionService: Local subscription not found for MP Payment: $paymentId");
            return false;
        }

        // 1. Record the payment
        SubscriptionPayment::updateOrCreate(
            ['payment_id' => $paymentId],
            [
                'cook_id' => $subscription->cook_id,
                'subscription_plan_id' => $subscription->plan_id,
                'amount' => $payment->transaction_amount,
                'currency' => $payment->currency_id,
                'payment_gateway' => 'mercadopago',
                'status' => 'approved',
                'paid_at' => now(),
            ]
        );

        // 2. Extend subscription period
        $plan = $subscription->plan;
        $subscription->update([
            'status' => 'active',
            'current_period_end' => $plan->billing_period === 'monthly' ? now()->addMonth() : now()->addYear(),
        ]);

        // 3. Reset limits
        $subscription->cook->update([
            'monthly_sales_accumulated' => 0,
            'monthly_orders_accumulated' => 0,
            'is_selling_blocked' => false,
        ]);

        return true;
    }

    /**
     * Handle generic payments (useful for legacy or mixed flows).
     */
    protected function handleGenericPayment(string $paymentId)
    {
        $payment = $this->mpService->getPayment($paymentId);

        if ($payment && $payment->status === 'approved') {
            $hasPreapproval = isset($payment->preapproval_id);
            $hasCookRef = isset($payment->external_reference) && is_numeric($payment->external_reference);

            if ($hasPreapproval || $hasCookRef) {
                return $this->handleAuthorizedPayment($paymentId);
            }
        }

        return false;
    }
}
