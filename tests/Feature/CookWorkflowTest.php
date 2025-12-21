<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CookWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cook_can_access_dashboard()
    {
        $cook = Cook::factory()->create(['is_approved' => true]);

        $response = $this->actingAs($cook->user)->get(route('cook.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function cook_can_view_their_dishes()
    {
        $cook = Cook::factory()->create();
        Dish::factory()->count(3)->create(['cook_id' => $cook->id]);

        $response = $this->actingAs($cook->user)->get(route('cook.dishes.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function cook_can_create_new_dish()
    {
        Storage::fake('public');
        $cook = Cook::factory()->create();

        $response = $this->actingAs($cook->user)->post(route('cook.dishes.store'), [
            'name' => 'Milanesa Napolitana',
            'description' => 'Milanesa con jamón, queso y salsa',
            'price' => 1500,
            'available_stock' => 10,
            'available_days' => [1, 2, 3, 4, 5],
            'preparation_time_minutes' => 30,
            'delivery_method' => 'both',
            'diet_tags' => [],
            'is_active' => true,
            'photo' => UploadedFile::fake()->image('dish.jpg'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dishes', [
            'cook_id' => $cook->id,
            'name' => 'Milanesa Napolitana',
            'price' => 1500,
        ]);
    }

    /** @test */
    public function cook_can_update_dish()
    {
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'name' => 'Old Name']);

        $response = $this->actingAs($cook->user)->put(route('cook.dishes.update', $dish), [
            'name' => 'New Name',
            'description' => $dish->description,
            'price' => $dish->price,
            'available_stock' => $dish->available_stock,
            'available_days' => [1, 2, 3],
            'preparation_time_minutes' => 30,
            'delivery_method' => 'both',
            'diet_tags' => [],
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $dish->fresh()->name);
    }

    /** @test */
    public function cook_can_delete_dish()
    {
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id]);

        $response = $this->actingAs($cook->user)->delete(route('cook.dishes.destroy', $dish));

        $response->assertRedirect();
        $this->assertDatabaseMissing('dishes', ['id' => $dish->id]);
    }

    /** @test */
    public function cook_can_toggle_dish_active_status()
    {
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'is_active' => true]);

        $response = $this->actingAs($cook->user)->post(route('cook.dishes.toggle', $dish));

        $response->assertJson(['success' => true]);
        $this->assertFalse($dish->fresh()->is_active);
    }

    /** @test */
    public function cook_can_update_stock()
    {
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'available_stock' => 10]);

        $response = $this->actingAs($cook->user)->post(route('cook.dishes.stock', $dish), [
            'available_stock' => 15,
        ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals(15, $dish->fresh()->available_stock);
    }

    /** @test */
    public function cook_can_view_their_orders()
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);
        Order::factory()->count(3)->create(['cook_id' => $cook->id, 'customer_id' => $customer->id]);

        $response = $this->actingAs($cook->user)->get(route('cook.orders.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function cook_can_accept_order()
    {
        $cook = Cook::factory()->create();
        $order = Order::factory()->create([
            'cook_id' => $cook->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $response = $this->actingAs($cook->user)->post(route('cook.orders.accept', $order));

        $response->assertRedirect();
        $this->assertEquals(Order::STATUS_PREPARING, $order->fresh()->status);
    }

    /** @test */
    public function cook_can_reject_order()
    {
        $cook = Cook::factory()->create();
        $order = Order::factory()->create([
            'cook_id' => $cook->id,
            'status' => Order::STATUS_AWAITING_COOK,
        ]);

        $response = $this->actingAs($cook->user)->post(route('cook.orders.reject', $order), [
            'rejection_reason' => 'No tengo ingredientes',
        ]);

        $response->assertRedirect();
        $this->assertEquals('rejected_by_cook', $order->fresh()->status);
    }

    /** @test */
    public function cook_can_mark_order_as_ready()
    {
        $cook = Cook::factory()->create();
        $order = Order::factory()->create([
            'cook_id' => $cook->id,
            'status' => Order::STATUS_PREPARING,
            'delivery_type' => 'pickup',
        ]);

        $response = $this->actingAs($cook->user)->post(route('cook.orders.update-status', $order), [
            'action' => 'ready',
        ]);

        $response->assertRedirect();
        $this->assertEquals('ready_for_pickup', $order->fresh()->status);
    }

    /** @test */
    public function cook_cannot_access_another_cooks_dishes()
    {
        $cook1 = Cook::factory()->create();
        $cook2 = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook2->id]);

        $response = $this->actingAs($cook1->user)->get(route('cook.dishes.edit', $dish));

        $response->assertStatus(404);
    }
}
