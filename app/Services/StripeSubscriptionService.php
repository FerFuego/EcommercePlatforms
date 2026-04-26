<?php

namespace App\Services;

use App\Models\Cook;
use App\Models\SubscriptionPlan;

class StripeSubscriptionService implements SubscriptionServiceInterface
{
    public function createSubscription(Cook $cook, SubscriptionPlan $plan, array $paymentMethodData)
    {
        // $paymentMethodData can be the Stripe payment method ID
        return $cook->newSubscription('default', $plan->provider_subscription_id)
            ->create($paymentMethodData['payment_method_id'] ?? null);
    }

    public function cancelSubscription(Cook $cook)
    {
        return $cook->subscription('default')->cancel();
    }

    public function handleWebhook(array $payload)
    {
        // Custom Stripe webhook handling if needed,
        // although Cashier handles most out-of-the-box.
    }
}
