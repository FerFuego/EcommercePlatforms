<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dish extends Model
{
    use HasFactory;
    protected $fillable = [
        'cook_id',
        'name',
        'description',
        'price',
        'photo_url',
        'available_stock',
        'is_active',
        'available_days',
        'preparation_time_minutes',
        'delivery_method',
        'diet_tags',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
        'available_days' => 'array',
        'diet_tags' => 'array',
    ];

    /**
     * Relación con Cook
     */
    public function cook(): BelongsTo
    {
        return $this->belongsTo(Cook::class);
    }

    /**
     * Relación con OrderItems
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relación con Orders (a través de OrderItems)
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'total_price'])
            ->withTimestamps();
    }

    /**
     * Scope para filtrar platos disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where('available_stock', '>', 0);
    }

    /**
     * Scope para filtrar platos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para filtrar por dieta
     * 
     * @param $query
     * @param array $dietTags Etiquetas de dieta a buscar
     */
    public function scopeByDiet($query, $dietTags)
    {
        if (empty($dietTags)) {
            return $query;
        }

        return $query->where(function ($q) use ($dietTags) {
            foreach ($dietTags as $tag) {
                $q->orWhereJsonContains('diet_tags', $tag);
            }
        });
    }

    /**
     * Verificar si el plato está disponible hoy
     */
    public function isAvailableToday(): bool
    {
        if (!$this->is_active || $this->available_stock <= 0) {
            return false;
        }

        // Si no hay días específicos, está disponible todos los días
        if (empty($this->available_days)) {
            return true;
        }

        // Verificar si hoy está en los días disponibles (1=Monday, 7=Sunday)
        $today = now()->dayOfWeekIso;
        return in_array($today, $this->available_days);
    }

    /**
     * Verificar si está disponible en un día específico
     */
    public function isAvailableOnDay(int $day): bool
    {
        if (empty($this->available_days)) {
            return true;
        }
        return in_array($day, $this->available_days);
    }

    /**
     * Verificar si tiene stock disponible
     */
    public function hasStock(): bool
    {
        return $this->available_stock > 0;
    }

    /**
     * Decrementar stock
     */
    public function decrementStock(int $quantity): bool
    {
        if ($this->available_stock >= $quantity) {
            $this->available_stock -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Incrementar stock
     */
    public function incrementStock(int $quantity): void
    {
        $this->available_stock += $quantity;
        $this->save();
    }
}
