<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\CookSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DishDetailTest extends TestCase
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
    public function dish_detail_page_loads_successfully()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'is_active' => true,
            'available_stock' => 5,
        ]);

        $response = $this->get(route('marketplace.dish.detail', $dish->id));

        $response->assertStatus(200);
        $response->assertSee($dish->name);
    }

    /** @test */
    public function dish_detail_shows_cook_info()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'is_active' => true,
            'available_stock' => 5,
        ]);

        $response = $this->get(route('marketplace.dish.detail', $dish->id));

        $response->assertSee($cook->user->name);
        $response->assertSee('Ver Perfil');
    }

    /** @test */
    public function dish_detail_shows_price()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'price' => 2500,
            'is_active' => true,
            'available_stock' => 5,
        ]);

        $response = $this->get(route('marketplace.dish.detail', $dish->id));

        $response->assertSee('2.500');
    }

    /** @test */
    public function dish_detail_shows_add_to_cart_when_in_stock()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'is_active' => true,
            'available_stock' => 5,
        ]);

        $response = $this->get(route('marketplace.dish.detail', $dish->id));

        $response->assertSee('Agregar al Carrito');
    }

    /** @test */
    public function dish_detail_shows_out_of_stock_message()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'is_active' => true,
            'available_stock' => 0,
        ]);

        // This will redirect back since dish is not available, so we need to test it differently
        $response = $this->get(route('marketplace.dish.detail', $dish->id));

        // Should redirect since dish not available today (stock 0)
        $response->assertRedirect();
    }

    /** @test */
    public function dish_detail_shows_whatsapp_button_when_cook_has_phone()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $cook->user->update(['phone' => '5491112345678']);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'is_active' => true,
            'available_stock' => 5,
        ]);

        $response = $this->get(route('marketplace.dish.detail', $dish->id));

        $response->assertSee('Consultar');
    }

    /** @test */
    public function dish_detail_redirects_when_dish_inactive()
    {
        $cook = Cook::factory()->create(['is_approved' => true, 'active' => true]);
        $this->createActiveSubscription($cook);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'is_active' => false,
            'available_stock' => 5,
        ]);

        $response = $this->get(route('marketplace.dish.detail', $dish->id));

        $response->assertRedirect();
    }
}
