<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Cook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 1000, 5000);
        $deliveryFee = fake()->boolean() ? 500 : 0;

        return [
            'customer_id' => User::factory()->create(['role' => 'customer']),
            'cook_id' => Cook::factory(),
            'status' => Order::STATUS_PAID,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $subtotal + $deliveryFee,
            'commission_amount' => round($subtotal * 0.12, 2),
            'delivery_type' => $deliveryFee > 0 ? 'delivery' : 'pickup',
            'delivery_address' => $deliveryFee > 0 ? fake()->address() : null,
            'payment_method' => fake()->randomElement(['mercadopago', 'cash', 'transfer']),
            'payment_id' => 'PAY_' . strtoupper(fake()->bothify('????????')),
            'payment_status' => 'approved',
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_PENDING_PAYMENT,
            'payment_id' => null,
        ]);
    }

    public function awaitingCook(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_AWAITING_COOK,
        ]);
    }

    public function preparing(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_PREPARING,
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_DELIVERED,
            'completed_at' => now(),
        ]);
    }
}
