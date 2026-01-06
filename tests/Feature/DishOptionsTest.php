<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\DishOption;
use App\Models\DishOptionGroup;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DishOptionsTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $cook;
    protected $dish;
    protected $group;
    protected $option1;
    protected $option2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->cook = Cook::factory()->create();

        // Crear un plato con opciones
        $this->dish = Dish::factory()->create([
            'cook_id' => $this->cook->id,
            'price' => 1000,
            'available_stock' => 10,
            'is_active' => true,
            'available_days' => [1, 2, 3, 4, 5, 6, 7], // ISO Days
        ]);

        $this->group = DishOptionGroup::create([
            'dish_id' => $this->dish->id,
            'name' => 'Extras',
            'min_options' => 0,
            'max_options' => 2,
            'is_required' => false,
        ]);

        $this->option1 = DishOption::create([
            'group_id' => $this->group->id,
            'name' => 'Queso Extra',
            'additional_price' => 200,
        ]);

        $this->option2 = DishOption::create([
            'group_id' => $this->group->id,
            'name' => 'Bacon Extra',
            'additional_price' => 300,
        ]);
    }

    /** @test */
    public function dish_relationships_work_correctly()
    {
        $this->assertCount(1, $this->dish->optionGroups);
        $this->assertCount(2, $this->group->options);
        $this->assertEquals($this->dish->id, $this->group->dish->id);
        $this->assertEquals($this->group->id, $this->option1->group->id);
    }

    /** @test */
    public function can_add_dish_with_options_to_cart()
    {
        $response = $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
            'options' => [
                $this->group->id => [$this->option1->id, $this->option2->id]
            ]
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect();
        $cart = session('cart');

        $this->assertCount(1, $cart);
        $this->assertEquals(1500, $cart[0]['price']); // 1000 + 200 + 300
        $this->assertCount(2, $cart[0]['options']);
        $this->assertEquals('Queso Extra', $cart[0]['options'][0]['name']);
    }

    /** @test */
    public function checkout_saves_selected_options_in_database()
    {
        // 1. Agregar al carrito
        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
            'options' => [
                $this->group->id => [$this->option1->id]
            ]
        ]);

        // 2. Procesar pedido
        $response = $this->actingAs($this->customer)->post(route('orders.process'), [
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'immediate',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect();

        // 3. Verificar base de datos
        $order = Order::latest()->first();
        $this->assertNotNull($order);

        $orderItem = $order->items()->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals(1200, $orderItem->unit_price);

        $this->assertDatabaseHas('order_item_options', [
            'order_item_id' => $orderItem->id,
            'dish_option_id' => $this->option1->id,
            'price' => 200,
        ]);

        // Verificar relación a través de eloquent
        $this->assertCount(1, $orderItem->options);
        $this->assertEquals('Queso Extra', $orderItem->options->first()->dishOption->name);
    }

    /** @test */
    public function unit_price_recalculates_per_item_with_different_options()
    {
        // Agregar mismo plato dos veces con distintas opciones

        // Item 1: Con Opción 1
        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
            'options' => [
                $this->group->id => [$this->option1->id]
            ]
        ]);

        // Item 2: Con Opción 2
        $this->actingAs($this->customer)->post(route('cart.add', $this->dish->id), [
            'quantity' => 1,
            'options' => [
                $this->group->id => [$this->option2->id]
            ]
        ]);

        $cart = session('cart');
        $this->assertCount(2, $cart);
        $this->assertEquals(1200, $cart[0]['price']);
        $this->assertEquals(1300, $cart[1]['price']);
    }
}
