<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cook;
use App\Models\User;
use App\Models\Dish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_calculate_commission()
    {
        $order = Order::factory()->create([
            'subtotal' => 1000,
            'commission_amount' => 0,
        ]);

        $order->calculateCommission(0.12);

        $this->assertEquals(120, $order->fresh()->commission_amount);
    }

    /** @test */
    public function it_can_mark_as_paid()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING_PAYMENT,
        ]);

        $order->markAsPaid('PAY_123456');

        $freshOrder = $order->fresh();
        $this->assertEquals(Order::STATUS_AWAITING_COOK, $freshOrder->status);
        $this->assertEquals('PAY_123456', $freshOrder->payment_id);
    }

    /** @test */
    public function it_can_be_accepted_by_cook()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $order->acceptByCook();

        $this->assertEquals(Order::STATUS_PREPARING, $order->fresh()->status);
    }

    /** @test */
    public function it_cannot_be_accepted_if_not_awaiting()
    {
        $this->expectException(\Exception::class);

        $order = Order::factory()->create([
            'status' => Order::STATUS_DELIVERED,
        ]);

        $order->acceptByCook();
    }

    /** @test */
    public function it_can_be_rejected_by_cook()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $order->rejectByCook('No tengo ingredientes');

        $freshOrder = $order->fresh();
        $this->assertEquals(Order::STATUS_REJECTED_BY_COOK, $freshOrder->status);
        $this->assertEquals('No tengo ingredientes', $freshOrder->rejection_reason);
    }

    /** @test */
    public function it_can_be_marked_as_preparing()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $order->markAsPreparing();

        $this->assertEquals(Order::STATUS_PREPARING, $order->fresh()->status);
    }

    /** @test */
    public function it_can_be_marked_as_ready()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PREPARING,
        ]);

        $order->delivery_type = 'pickup';
        $order->markAsReady();

        $this->assertEquals(Order::STATUS_READY_FOR_PICKUP, $order->fresh()->status);
    }

    /** @test */
    public function it_can_be_marked_as_delivered()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_READY_FOR_PICKUP,
        ]);

        $order->markAsDelivered();

        $freshOrder = $order->fresh();
        $this->assertEquals(Order::STATUS_DELIVERED, $freshOrder->status);
        $this->assertNotNull($freshOrder->completed_at);
    }

    /** @test */
    public function it_can_be_cancelled()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PAID,
        ]);

        $order->cancel();

        $this->assertEquals(Order::STATUS_CANCELLED, $order->fresh()->status);
    }

    /** @test */
    public function it_knows_if_can_be_reviewed()
    {
        $deliveredOrder = Order::factory()->create([
            'status' => Order::STATUS_DELIVERED,
        ]);

        $preparingOrder = Order::factory()->create([
            'status' => Order::STATUS_PREPARING,
        ]);

        $this->assertTrue($deliveredOrder->canBeReviewed());
        $this->assertFalse($preparingOrder->canBeReviewed());
    }

    /** @test */
    public function it_belongs_to_customer()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(User::class, $order->customer);
        $this->assertEquals($customer->id, $order->customer->id);
    }

    /** @test */
    public function it_belongs_to_cook()
    {
        $cook = Cook::factory()->create();
        $order = Order::factory()->create(['cook_id' => $cook->id]);

        $this->assertInstanceOf(Cook::class, $order->cook);
        $this->assertEquals($cook->id, $order->cook->id);
    }

    /** @test */
    public function it_has_many_items()
    {
        $order = Order::factory()->create();
        $dish = Dish::factory()->create();

        OrderItem::factory()->count(3)->create([
            'order_id' => $order->id,
            'dish_id' => $dish->id,
        ]);

        $this->assertCount(3, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items->first());
    }

    /** @test */
    public function it_scopes_pending_orders()
    {
        Order::factory()->create(['status' => Order::STATUS_AWAITING_COOK]);
        Order::factory()->create(['status' => Order::STATUS_PREPARING]);
        Order::factory()->create(['status' => Order::STATUS_DELIVERED]);

        $pending = Order::pending()->get();

        $this->assertCount(2, $pending);
    }

    /** @test */
    public function it_scopes_completed_orders()
    {
        Order::factory()->create(['status' => Order::STATUS_DELIVERED]);
        Order::factory()->create(['status' => Order::STATUS_DELIVERED]);
        Order::factory()->create(['status' => Order::STATUS_PREPARING]);

        $completed = Order::completed()->get();

        $this->assertCount(2, $completed);
    }
}
