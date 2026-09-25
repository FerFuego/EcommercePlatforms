<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\CookSubscription;
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
            'available_stock' => 10,
            'is_active' => true,
            'is_schedulable' => true,
        ]);

        // Seed plans and create subscription
        $this->artisan('db:seed', ['--class' => 'SubscriptionPlanSeeder']);
        $plan = SubscriptionPlan::where('slug', 'basico-free')->first();
        $sub = CookSubscription::create([
            'cook_id' => $this->cook->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);
        $this->cook->update(['current_subscription_id' => $sub->id]);
        $this->cook->user->refresh();
        $this->cook->refresh();
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

    /** @test */
    public function immediate_order_fails_when_kitchen_is_closed_or_outside_hours()
    {
        // Forzamos horarios donde ahora esté cerrado (ej: abre a las 02:00:00 y cierra a las 03:00:00)
        $this->cook->update([
            'opening_time' => '02:00:00',
            'closing_time' => '03:00:00',
        ]);

        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'immediate',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseEmpty('orders');
    }

    /** @test */
    public function customer_can_place_scheduled_order_even_if_kitchen_is_currently_closed()
    {
        // Cocina cerrada en este momento
        $this->cook->update([
            'opening_time' => '10:00:00',
            'closing_time' => '18:00:00',
        ]);
        // Viajamos en el tiempo a las 23:00 (cerrado)
        Carbon::setTestNow(Carbon::today()->setHour(23)->setMinute(0));

        $scheduledTime = Carbon::tomorrow()->setHour(12)->setMinute(0);

        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
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
            'scheduled_time' => $scheduledTime->format('Y-m-d H:i:s'),
        ]);

        Carbon::setTestNow(); // Reset time
    }

    /** @test */
    public function scheduled_order_fails_for_today_if_kitchen_already_closed_today()
    {
        $this->cook->update([
            'opening_time' => '10:00:00',
            'closing_time' => '18:00:00',
        ]);
        // Viajamos en el tiempo a las 21:00 (ya cerró hoy)
        Carbon::setTestNow(Carbon::today()->setHour(21)->setMinute(0));

        // Intenta pedir para hoy mismo
        $todayScheduledTime = Carbon::today()->setHour(22)->setMinute(0);

        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'scheduled',
            'scheduled_time' => $todayScheduledTime->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseEmpty('orders');

        Carbon::setTestNow(); // Reset time
    }

    /** @test */
    public function cook_profile_and_checkout_render_operating_status_notice()
    {
        $this->cook->update([
            'opening_time' => '11:00:00',
            'closing_time' => '15:00:00',
        ]);
        // Simular que son las 22:00 (cocina cerrada)
        Carbon::setTestNow(Carbon::today()->setHour(22)->setMinute(0));

        // 1. Ver perfil del cocinero
        $profileResponse = $this->get(route('marketplace.cook.profile', $this->cook->id));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Cocina Cerrada');
        $profileResponse->assertSee('Solo Pedidos Programados');

        // 2. Agregar al carrito y ver Checkout
        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
        ]);

        $checkoutResponse = $this->actingAs($this->customer)->get(route('orders.checkout'));
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertSee('Este pedido se procesará como Pedido Programado');
        $checkoutResponse->assertSee('No disponible (cocina fuera de horario)');
        $checkoutResponse->assertSee('Requerido (cocina fuera de turno)');

        Carbon::setTestNow(); // Reset time
    }
}
