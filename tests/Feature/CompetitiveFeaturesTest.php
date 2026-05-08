<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\CookSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitiveFeaturesTest extends TestCase
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

    /** @test */
    public function dish_is_low_stock_method_returns_correct_boolean()
    {
        $dish = new Dish(['available_stock' => 3]);
        $this->assertTrue($dish->isLowStock());

        $dish->available_stock = 5;
        $this->assertTrue($dish->isLowStock());

        $dish->available_stock = 6;
        $this->assertFalse($dish->isLowStock());

        $dish->available_stock = 0;
        $this->assertFalse($dish->isLowStock());
    }

    /** @test */
    public function scarcity_badge_is_visible_in_catalog_when_stock_is_low()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'available_stock' => 3,
            'is_active' => true,
        ]);

        $response = $this->get(route('marketplace.catalog'));
        
        $response->assertStatus(200);
        $response->assertSee('¡Solo quedan 3!');
    }

    /** @test */
    public function scarcity_badge_is_visible_in_cook_profile_when_stock_is_low()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'available_stock' => 2,
            'is_active' => true,
        ]);

        $response = $this->get(route('marketplace.cook.profile', $cook->id));
        
        $response->assertStatus(200);
        $response->assertSee('¡Solo quedan 2!');
    }

    /** @test */
    public function cook_can_view_prep_list()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id, 'is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);

        $this->actingAs($user);

        $response = $this->get(route('cook.prep.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Hoja de Producción');
    }

    /** @test */
    public function prep_list_aggregates_quantities_correctly()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id, 'is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);

        $dish = Dish::factory()->create(['cook_id' => $cook->id, 'name' => 'Pasta Especial']);
        
        // Create orders with items for this dish
        $order1 = Order::factory()->create(['cook_id' => $cook->id, 'status' => 'paid']);
        OrderItem::create([
            'order_id' => $order1->id,
            'dish_id' => $dish->id,
            'quantity' => 3,
            'unit_price' => $dish->price,
            'total_price' => $dish->price * 3,
        ]);

        $order2 = Order::factory()->create(['cook_id' => $cook->id, 'status' => 'preparing']);
        OrderItem::create([
            'order_id' => $order2->id,
            'dish_id' => $dish->id,
            'quantity' => 5,
            'unit_price' => $dish->price,
            'total_price' => $dish->price * 5,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('cook.prep.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Pasta Especial');
        $response->assertSee('x8'); // 3 + 5 = 8
    }
}
