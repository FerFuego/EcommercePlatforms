<?php

namespace Tests\Feature;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\User;
use App\Models\Cook;
use App\Notifications\OrderStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        Notification::fake();
    }

    /** @test */
    public function event_and_notifications_are_dispatched_when_order_is_marked_as_paid()
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'status' => Order::STATUS_PENDING_PAYMENT,
        ]);

        $order->markAsPaid('TEST_PAYMENT_ID');

        // Check Event
        Event::assertDispatched(OrderStatusUpdated::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });

        // Check Notifications
        Notification::assertSentTo($customer, OrderStatusNotification::class);
        Notification::assertSentTo($cook->user, OrderStatusNotification::class);
    }

    /** @test */
    public function event_and_notification_are_dispatched_when_cook_accepts_order()
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $order->acceptByCook();

        Event::assertDispatched(OrderStatusUpdated::class);
        Notification::assertSentTo($customer, OrderStatusNotification::class);
    }

    /** @test */
    public function event_and_notification_are_dispatched_when_cook_rejects_order()
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $order->rejectByCook('No ingredients');

        Event::assertDispatched(OrderStatusUpdated::class);
        Notification::assertSentTo($customer, OrderStatusNotification::class);
    }

    /** @test */
    public function event_and_notification_are_dispatched_when_order_is_ready()
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => Order::STATUS_PREPARING,
        ]);

        $order->markAsReady();

        Event::assertDispatched(OrderStatusUpdated::class);
        Notification::assertSentTo($customer, OrderStatusNotification::class);
    }

    /** @test */
    public function event_and_notification_are_dispatched_when_order_is_delivered()
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => Order::STATUS_ON_THE_WAY,
        ]);

        $order->markAsDelivered();

        Event::assertDispatched(OrderStatusUpdated::class);
        Notification::assertSentTo($customer, OrderStatusNotification::class);
    }

    /** @test */
    public function notification_has_required_channels()
    {
        $order = Order::factory()->create();
        $customer = User::factory()->create();
        $notification = new OrderStatusNotification($order);

        $channels = $notification->via($customer);

        $this->assertContains('mail', $channels);
        $this->assertContains(\App\Channels\WhatsAppChannel::class, $channels);
        $this->assertContains(\App\Channels\WebPushChannel::class, $channels);
    }

    /** @test */
    public function notification_has_whatsapp_and_push_data()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PREPARING]);
        $customer = User::factory()->create(['name' => 'John Doe']);
        $notification = new OrderStatusNotification($order);

        $whatsappMessage = $notification->toWhatsApp($customer);
        $pushData = $notification->toWebPush($customer);

        $this->assertStringContainsString('Pedido #' . $order->id, $whatsappMessage);
        $this->assertStringContainsString('En Preparación', $whatsappMessage);

        $this->assertEquals('Actualización de Pedido #' . $order->id, $pushData['title']);
        $this->assertStringContainsString('En Preparación', $pushData['body']);
    }
}
