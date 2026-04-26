<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class CookSubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }
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

        try {
            $result = $this->subscriptionService->initiateSubscription($cook, $plan);

            if ($result['status'] === 'already_active') {
                return redirect()->route('cook.subscription.index')->with('info', $result['message']);
            }

            if ($result['status'] === 'success') {
                return redirect($result['init_point']);
            }

            return back()->with('error', 'No se pudo iniciar el proceso de suscripción.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Subscription Process Error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Callback for successful subscription (Mercado Pago redirect)
     */
    public function success(Request $request)
    {
        $preapprovalId = $request->input('preapproval_id');

        if (!$preapprovalId) {
            return redirect()->route('cook.subscription.index')->with('error', 'No se recibió el ID de la suscripción.');
        }

        $success = $this->subscriptionService->activateSubscription($preapprovalId);

        if ($success) {
            return redirect()->route('cook.subscription.index')->with('success', '¡Suscripción autorizada! Se activará en breve.');
        }

        return redirect()->route('cook.subscription.index')->with('warning', 'Estamos procesando tu suscripción. Podría tardar unos minutos en aparecer activa.');
    }

    /**
     * Cancel the current subscription
     */
    public function cancel()
    {
        $cook = auth()->user()->cook;
        $success = $this->subscriptionService->cancelSubscription($cook);

        if ($success) {
            return back()->with('success', 'Tu suscripción ha sido cancelada correctamente.');
        }

        return back()->with('error', 'No se pudo cancelar la suscripción o no tienes una suscripción activa.');
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
}
