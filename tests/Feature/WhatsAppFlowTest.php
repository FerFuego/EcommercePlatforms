<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\CookSubscription;
use App\Events\OrderStatusUpdated;
use App\Notifications\OrderStatusNotification;
use App\Notifications\NewOrderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WhatsAppFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
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
        $cook->refresh();
        return $sub;
    }

    // ──────────────────────────────────────────────
    // Checkout without payment
    // ──────────────────────────────────────────────

    /** @test */
    public function checkout_does_not_require_payment_method()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'available_stock' => 10]);

        session([
            'cart' => [[
                'dish_id' => $dish->id,
                'quantity' => 1,
                'cook_id' => $cook->id,
                'price' => $dish->price,
                'name' => $dish->name,
                'photo_url' => $dish->photo_url,
                'options' => [],
            ]]
        ]);

        $response = $this->actingAs($customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'schedule_type' => 'immediate',
        ]);

        $response->assertRedirect();
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(Order::STATUS_AWAITING_COOK, $order->status);
        $this->assertNull($order->payment_method);
        $this->assertNull($order->payment_status);
    }

    /** @test */
    public function order_starts_in_awaiting_cook_status()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'available_stock' => 10]);

        session([
            'cart' => [[
                'dish_id' => $dish->id,
                'quantity' => 2,
                'cook_id' => $cook->id,
                'price' => $dish->price,
                'name' => $dish->name,
                'photo_url' => $dish->photo_url,
                'options' => [],
            ]]
        ]);

        $this->actingAs($customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'schedule_type' => 'immediate',
        ]);

        $order = Order::first();
        $this->assertEquals(Order::STATUS_AWAITING_COOK, $order->status);
    }

    // ──────────────────────────────────────────────
    // WhatsApp URL in success page
    // ──────────────────────────────────────────────

    /** @test */
    public function success_page_shows_whatsapp_button_when_cook_has_phone()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => '5491112345678']);
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $response = $this->actingAs($customer)->get(route('orders.success', $order->id));

        $response->assertStatus(200);
        $response->assertSee('wa.me');
        $response->assertSee('Abrir WhatsApp');
    }

    /** @test */
    public function success_page_shows_fallback_when_cook_has_no_phone()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => null]);
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $response = $this->actingAs($customer)->get(route('orders.success', $order->id));

        $response->assertStatus(200);
        $response->assertSee('no disponible');
    }

    // ──────────────────────────────────────────────
    // WhatsApp URL in order detail
    // ──────────────────────────────────────────────

    /** @test */
    public function order_detail_shows_whatsapp_for_customer()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => '5491112345678']);
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
        ]);

        $response = $this->actingAs($customer)->get(route('orders.show', $order->id));

        $response->assertStatus(200);
        $response->assertSee('Abrir WhatsApp');
    }

    /** @test */
    public function order_detail_shows_whatsapp_for_cook()
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create([
            'role' => 'customer',
            'phone' => '5491198765432',
        ]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
        ]);

        $response = $this->actingAs($cook->user)->get(route('orders.show', $order->id));

        $response->assertStatus(200);
        $response->assertSee('Abrir WhatsApp');
    }

    // ──────────────────────────────────────────────
    // Checkout page removed payment section
    // ──────────────────────────────────────────────

    /** @test */
    public function checkout_page_does_not_show_payment_methods()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'price' => 1500, 'available_stock' => 5]);

        session([
            'cart' => [[
                'dish_id' => $dish->id,
                'quantity' => 1,
                'cook_id' => $cook->id,
                'price' => $dish->price,
                'name' => $dish->name,
                'photo_url' => $dish->photo_url,
            ]]
        ]);

        $response = $this->actingAs($customer)->get(route('orders.checkout'));

        $response->assertStatus(200);
        $response->assertDontSee('Método de Pago');
        $response->assertDontSee('MercadoPago');
        $response->assertSee('Contactar por WhatsApp');
    }

    // ──────────────────────────────────────────────
    // Notify new order
    // ──────────────────────────────────────────────

    /** @test */
    public function notify_new_order_dispatches_event_and_notifications()
    {
        Event::fake();
        Notification::fake();

        $cook = Cook::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $order->notifyNewOrder();

        Event::assertDispatched(OrderStatusUpdated::class);
        Notification::assertSentTo($customer, OrderStatusNotification::class);
        Notification::assertSentTo($cook->user, NewOrderNotification::class);
    }

    /** @test */
    public function notify_new_order_creates_log_entry()
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $order->notifyNewOrder();

        $this->assertDatabaseHas('order_logs', [
            'order_id' => $order->id,
            'event' => 'order_placed',
        ]);
    }
}
