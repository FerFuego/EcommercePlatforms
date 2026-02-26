<?php

namespace App\Services;

use App\Models\Cook;
use App\Models\SubscriptionPlan;

interface SubscriptionServiceInterface
{
    public function createSubscription(Cook $cook, SubscriptionPlan $plan, array $paymentMethodData);
    public function cancelSubscription(Cook $cook);
    public function handleWebhook(array $payload);
}
