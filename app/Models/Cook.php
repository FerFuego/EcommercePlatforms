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
        'active',
        'location_lat',
        'location_lng',
        'coverage_radius_km',
        'payout_method',
        'payout_details',
        'dni_photo',
        'food_handler_declaration',
        'opening_time',
        'closing_time',
        'max_scheduled_portions_per_day',
        'current_subscription_id',
        'sales_reset_at',
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
        'monthly_sales_accumulated' => 'decimal:2',
        'monthly_orders_accumulated' => 'integer',
        'sales_reset_at' => 'datetime',
        'is_selling_blocked' => 'boolean',
    ];

    const CLOSING_SOON_MINUTES = 30;

    /**
     * Bootstrap model events.
     */
    protected static function booted(): void
    {
        static::saving(function (Cook $cook) {
            if ((is_null($cook->location_lat) || is_null($cook->location_lng) || ($cook->location_lat == 0 && $cook->location_lng == 0))) {
                $address = $cook->user->address ?? null;
                if (!empty($address)) {
                    $coords = \App\Services\GeocodingService::geocodeAddress($address);
                    if ($coords) {
                        $cook->location_lat = $coords['lat'];
                        $cook->location_lng = $coords['lng'];
                    }
                }
            }
        });
    }

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
        $lat = (float) $lat;
        $lng = (float) $lng;
        $radius = (float) $radius;

        $haversine = "(6371 * acos(cos(radians(?)) 
                     * cos(radians(location_lat)) 
                     * cos(radians(location_lng) - radians(?)) 
                     + sin(radians(?)) 
                     * sin(radians(location_lat))))";

        return $query
            ->whereRaw("{$haversine} < (? + 0)", [$lat, $lng, $lat, $radius])
            ->where('is_approved', true)
            ->where('active', true)
            ->orderByRaw("{$haversine} ASC", [$lat, $lng, $lat]);
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

    /**
     * Relación con CookSubscription (suscripción actual)
     */
    public function currentSubscription(): BelongsTo
    {
        return $this->belongsTo(CookSubscription::class, 'current_subscription_id');
    }

    /**
     * Relación con SubscriptionPlan (plan actual a través de la suscripción)
     */
    public function plan()
    {
        // Alternativamente, se puede definir un HasOneThrough si es más limpio,
        // pero acceder por la suscripción es más directo
        if ($this->currentSubscription) {
            return $this->currentSubscription->plan;
        }
        return null;
    }

    /**
     * Verificar si el cocinero tiene una feature premium en su plan actual
     */
    public function hasFeature(string $featureKey): bool
    {
        $plan = $this->plan();
        if (!$plan || !$plan->features) {
            return false;
        }

        return isset($plan->features[$featureKey]) && $plan->features[$featureKey] === true;
    }

    /**
     * Evalúa dinámicamente si el cocinero superó los límites de su plan actual.
     * Sincroniza automáticamente la columna is_selling_blocked en la base de datos.
     */
    public function isSellingBlocked(): bool
    {
        $plan = $this->plan();

        if (!$plan) {
            return (bool) $this->is_selling_blocked;
        }

        $exceedsSales = $plan->monthly_sales_limit !== null
            && (float) $plan->monthly_sales_limit > 0
            && (float) $this->monthly_sales_accumulated > (float) $plan->monthly_sales_limit;

        $exceedsOrders = $plan->monthly_orders_limit !== null
            && (int) $plan->monthly_orders_limit > 0
            && (int) $this->monthly_orders_accumulated > (int) $plan->monthly_orders_limit;

        $shouldBlock = $exceedsSales || $exceedsOrders;

        // Si el estado en BD difiere del estado real calculado, corregirlo en la BD
        if ((bool) $this->is_selling_blocked !== $shouldBlock) {
            $this->is_selling_blocked = $shouldBlock;
            $this->save();
        }

        return $shouldBlock;
    }

    /**
     * Incrementa las métricas mensuales y verifica si supera el límite de su plan actual.
     */
    public function incrementMetricsAndCheckLimits(float $amount): void
    {
        $this->monthly_sales_accumulated = number_format((float) $this->monthly_sales_accumulated + $amount, 2, '.', '');
        $this->monthly_orders_accumulated = (int) $this->monthly_orders_accumulated + 1;

        $this->isSellingBlocked();
        $this->save();
    }

    /**
     * Evalúa el estado operativo actual del cocinero respecto a su horario de atención.
     */
    public function getOperatingStatus(): array
    {
        $openingFormatted = $this->opening_time ? \Carbon\Carbon::parse($this->opening_time)->format('H:i') : null;
        $closingFormatted = $this->closing_time ? \Carbon\Carbon::parse($this->closing_time)->format('H:i') : null;

        if (!$this->active) {
            return [
                'status' => 'closed',
                'label' => 'Cocina Cerrada',
                'badge_color' => 'red',
                'reason' => 'La cocina se encuentra cerrada temporalmente. Puedes realizar pedidos programados para los próximos días.',
                'accepts_immediate' => false,
                'next_available_date' => 'tomorrow',
                'opening_time_formatted' => $openingFormatted,
                'closing_time_formatted' => $closingFormatted,
            ];
        }

        if (!$this->opening_time || !$this->closing_time) {
            return [
                'status' => 'open',
                'label' => 'Abierto ahora',
                'badge_color' => 'green',
                'reason' => 'Aceptando pedidos inmediatos y programados.',
                'accepts_immediate' => true,
                'next_available_date' => 'today',
                'opening_time_formatted' => $openingFormatted,
                'closing_time_formatted' => $closingFormatted,
            ];
        }

        $now = now();
        $nowTime = $now->format('H:i:s');
        $opening = \Carbon\Carbon::parse($this->opening_time)->format('H:i:s');
        $closing = \Carbon\Carbon::parse($this->closing_time)->format('H:i:s');

        $closingCarbon = \Carbon\Carbon::createFromTimeString($closing);
        $cutoffCarbon = (clone $closingCarbon)->subMinutes(self::CLOSING_SOON_MINUTES);
        $cutoff = $cutoffCarbon->format('H:i:s');

        $isOpen = false;
        $isClosingSoon = false;
        $nextDate = 'today';

        if ($opening < $closing) {
            if ($nowTime >= $opening && $nowTime < $cutoff) {
                $isOpen = true;
                $nextDate = 'today';
            } elseif ($nowTime >= $cutoff && $nowTime <= $closing) {
                $isOpen = true;
                $isClosingSoon = true;
                $nextDate = 'tomorrow';
            } elseif ($nowTime > $closing) {
                $isOpen = false;
                $nextDate = 'tomorrow';
            } else {
                $isOpen = false;
                $nextDate = 'today';
            }
        } else {
            // Horario nocturno que cruza medianoche (ej: 20:00 a 02:00)
            $isWithinHours = ($nowTime >= $opening || $nowTime <= $closing);
            if ($isWithinHours) {
                if ($nowTime >= $cutoff && $nowTime <= $closing) {
                    $isOpen = true;
                    $isClosingSoon = true;
                    $nextDate = 'tomorrow';
                } else {
                    $isOpen = true;
                    $nextDate = 'today';
                }
            } else {
                $isOpen = false;
                $nextDate = ($nowTime > $closing && $nowTime < $opening) ? 'today' : 'tomorrow';
            }
        }

        if ($isClosingSoon) {
            return [
                'status' => 'closing_soon',
                'label' => 'Cierra pronto',
                'badge_color' => 'amber',
                'reason' => "La cocina cierra a las {$closingFormatted} hs (en menos de " . self::CLOSING_SOON_MINUTES . " min). Los nuevos pedidos son únicamente programados para el día siguiente.",
                'accepts_immediate' => false,
                'next_available_date' => 'tomorrow',
                'opening_time_formatted' => $openingFormatted,
                'closing_time_formatted' => $closingFormatted,
            ];
        }

        if ($isOpen) {
            return [
                'status' => 'open',
                'label' => 'Abierto ahora',
                'badge_color' => 'green',
                'reason' => "Atendiendo hoy hasta las {$closingFormatted} hs.",
                'accepts_immediate' => true,
                'next_available_date' => 'today',
                'opening_time_formatted' => $openingFormatted,
                'closing_time_formatted' => $closingFormatted,
            ];
        }

        $nextMsg = ($nextDate === 'today')
            ? "Abre hoy a las {$openingFormatted} hs. Puedes realizar tu pedido de forma programada."
            : "La cocina cerró por hoy y abre mañana a las {$openingFormatted} hs. Los pedidos son únicamente programados para el día siguiente.";

        return [
            'status' => 'closed',
            'label' => 'Cocina Cerrada',
            'badge_color' => 'red',
            'reason' => $nextMsg,
            'accepts_immediate' => false,
            'next_available_date' => $nextDate,
            'opening_time_formatted' => $openingFormatted,
            'closing_time_formatted' => $closingFormatted,
        ];
    }

    public function isOpenNow(): bool
    {
        return $this->getOperatingStatus()['accepts_immediate'];
    }

    public function isClosingSoon(): bool
    {
        return $this->getOperatingStatus()['status'] === 'closing_soon';
    }

    public function isClosedNow(): bool
    {
        return $this->getOperatingStatus()['status'] === 'closed';
    }
}
