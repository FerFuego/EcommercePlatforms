<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customer_can_browse_catalog()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        Dish::factory()->count(3)->create(['cook_id' => $cook->id, 'is_active' => true, 'available_stock' => 10]);

        $response = $this->get(route('marketplace.catalog'));

        $response->assertStatus(200);
        $response->assertSee($cook->user->name);
    }

    /** @test */
    public function customer_can_view_cook_profile()
    {
        $cook = Cook::factory()->create(['is_approved' => true]);
        $dish = Dish::factory()->create(['cook_id' => $cook->id]);

        $response = $this->actingAs($customer = User::factory()->create())->get(route('marketplace.cook.profile', $cook->id));

        $response->assertStatus(200);
        $response->assertSee($cook->user->name);
        $response->assertSee($dish->name);
    }

    /** @test */
    public function customer_can_add_dish_to_cart()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'available_stock' => 10]);

        $response = $this->actingAs($customer)->post(route('cart.add', $dish->id), [
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(2, session('cart')[0]['quantity']);
    }

    /** @test */
    public function customer_can_view_cart()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $dish = Dish::factory()->create();

        // Simular carrito en sesión
        session([
            'cart' => [
                [
                    'dish_id' => $dish->id,
                    'quantity' => 2,
                    'cook_id' => $dish->cook_id,
                    'price' => $dish->price,
                    'name' => $dish->name,
                    'photo_url' => $dish->photo_url,
                ],
            ]
        ]);

        $response = $this->actingAs($customer)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee($dish->name);
    }

    /** @test */
    public function customer_can_proceed_to_checkout()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'price' => 1000]);

        session([
            'cart' => [
                [
                    'dish_id' => $dish->id,
                    'quantity' => 2,
                    'cook_id' => $cook->id,
                    'price' => $dish->price,
                    'name' => $dish->name,
                    'photo_url' => $dish->photo_url,
                ],
            ]
        ]);

        $response = $this->actingAs($customer)->get(route('orders.checkout'));

        $response->assertStatus(200);
        $response->assertSee('Checkout');
    }

    /** @test */
    public function customer_can_complete_order_with_pickup()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'price' => 1500, 'available_stock' => 10]);

        session([
            'cart' => [
                [
                    'dish_id' => $dish->id,
                    'quantity' => 2,
                    'cook_id' => $cook->id,
                    'price' => $dish->price,
                    'name' => $dish->name,
                    'photo_url' => $dish->photo_url,
                ],
            ]
        ]);

        $response = $this->actingAs($customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'immediate',
            'notes' => 'Sin cebolla',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'subtotal' => 3000,
        ]);

        // Cart should be cleared
        $this->assertEmpty(session('cart', []));
    }

    /** @test */
    public function customer_can_complete_order_with_delivery()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'price' => 2000, 'available_stock' => 10]);

        session([
            'cart' => [
                [
                    'dish_id' => $dish->id,
                    'quantity' => 1,
                    'cook_id' => $cook->id,
                    'price' => $dish->price,
                    'name' => $dish->name,
                    'photo_url' => $dish->photo_url,
                ],
            ]
        ]);

        $response = $this->actingAs($customer)->post(route('orders.process'), [
            'delivery_type' => 'delivery',
            'delivery_address' => 'Calle Falsa 123',
            'delivery_lat' => -32.1745,
            'delivery_lng' => -63.2963,
            'payment_method' => 'mercadopago',
            'schedule_type' => 'immediate',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'delivery_type' => 'delivery',
            'delivery_address' => 'Calle Falsa 123',
        ]);
    }

    /** @test */
    public function order_stock_is_decremented_after_purchase()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'available_stock' => 10]);

        session([
            'cart' => [
                [
                    'dish_id' => $dish->id,
                    'quantity' => 3,
                    'cook_id' => $cook->id,
                    'price' => $dish->price,
                    'name' => $dish->name,
                    'photo_url' => $dish->photo_url,
                ],
            ]
        ]);

        $this->actingAs($customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'immediate',
        ]);

        $this->assertEquals(7, $dish->fresh()->available_stock);
    }

    /** @test */
    public function customer_can_view_their_orders()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->get(route('orders.my'));

        $response->assertStatus(200);
        $response->assertSee("#$order->id");
    }

    /** @test */
    public function customer_cannot_checkout_with_empty_cart()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cook = Cook::factory()->create();

        $response = $this->actingAs($customer)->get(route('orders.checkout'));

        $response->assertRedirect(route('marketplace.catalog'));
    }
}
