<?php

namespace Tests\Unit;

use App\Models\Cook;
use App\Models\User;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_calculate_nearby_cooks_using_haversine_formula()
    {
        // Create cooks at different locations
        $centerCook = Cook::factory()->create([
            'location_lat' => -32.1745,
            'location_lng' => -63.2963,
            'is_approved' => true,
            'active' => true,
        ]);

        $nearbyCook = Cook::factory()->create([
            'location_lat' => -32.1756, // ~1.2km away
            'location_lng' => -63.2945,
            'is_approved' => true,
            'active' => true,
        ]);

        $farCook = Cook::factory()->create([
            'location_lat' => -32.2000, // ~3km away
            'location_lng' => -63.3200,
            'is_approved' => true,
            'active' => true,
        ]);

        // Search within 2km radius
        $results = Cook::nearby(-32.1745, -63.2963, 2)
            ->where('is_approved', true)
            ->where('active', true)
            ->get();

        $this->assertCount(2, $results); // Should find centerCook and nearbyCook
        $this->assertTrue($results->contains($centerCook));
        $this->assertTrue($results->contains($nearbyCook));
        $this->assertFalse($results->contains($farCook));
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $cook->user);
        $this->assertEquals($user->id, $cook->user->id);
    }

    /** @test */
    public function it_has_many_dishes()
    {
        $cook = Cook::factory()->create();
        $dishes = Dish::factory()->count(3)->create(['cook_id' => $cook->id]);

        $this->assertCount(3, $cook->dishes);
        $this->assertInstanceOf(Dish::class, $cook->dishes->first());
    }

    /** @test */
    public function it_has_many_orders()
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);

        $orders = Order::factory()->count(2)->create([
            'cook_id' => $cook->id,
            'customer_id' => $customer->id,
        ]);

        $this->assertCount(2, $cook->orders);
        $this->assertInstanceOf(Order::class, $cook->orders->first());
    }

    /** @test */
    public function it_has_many_reviews()
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'cook_id' => $cook->id,
            'customer_id' => $customer->id,
        ]);

        $review = Review::factory()->create([
            'cook_id' => $cook->id,
            'customer_id' => $customer->id,
            'order_id' => $order->id,
        ]);

        $this->assertCount(1, $cook->reviews);
        $this->assertInstanceOf(Review::class, $cook->reviews->first());
    }

    /** @test */
    public function it_filters_approved_cooks()
    {
        Cook::factory()->create(['is_approved' => true]);
        Cook::factory()->create(['is_approved' => true]);
        Cook::factory()->create(['is_approved' => false]);

        $approved = Cook::approved()->get();

        $this->assertCount(2, $approved);
    }

    /** @test */
    public function it_filters_active_cooks()
    {
        Cook::factory()->create(['active' => true]);
        Cook::factory()->create(['active' => false]);

        $active = Cook::active()->get();

        $this->assertCount(1, $active);
    }

    /** @test */
    public function it_updates_rating_average()
    {
        $cook = Cook::factory()->create([
            'rating_avg' => 0,
            'rating_count' => 0,
        ]);

        $cook->updateRating(5);
        $this->assertEquals(5.0, $cook->fresh()->rating_avg);
        $this->assertEquals(1, $cook->fresh()->rating_count);

        $cook->updateRating(3);
        $this->assertEquals(4.0, $cook->fresh()->rating_avg);
        $this->assertEquals(2, $cook->fresh()->rating_count);
    }

    /** @test */
    public function kitchen_photos_are_cast_to_array()
    {
        $cook = Cook::factory()->create([
            'kitchen_photos' => ['photo1.jpg', 'photo2.jpg', 'photo3.jpg'],
        ]);

        $this->assertIsArray($cook->kitchen_photos);
        $this->assertCount(3, $cook->kitchen_photos);
    }

    /** @test */
    public function payout_details_are_cast_to_array()
    {
        $cook = Cook::factory()->create([
            'payout_details' => ['cbu' => '0000003100123456789012'],
        ]);

        $this->assertIsArray($cook->payout_details);
        $this->assertArrayHasKey('cbu', $cook->payout_details);
    }
}
