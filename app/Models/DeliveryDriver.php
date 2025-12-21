<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dni_number',
        'dni_photo',
        'profile_photo',
        'vehicle_type',
        'vehicle_plate',
        'vehicle_photo',
        'location_lat',
        'location_lng',
        'coverage_radius_km',
        'bank_name',
        'account_number',
        'account_type',
        'cbu_cvu',
        'is_approved',
        'is_available',
        'rating_avg',
        'rating_count',
        'total_deliveries',
        'total_earnings',
    ];

    protected $casts = [
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'is_approved' => 'boolean',
        'is_available' => 'boolean',
        'rating_avg' => 'decimal:2',
        'total_earnings' => 'decimal:2',
    ];

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con DeliveryAssignments
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class, 'delivery_user_id', 'user_id');
    }

    /**
     * Alternar disponibilidad (online/offline)
     */
    public function toggleAvailability(): void
    {
        $this->is_available = !$this->is_available;
        $this->save();
    }

    /**
     * Actualizar rating promedio
     */
    public function updateRating(int $newRating): void
    {
        $totalRating = ((float) $this->rating_avg * $this->rating_count) + $newRating;
        $this->rating_count++;
        $this->rating_avg = $totalRating / $this->rating_count;
        $this->save();
    }

    /**
     * Agregar ganancias
     */
    public function addEarnings(float $amount): void
    {
        $this->total_earnings = (float) $this->total_earnings + $amount;
        $this->total_deliveries++;
        $this->save();
    }

    /**
     * Verificar si está dentro del área de cobertura
     */
    public function isWithinCoverage(float $lat, float $lng): bool
    {
        $distance = $this->calculateDistance($lat, $lng);
        return $distance <= $this->coverage_radius_km;
    }

    /**
     * Calcular distancia usando fórmula Haversine
     */
    private function calculateDistance(float $lat, float $lng): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat - (float) $this->location_lat);
        $dLng = deg2rad($lng - (float) $this->location_lng);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad((float) $this->location_lat)) * cos(deg2rad($lat)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
