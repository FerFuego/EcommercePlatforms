<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the FREE plan ID, or create it if it doesn't exist
        $freePlan = DB::table('subscription_plans')->where('name', 'Básico (FREE)')->first();

        if (!$freePlan) {
            $freePlanId = DB::table('subscription_plans')->insertGetId([
                'name' => 'Básico (FREE)',
                'slug' => 'basico-free',
                'price' => 0.00,
                'currency' => 'ARS',
                'billing_period' => 'monthly',
                'monthly_sales_limit' => 50000.00,
                'monthly_orders_limit' => 20,
                'commission_percentage' => 15.00,
                'features' => json_encode(['premium_badge' => false, 'priority_listing' => false]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $freePlanId = $freePlan->id;
        }

        // Get all cooks without a subscription
        $cooks = DB::table('cooks')->whereNull('current_subscription_id')->get();

        foreach ($cooks as $cook) {
            // Create subscription
            $subscriptionId = DB::table('cook_subscriptions')->insertGetId([
                'cook_id' => $cook->id,
                'plan_id' => $freePlanId,
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update cook
            DB::table('cooks')
                ->where('id', $cook->id)
                ->update([
                    'current_subscription_id' => $subscriptionId,
                    'monthly_sales_accumulated' => 0,
                    'monthly_orders_accumulated' => 0,
                    'is_selling_blocked' => false,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data migration, we don't necessarily want to drop the subscriptions 
        // on rollback unless we are removing the feature entirely.
        // We'll leave it empty to prevent data loss on accidental rollbacks.
    }
};
