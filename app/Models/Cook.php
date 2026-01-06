<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cook extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bio',
        'kitchen_photos',
        'rating_avg',
        'rating_count',
        'active',
        'location_lat',
        'location_lng',
        'coverage_radius_km',
        'payout_method',
        'payout_details',
        'dni_photo',
        'food_handler_declaration',
        'is_approved',
        'opening_time',
        'closing_time',
        'max_scheduled_portions_per_day',
    ];

    protected $casts = [
        'kitchen_photos' => 'array',
        'payout_details' => 'array',
        'active' => 'boolean',
        'food_handler_declaration' => 'boolean',
        'is_approved' => 'boolean',
        'rating_avg' => 'decimal:2',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'coverage_radius_km' => 'decimal:2',
        'max_scheduled_portions_per_day' => 'integer',
    ];

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Dishes
     */
    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    /**
     * Relación con Orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relación con Reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope para filtrar cocineros cercanos usando fórmula Haversine
     * 
     * @param $query
     * @param float $lat Latitud del usuario
     * @param float $lng Longitud del usuario
     * @param float $radius Radio de búsqueda en km
     */
    public function scopeNearby($query, $lat, $lng, $radius = 10)
    {
        $haversine = "(6371 * acos(cos(radians($lat)) 
                     * cos(radians(location_lat)) 
                     * cos(radians(location_lng) - radians($lng)) 
                     + sin(radians($lat)) 
                     * sin(radians(location_lat))))";

        return $query
            ->whereRaw("{$haversine} < ?", [$radius])
            ->where('is_approved', true)
            ->where('active', true)
            ->orderByRaw("{$haversine} ASC");
    }

    /**
     * Actualizar rating promedio del cocinero (desde reviews)
     */
    public function updateRatingFromReviews(): void
    {
        // Use aggregate queries directly on the relationship to avoid loading all models
        // and to ensure we get the fresh count from the DB
        $this->rating_count = $this->reviews()->count();
        $this->rating_avg = $this->reviews()->avg('rating') ?? 0;
        $this->save();
    }

    /**
     * Actualizar rating con un nuevo valor (para tests)
     */
    public function updateRating(int $newRating): void
    {
        $totalRating = ($this->rating_avg * $this->rating_count) + $newRating;
        $this->rating_count++;
        $this->attributes['rating_avg'] = round($totalRating / $this->rating_count, 1);
        $this->save();
    }

    /**
     * Scope para cocineros aprobados
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope para cocineros activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Accessor para kitchen_photos
     */
    public function getKitchenPhotosAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Mutator para kitchen_photos
     */
    public function setKitchenPhotosAttribute($value)
    {
        $this->attributes['kitchen_photos'] = json_encode($value);
    }
    /**
     * Relación con Usuarios que lo tienen de Favorito
     */
    public function favoritedBy(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_cooks')
            ->withTimestamps();
    }
}
