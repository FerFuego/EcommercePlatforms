<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class CookSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $cook = auth()->user()->cook;

        if ($request->has('success')) {
            // In a real app, we would verify the payment here or wait for webhooks
            // For this phase, we'll show a message
            session()->flash('success', '¡Pago procesado correctamente! Tu suscripción se activará en breve.');
        }

        $activePlan = $cook->plan();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('cook.subscription.index', compact('cook', 'activePlan', 'plans'));
    }

    public function checkout(SubscriptionPlan $plan)
    {
        $cook = auth()->user()->cook;
        // Si el usuario ya tiene este plan, lo redirigimos
        if ($cook->plan() && $cook->plan()->id === $plan->id) {
            return redirect()->route('cook.subscription.index')->with('info', 'Ya estás suscrito a este plan.');
        }

        $stripeConfigured = !empty(\App\Models\Setting::get('stripe_secret_key'));
        $mpConfigured = !empty(\App\Models\Setting::get('mp_access_token'));

        return view('cook.subscription.checkout', compact('plan', 'cook', 'stripeConfigured', 'mpConfigured'));
    }

    public function process(Request $request, SubscriptionPlan $plan)
    {
        $cook = auth()->user()->cook;

        // If the plan is free, process it immediately
        if ($plan->price <= 0) {
            return $this->processSubscriptionSuccess($cook, $plan);
        }

        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === 'stripe') {
            return $this->processStripe($cook, $plan);
        } elseif ($paymentMethod === 'mercadopago') {
            return $this->processMercadoPago($cook, $plan);
        }

        return back()->with('error', 'Por favor selecciona un método de pago válido.');
    }

    protected function processStripe($cook, SubscriptionPlan $plan)
    {
        if (empty($plan->stripe_price_id)) {
            return back()->with('error', 'Este plan no tiene un ID de precio de Stripe configurado.');
        }

        // Set Stripe Key from settings
        config(['cashier.key' => \App\Models\Setting::get('stripe_public_key')]);
        config(['cashier.secret' => \App\Models\Setting::get('stripe_secret_key')]);

        $user = auth()->user();

        // Checkout session for subscription
        return $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('cook.subscription.index', ['success' => 1]),
                'cancel_url' => route('cook.subscription.checkout', $plan),
            ]);
    }

    protected function processMercadoPago($cook, SubscriptionPlan $plan)
    {
        $accessToken = \App\Models\Setting::get('mp_access_token');
        if (empty($accessToken)) {
            return back()->with('error', 'MercadoPago no está configurado.');
        }

        // Using MP SDK v3
        \MercadoPago\MercadoPagoConfig::setAccessToken($accessToken);

        try {
            // Case A: Recurring Subscription (Pre-approval)
            if ($plan->mp_plan_id) {
                $client = new \MercadoPago\Client\PreApproval\PreApprovalClient();

                $subscription = $client->create([
                    "preapproval_plan_id" => $plan->mp_plan_id,
                    "reason" => "Suscripción Cocinarte: " . $plan->name,
                    "external_reference" => "cook_sub_" . $cook->id . "_" . $plan->id,
                    "payer_email" => $cook->user->email,
                    "back_url" => route('cook.subscription.index', ['success' => 1]),
                    "auto_return" => "approved",
                ]);

                return redirect($subscription->init_point);
            }

            // Case B: One-time Payment (Preference) - Legacy/Fallback
            $client = new \MercadoPago\Client\Preference\PreferenceClient();
            $preference = $client->create([
                "items" => [
                    [
                        "title" => "Suscripción Cocinarte: " . $plan->name,
                        "quantity" => 1,
                        "unit_price" => (float) $plan->price,
                        "currency_id" => "ARS"
                    ]
                ],
                "external_reference" => "cook_sub_" . $cook->id . "_" . $plan->id,
                "back_urls" => [
                    "success" => route('cook.subscription.index', ['success' => 1]),
                    "failure" => route('cook.subscription.index', ['error' => 1]),
                    "pending" => route('cook.subscription.index')
                ],
                "auto_return" => "approved",
            ]);

            return redirect($preference->init_point);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('MP Subscription Error: ' . $e->getMessage());
            return back()->with('error', 'Error al comunicar con MercadoPago: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $cook = auth()->user()->cook;
        $payments = SubscriptionPayment::where('cook_id', $cook->id)
            ->with('plan')
            ->latest()
            ->paginate(10);

        $totalInvested = SubscriptionPayment::where('cook_id', $cook->id)
            ->where('status', 'approved')
            ->sum('amount');

        return view('cook.subscription.history', compact('cook', 'payments', 'totalInvested'));
    }

    protected function processSubscriptionSuccess($cook, SubscriptionPlan $plan)
    {
        // Internal method for FREE plans or successful webhooks
        $subscription = \App\Models\CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => $plan->billing_period === 'monthly' ? now()->addMonth() : now()->addYear(),
        ]);

        $cook->update([
            'current_subscription_id' => $subscription->id,
            'monthly_sales_accumulated' => 0,
            'monthly_orders_accumulated' => 0,
            'is_selling_blocked' => false,
        ]);

        return redirect()->route('cook.subscription.index')->with('success', '¡Suscripción actualizada exitosamente!');
    }
}
