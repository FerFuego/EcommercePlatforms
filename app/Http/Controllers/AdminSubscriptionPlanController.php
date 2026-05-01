<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\MercadoPagoService;

class AdminSubscriptionPlanController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }
    public function index()
    {
        $plans = SubscriptionPlan::latest()->get();
        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly',
            'monthly_sales_limit' => 'nullable|numeric|min:0',
            'monthly_orders_limit' => 'nullable|integer|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'stripe_price_id' => 'nullable|string|max:255',
            'mp_plan_id' => 'nullable|string|max:255',
            'features' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Convert features array to booleans if checkboxes
        $features = [
            'premium_badge' => isset($request->features['premium_badge']),
            'can_create_offers' => isset($request->features['can_create_offers']),
            'advanced_stats' => isset($request->features['advanced_stats']),
            'priority_listing' => isset($request->features['priority_listing']),
        ];

        $validated['features'] = $features;
        $validated['is_active'] = $request->has('is_active');

        $plan = SubscriptionPlan::create($validated);

        // Sync with Mercado Pago
        $mpPlanId = $this->mercadoPagoService->syncPlan($plan);
        if ($mpPlanId) {
            $plan->update(['mp_plan_id' => $mpPlanId]);
        } else {
            session()->flash('warning', 'El plan se creó localmente pero no se pudo sincronizar con Mercado Pago. Verifica el Access Token.');
        }

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan de suscripción creado.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly',
            'monthly_sales_limit' => 'nullable|numeric|min:0',
            'monthly_orders_limit' => 'nullable|integer|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'stripe_price_id' => 'nullable|string|max:255',
            'mp_plan_id' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $features = [
            'premium_badge' => isset($request->features['premium_badge']),
            'can_create_offers' => isset($request->features['can_create_offers']),
            'advanced_stats' => isset($request->features['advanced_stats']),
            'priority_listing' => isset($request->features['priority_listing']),
        ];

        $validated['features'] = $features;
        $validated['is_active'] = $request->has('is_active');

        $subscriptionPlan->update($validated);

        // Sync with Mercado Pago
        $mpPlanId = $this->mercadoPagoService->syncPlan($subscriptionPlan);
        if ($mpPlanId && $mpPlanId !== $subscriptionPlan->mp_plan_id) {
            $subscriptionPlan->update(['mp_plan_id' => $mpPlanId]);
        } elseif (!$mpPlanId) {
            session()->flash('warning', 'No se pudo sincronizar los cambios con Mercado Pago. Verifica la conexión.');
        }

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan actualizado.');
    }

    public function toggleStatus(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->is_active = !$subscriptionPlan->is_active;
        $subscriptionPlan->save();

        return back()->with('success', 'Estado del plan actualizado.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        // Check for active subscriptions
        $activeSubscriptionsCount = $subscriptionPlan->subscriptions()->where('status', 'active')->count();

        if ($activeSubscriptionsCount > 0) {
            return back()->with('error', 'No se puede eliminar un plan que tiene suscripciones activas. Te recomendamos desactivarlo para evitar errores en las cuentas de los cocineros.');
        }

        // Coordination with Mercado Pago
        if ($subscriptionPlan->mp_plan_id) {
            $this->mercadoPagoService->deactivatePlan($subscriptionPlan->mp_plan_id);
        }

        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan de suscripción eliminado correctamente.');
    }

    public function syncFromMercadoPago()
    {
        try {
            $token = \App\Models\Setting::get('mp_access_token');
            if (!$token) {
                return back()->with('error', 'No hay un Access Token configurado.');
            }

            $result = $this->mercadoPagoService->testToken($token);

            if ($result['status'] !== 'success') {
                return back()->with('error', 'Error de conexión con Mercado Pago: ' . ($result['message'] ?? 'Desconocido'));
            }

            // Usamos el cliente de búsqueda para obtener los planes activos
            $client = new \MercadoPago\Client\PreApprovalPlan\PreApprovalPlanClient();
            \MercadoPago\MercadoPagoConfig::setAccessToken($token);
            
            $search = new \MercadoPago\Net\MPSearchRequest(50, 0, ['status' => 'active']);
            $mpResults = $client->search($search);

            $syncedCount = 0;
            foreach ($mpResults->results as $mpPlan) {
                // Buscamos coincidencia local por nombre o slug
                $localPlan = SubscriptionPlan::where('name', 'LIKE', '%' . $mpPlan->reason . '%')
                    ->orWhere('slug', \Illuminate\Support\Str::slug($mpPlan->reason))
                    ->first();

                if ($localPlan) {
                    $localPlan->update([
                        'mp_plan_id' => $mpPlan->id,
                        'price' => $mpPlan->auto_recurring->transaction_amount
                    ]);
                    $syncedCount++;
                } else {
                    // Si no existe, lo creamos
                    SubscriptionPlan::create([
                        'name' => $mpPlan->reason,
                        'slug' => \Illuminate\Support\Str::slug($mpPlan->reason),
                        'price' => $mpPlan->auto_recurring->transaction_amount,
                        'mp_plan_id' => $mpPlan->id,
                        'currency' => $mpPlan->auto_recurring->currency_id ?? 'ARS',
                        'billing_period' => 'monthly',
                        'commission_percentage' => 0, // Default
                        'is_active' => true,
                        'features' => [
                            'premium_badge' => false,
                            'can_create_offers' => false,
                            'advanced_stats' => false,
                            'priority_listing' => false,
                        ]
                    ]);
                    $syncedCount++;
                }
            }

            return back()->with('success', "Sincronización completada. Se actualizaron/crearon {$syncedCount} planes desde Mercado Pago.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }
}
