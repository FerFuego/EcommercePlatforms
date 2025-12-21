<?php

namespace Database\Factories;

use App\Models\Cook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CookFactory extends Factory
{
    protected $model = Cook::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'cook']),
            'bio' => fake()->paragraph(3),
            'dni_photo' => 'cooks/dni/test.jpg',
            'kitchen_photos' => ['cooks/kitchens/test1.jpg', 'cooks/kitchens/test2.jpg'],
            'rating_avg' => fake()->randomFloat(1, 3.5, 5.0),
            'rating_count' => fake()->numberBetween(0, 50),
            'active' => true,
            'location_lat' => fake()->latitude(-33, -31),
            'location_lng' => fake()->longitude(-64, -62),
            'coverage_radius_km' => fake()->numberBetween(5, 15),
            'payout_method' => 'cbu',
            'payout_details' => ['cbu' => '0000003100' . fake()->numerify('##########')],
            'is_approved' => true,
            'food_handler_declaration' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'active' => false,
        ]);
    }
}
