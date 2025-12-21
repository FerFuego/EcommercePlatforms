<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use App\Models\Cook;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'cook_id' => $cook->id,
            'customer_id' => $customer->id,
            'status' => Order::STATUS_DELIVERED,
        ]);

        return [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->boolean(70) ? fake()->sentence(15) : null,
        ];
    }
}
