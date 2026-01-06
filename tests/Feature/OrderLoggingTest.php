<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_logs_event()
    {
        $user = User::factory()->create();
        $cookUser = User::factory()->create();
        $cook = Cook::factory()->create(['user_id' => $cookUser->id]);
        $dish = Dish::factory()->create(['cook_id' => $cook->id]);

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
                    'payment_method' => 'cash',
                    'schedule_type' => 'immediate',
                ]);

        $order = Order::first();
        $this->assertNotNull($order);

        // Should have 2 logs: order_placed and awaiting_cook
        $this->assertEquals(2, $order->logs()->count());
        $this->assertEquals('order_placed', $order->logs()->where('event', 'order_placed')->first()->event);
        $this->assertEquals('awaiting_cook', $order->logs()->where('event', 'awaiting_cook')->first()->event);
    }

    public function test_order_status_transitions_log_events()
    {
        $user = User::factory()->create();
        $cookUser = User::factory()->create();
        $cook = Cook::factory()->create(['user_id' => $cookUser->id]);
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
