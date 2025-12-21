<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo_path',
        'is_suspended',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
        ];
    }

    /**
     * Relación con Cook (1:1 para usuarios con role='cook')
     */
    public function cook(): HasOne
    {
        return $this->hasOne(Cook::class);
    }

    /**
     * Relación con Orders como Customer
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Relación con Reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    /**
     * Relación con DeliveryDriver (1:1 para usuarios con role='delivery_driver')
     */
    public function deliveryDriver(): HasOne
    {
        return $this->hasOne(DeliveryDriver::class);
    }

    /**
     * Scope para filtrar por rol
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Verificar si es cocinero
     */
    public function isCook(): bool
    {
        return $this->role === 'cook';
    }

    /**
     * Verificar si es cliente
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Verificar si es admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si es delivery driver
     */
    public function isDeliveryDriver(): bool
    {
        return $this->role === 'delivery_driver';
    }
}
