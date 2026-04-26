<?php

namespace App\Services;

use App\Models\Cook;
use App\Models\SubscriptionPlan;

class MercadoPagoSubscriptionService implements SubscriptionServiceInterface
{
    public function createSubscription(Cook $cook, SubscriptionPlan $plan, array $paymentMethodData)
    {
        // Use MP Subscriptions / Preapproval API to create a subscription
        // implementation goes here
    }

    public function cancelSubscription(Cook $cook)
    {
        // implementation goes here
    }

    public function handleWebhook(array $payload)
    {
        // specific MP webhook handling
    }
}
