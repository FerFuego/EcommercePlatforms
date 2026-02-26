<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSubscriptionPlanController extends Controller
{
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

        SubscriptionPlan::create($validated);

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

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan actualizado.');
    }

    public function toggleStatus(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->is_active = !$subscriptionPlan->is_active;
        $subscriptionPlan->save();

        return back()->with('success', 'Estado del plan actualizado.');
    }
}
