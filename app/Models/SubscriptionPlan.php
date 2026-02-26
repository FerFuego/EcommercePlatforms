<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'billing_period',
        'monthly_sales_limit',
        'monthly_orders_limit',
        'commission_percentage',
        'stripe_price_id',
        'mp_plan_id',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'monthly_sales_limit' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(CookSubscription::class, 'plan_id');
    }
}
