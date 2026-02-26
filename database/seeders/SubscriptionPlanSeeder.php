<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::updateOrCreate(
            ['slug' => 'basico-free'],
            [
                'name' => 'Básico (FREE)',
                'price' => 0.00,
                'currency' => 'ARS',
                'billing_period' => 'monthly',
                'monthly_sales_limit' => 50000.00, // Example limit
                'monthly_orders_limit' => 100,
                'commission_percentage' => 0,
                'features' => [
                    'premium_badge' => false,
                    'priority_listing' => false,
                ],
                'is_active' => true,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'estandar'],
            [
                'name' => 'Estándar',
                'price' => 20000.00,
                'currency' => 'ARS',
                'billing_period' => 'monthly',
                'monthly_sales_limit' => 200000.00, // Example limit
                'monthly_orders_limit' => 200,
                'commission_percentage' => 0,
                'features' => [
                    'premium_badge' => false,
                    'priority_listing' => false,
                ],
                'is_active' => true,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'profesional'],
            [
                'name' => 'Profesional',
                'price' => 50000.00,
                'currency' => 'ARS',
                'billing_period' => 'monthly',
                'monthly_sales_limit' => 500000.00, // Example limit
                'monthly_orders_limit' => 400,
                'commission_percentage' => 0,
                'features' => [
                    'premium_badge' => false,
                    'priority_listing' => false,
                ],
                'is_active' => true,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'premium'],
            [
                'name' => 'Premium',
                'price' => 100000.00,
                'currency' => 'ARS',
                'billing_period' => 'monthly',
                'monthly_sales_limit' => null, // Unlimited
                'monthly_orders_limit' => null,
                'commission_percentage' => 0,
                'features' => [
                    'premium_badge' => true,
                    'priority_listing' => true,
                ],
                'is_active' => true,
            ]
        );
    }
}
