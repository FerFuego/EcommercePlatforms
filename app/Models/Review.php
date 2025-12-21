<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'customer_id',
        'cook_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Relación con Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con Customer (User)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relación con Cook
     */
    public function cook(): BelongsTo
    {
        return $this->belongsTo(Cook::class);
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function booted(): void
    {
        // Actualizar rating del cocinero cuando se crea un review
        static::created(function ($review) {
            $review->cook->updateRatingFromReviews();
        });

        // Actualizar rating del cocinero cuando se actualiza un review
        static::updated(function ($review) {
            $review->cook->updateRatingFromReviews();
        });

        // Actualizar rating del cocinero cuando se elimina un review
        static::deleted(function ($review) {
            $review->cook->updateRatingFromReviews();
        });
    }
}
