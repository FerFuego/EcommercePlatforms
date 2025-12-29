<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'dish_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relación con Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con Dish
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }

    /**
     * Relación con opciones seleccionadas
     */
    public function options(): HasMany
    {
        return $this->hasMany(OrderItemOption::class);
    }

    /**
     * Mutator para calcular total_price automáticamente
     */
    public function setQuantityAttribute($value): void
    {
        $this->attributes['quantity'] = $value;

        if (isset($this->attributes['unit_price'])) {
            $this->attributes['total_price'] = $value * $this->attributes['unit_price'];
        }
    }

    /**
     * Mutator para calcular total_price automáticamente
     */
    public function setUnitPriceAttribute($value): void
    {
        $this->attributes['unit_price'] = $value;

        if (isset($this->attributes['quantity'])) {
            $this->attributes['total_price'] = $value * $this->attributes['quantity'];
        }
    }
}
