<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAssignment extends Model
{
    protected $fillable = [
        'order_id',
        'delivery_user_id',
        'status',
        'location_tracking',
        'pickup_lat',
        'pickup_lng',
        'delivery_lat',
        'delivery_lng',
        'delivery_fee',
        'picked_up_at',
        'delivered_at',
        'rejection_reason',
    ];

    protected $casts = [
        'location_tracking' => 'array',
        'pickup_lat' => 'decimal:8',
        'pickup_lng' => 'decimal:8',
        'delivery_lat' => 'decimal:8',
        'delivery_lng' => 'decimal:8',
        'delivery_fee' => 'decimal:2',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Relación con Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con Delivery User
     */
    public function deliveryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_user_id');
    }

    /**
     * Agregar ubicación al tracking
     */
    public function addLocationTracking(float $lat, float $lng): void
    {
        $tracking = $this->location_tracking ?? [];
        $tracking[] = [
            'lat' => $lat,
            'lng' => $lng,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->location_tracking = $tracking;
        $this->save();
    }
}
