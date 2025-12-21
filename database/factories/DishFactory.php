<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\Cook;
use Illuminate\Database\Eloquent\Factories\Factory;

class DishFactory extends Factory
{
    protected $model = Dish::class;

    public function definition(): array
    {
        return [
            'cook_id' => Cook::factory(),
            'name' => fake()->randomElement([
                'Guiso de Lentejas',
                'Milanesa con Puré',
                'Ravioles Caseros',
                'Empanadas de Carne',
                'Lasagna Casera',
                'Pollo al Horno',
            ]),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 800, 2500),
            'photo_url' => null,
            'available_stock' => fake()->numberBetween(0, 20),
            'is_active' => true,
            'diet_tags' => fake()->randomElement([
                [],
                ['vegetarian'],
                ['vegan', 'gluten-free'],
                ['gluten-free'],
                ['low-carb'],
            ]),
            'available_days' => [1, 2, 3, 4, 5, 6],
            'preparation_time_minutes' => fake()->numberBetween(20, 60),
            'delivery_method' => 'both',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'available_stock' => 0,
        ]);
    }
}
