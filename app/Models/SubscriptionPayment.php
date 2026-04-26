<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cook_id',
        'subscription_plan_id',
        'amount',
        'currency',
        'payment_gateway',
        'payment_id',
        'preapproval_id',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function cook()
    {
        return $this->belongsTo(Cook::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
