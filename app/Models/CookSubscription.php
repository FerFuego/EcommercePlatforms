<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'cook_id',
        'plan_id',
        'provider',
        'provider_subscription_id',
        'provider_customer_id',
        'status',
        'current_period_start',
        'current_period_end',
        'cancel_at_period_end',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancel_at_period_end' => 'boolean',
    ];

    public function cook()
    {
        return $this->belongsTo(Cook::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive()
    {
        return $this->status === 'active' || $this->status === 'trialing'; // Adapt as needed
    }
}
