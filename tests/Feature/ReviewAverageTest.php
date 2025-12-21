<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewAverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_cook_rating_updates_when_review_is_created()
    {
        // 1. Create Cook
        $cookUser = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $cookUser->id]);

        // 2. Create Customer and Order
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'cook_id' => $cook->id,
            'customer_id' => $customer->id,
            'status' => 'delivered'
        ]);

        // 3. Create Review (5 stars)
        $review = Review::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'rating' => 5,
            'comment' => 'Excellent!'
        ]);

        // 4. Verify Cook Stats
        $cook->refresh();
        $this->assertEquals(1, $cook->rating_count);
        $this->assertEquals(5.0, $cook->rating_avg);

        // 5. Create Second Review (3 stars)
        $customer2 = User::factory()->create(['role' => 'customer']);
        $order2 = Order::factory()->create([
            'cook_id' => $cook->id,
            'customer_id' => $customer2->id,
            'status' => 'delivered'
        ]);

        Review::create([
            'order_id' => $order2->id,
            'customer_id' => $customer2->id,
            'cook_id' => $cook->id,
            'rating' => 3,
            'comment' => 'Good'
        ]);

        // 6. Verify Cook Stats Updated (Average of 5 and 3 is 4)
        $cook->refresh();
        $this->assertEquals(2, $cook->rating_count);
        $this->assertEquals(4.0, $cook->rating_avg);
    }

    public function test_cook_rating_updates_when_review_is_deleted()
    {
        // 1. Setup Cook with 2 reviews (5 and 3 stars)
        $cookUser = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $cookUser->id]);

        $customer = User::factory()->create(['role' => 'customer']);
        $order1 = Order::factory()->create(['cook_id' => $cook->id, 'customer_id' => $customer->id, 'status' => 'delivered']);
        $review1 = Review::create(['order_id' => $order1->id, 'customer_id' => $customer->id, 'cook_id' => $cook->id, 'rating' => 5]);

        $order2 = Order::factory()->create(['cook_id' => $cook->id, 'customer_id' => $customer->id, 'status' => 'delivered']);
        $review2 = Review::create(['order_id' => $order2->id, 'customer_id' => $customer->id, 'cook_id' => $cook->id, 'rating' => 3]);

        $cook->refresh();
        $this->assertEquals(4.0, $cook->rating_avg);

        // 2. Delete one review
        $review2->delete();

        // 3. Verify Stats Updated (Should be 5.0 again)
        $cook->refresh();
        $this->assertEquals(1, $cook->rating_count);
        $this->assertEquals(5.0, $cook->rating_avg);
    }
}
