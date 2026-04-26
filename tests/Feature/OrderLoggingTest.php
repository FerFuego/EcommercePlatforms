<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\CookSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed default plans
        $this->artisan('db:seed', ['--class' => 'SubscriptionPlanSeeder']);
    }

    protected function createActiveSubscription(Cook $cook)
    {
        $plan = SubscriptionPlan::where('slug', 'basico-free')->first();
        $sub = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);
        $cook->update(['current_subscription_id' => $sub->id]);
        $cook->user->refresh();
        $cook->refresh();
        return $sub;
    }

    public function test_order_creation_logs_event()
    {
        $user = User::factory()->create();
        $cookUser = User::factory()->create();
        $cook = Cook::factory()->create(['user_id' => $cookUser->id]);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'available_stock' => 10]);

        $this->actingAs($user);

        $response = $this->withSession([
            'cart' => [
                [
                    'dish_id' => $dish->id,
                    'cook_id' => $cook->id,
                    'name' => $dish->name,
                    'price' => $dish->price,
                    'quantity' => 1,
                    'options' => []
                ]
            ]
        ])->post(route('orders.process'), [
                    'delivery_type' => 'pickup',
                    'schedule_type' => 'immediate',
                ]);

        $order = Order::first();
        $this->assertNotNull($order);

        // Should have 1 log: order_placed (via notifyNewOrder)
        $this->assertEquals(1, $order->logs()->count());
        $this->assertEquals('order_placed', $order->logs()->first()->event);
    }

    public function test_order_status_transitions_log_events()
    {
        $user = User::factory()->create();
        $cookUser = User::factory()->create();
        $cook = Cook::factory()->create(['user_id' => $cookUser->id]);
        $this->createActiveSubscription($cook);
        $order = Order::factory()->create([
            'customer_id' => $user->id,
            'cook_id' => $cook->id,
            'status' => Order::STATUS_AWAITING_COOK
        ]);

        $this->actingAs($cookUser);

        // Accept by cook
        $order->acceptByCook();
        $this->assertTrue($order->logs()->where('event', 'cook_accepted')->exists());

        // Mark as ready
        $order->markAsReady();
        $this->assertTrue($order->logs()->where('event', 'order_ready')->exists());

        // Mark as delivered
        $order->markAsDelivered();
        $this->assertTrue($order->logs()->where('event', 'order_delivered')->exists());

        // Check logs count
        $this->assertEquals(3, $order->logs()->count());
    }
}
