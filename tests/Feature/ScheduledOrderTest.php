<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $cook;
    protected $dish;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->cook = Cook::factory()->create([
            'opening_time' => '10:00:00',
            'closing_time' => '22:00:00',
            'max_scheduled_portions_per_day' => 10,
            'is_approved' => true,
            'active' => true,
        ]);

        $this->dish = Dish::factory()->create([
            'cook_id' => $this->cook->id,
            'price' => 1000,
            'available_stock' => 100,
            'is_active' => true,
            'is_schedulable' => true,
        ]);
    }

    /** @test */
    public function customer_can_create_scheduled_order()
    {
        $scheduledTime = Carbon::tomorrow()->setHour(12)->setMinute(0);

        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'scheduled',
            'scheduled_time' => $scheduledTime->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->customer->id,
            'cook_id' => $this->cook->id,
            'status' => Order::STATUS_AWAITING_COOK,
            'scheduled_time' => $scheduledTime->format('Y-m-d H:i:s'),
        ]);
    }

    /** @test */
    public function scheduled_order_fails_outside_cook_hours()
    {
        $scheduledTime = Carbon::tomorrow()->setHour(8)->setMinute(0); // Before opening (10:00)

        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'scheduled',
            'scheduled_time' => $scheduledTime->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseEmpty('orders');
    }

    /** @test */
    public function scheduled_order_fails_when_dish_is_not_schedulable()
    {
        $nonSchedulableDish = Dish::factory()->create([
            'cook_id' => $this->cook->id,
            'is_schedulable' => false,
        ]);

        $scheduledTime = Carbon::tomorrow()->setHour(12)->setMinute(0);

        $this->actingAs($this->customer)->post(route('cart.add', $nonSchedulableDish->id), [
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'scheduled',
            'scheduled_time' => $scheduledTime->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseEmpty('orders');
    }

    /** @test */
    public function scheduled_order_fails_when_cook_capacity_is_exceeded()
    {
        // Filling capacity (cook allows 10 portions per day)
        $scheduledDate = Carbon::tomorrow()->toDateString();
        $scheduledTime = Carbon::tomorrow()->setHour(12)->setMinute(0);

        // Pre-create an order using 8 portions
        $existingOrder = Order::factory()->create([
            'cook_id' => $this->cook->id,
            'scheduled_time' => $scheduledTime,
            'status' => Order::STATUS_PAID,
        ]);
        OrderItem::create([
            'order_id' => $existingOrder->id,
            'dish_id' => $this->dish->id,
            'quantity' => 8,
            'unit_price' => $this->dish->price,
            'total_price' => $this->dish->price * 8,
        ]);

        // Try to order 3 more (Total 11 > 10)
        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'scheduled',
            'scheduled_time' => $scheduledTime->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 1); // Only the pre-existing one
    }

    /** @test */
    public function cook_can_accept_scheduled_order()
    {
        $order = Order::factory()->create([
            'cook_id' => $this->cook->id,
            'customer_id' => $this->customer->id,
            'status' => Order::STATUS_AWAITING_COOK,
            'scheduled_time' => Carbon::tomorrow()->setHour(12),
        ]);

        $this->actingAs($this->cook->user);

        $order->acceptByCook();

        $this->assertEquals(Order::STATUS_SCHEDULED, $order->fresh()->status);
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $order->id,
            'event' => 'cook_accepted_scheduled',
        ]);
    }
}
