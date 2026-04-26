<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Cook;
use App\Models\SubscriptionPlan;
use App\Models\CookSubscription;
use App\Models\Dish;

class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default plans
        $this->artisan('db:seed', ['--class' => 'SubscriptionPlanSeeder']);
    }

    public function test_cook_metrics_increment_and_block_when_limit_exceeded()
    {
        // 1. Setup Cook with FREE plan
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'active' => true,
            'is_selling_blocked' => false,
            'monthly_orders_accumulated' => 0,
            'monthly_sales_accumulated' => 0,
        ]);

        $freePlan = SubscriptionPlan::where('slug', 'basico-free')->first();

        $subscription = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $cook->update(['current_subscription_id' => $subscription->id]);

        // 2. Validate initial state
        $this->assertEquals(0, $cook->monthly_orders_accumulated);
        $this->assertFalse($cook->is_selling_blocked);

        // 3. Increment metrics to the limit (100)
        // Let's manually increment 100 times (reaching the limit, but not exceeding it)
        for ($i = 0; $i < 100; $i++) {
            $cook->incrementMetricsAndCheckLimits(100);
        }

        $cook->refresh();
        $this->assertEquals(100, $cook->monthly_orders_accumulated);
        $this->assertEquals(10000, $cook->monthly_sales_accumulated);
        $this->assertFalse($cook->is_selling_blocked);

        // 4. Hit the limit (101st order should block)
        $cook->incrementMetricsAndCheckLimits(100);

        $cook->refresh();
        $cook->load('currentSubscription.plan');

        $this->assertEquals(101, $cook->monthly_orders_accumulated);
        $this->assertEquals(10100, $cook->monthly_sales_accumulated);
        $this->assertTrue($cook->is_selling_blocked); // Should be blocked now
    }

    public function test_monthly_metrics_reset_correctly()
    {
        // 1. Setup Cook with blocked status and accumulated metrics
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'active' => true,
            'is_selling_blocked' => true,
            'monthly_orders_accumulated' => 25,
            'monthly_sales_accumulated' => 25000,
        ]);

        $freePlan = SubscriptionPlan::where('slug', 'basico-free')->first();

        $subscription = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
            'current_period_start' => now()->subMonth(),
            'current_period_end' => now(),
        ]);

        $cook->update(['current_subscription_id' => $subscription->id]);

        // 2. Run the artisan command
        $this->artisan('cooks:reset-monthly-metrics')->assertExitCode(0);

        // 3. Verify metrics are reset
        $cook->refresh();
        $this->assertEquals(0, $cook->monthly_orders_accumulated);
        $this->assertEquals(0, $cook->monthly_sales_accumulated);
        $this->assertFalse($cook->is_selling_blocked);
    }

    public function test_admin_can_create_plan()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.subscription-plans.store'), [
            'name' => 'Test Plan',
            'price' => 1500,
            'currency' => 'ARS',
            'billing_period' => 'monthly',
            'monthly_sales_limit' => 100000,
            'monthly_orders_limit' => 50,
            'commission_percentage' => 10,
            'features' => [
                'premium_badge' => true,
            ],
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.subscription-plans.index'));
        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 1500,
        ]);
    }
}
