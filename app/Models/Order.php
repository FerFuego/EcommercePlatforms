<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'cook_id',
        'status',
        'delivery_type',
        'delivery_address',
        'delivery_fee',
        'delivery_lat',
        'delivery_lng',
        'subtotal',
        'commission_amount',
        'total_amount',
        'payment_method',
        'payment_id',
        'payment_status',
        'rejection_reason',
        'scheduled_time',
        'completed_at',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_lat' => 'decimal:8',
        'delivery_lng' => 'decimal:8',
        'scheduled_time' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Estados válidos del pedido
     */
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PAID = 'paid';
    const STATUS_AWAITING_COOK = 'awaiting_cook_acceptance';
    const STATUS_REJECTED = 'rejected_by_cook';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready_for_pickup';
    const STATUS_ASSIGNED_DELIVERY = 'assigned_to_delivery';
    const STATUS_ON_THE_WAY = 'on_the_way';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    // Aliases for tests
    const STATUS_REJECTED_BY_COOK = self::STATUS_REJECTED;
    const STATUS_READY_FOR_PICKUP = self::STATUS_READY;

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
     * Relación con OrderItems
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relación con Review
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Relación con DeliveryAssignment
     */
    public function deliveryAssignment(): HasOne
    {
        return $this->hasOne(DeliveryAssignment::class);
    }

    /**
     * Marcar como pagado
     */
    public function markAsPaid(string $paymentId = null): void
    {
        $this->status = self::STATUS_PAID;
        $this->payment_status = 'approved';
        if ($paymentId) {
            $this->payment_id = $paymentId;
        }
        $this->status = self::STATUS_AWAITING_COOK;
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));

        // Notificar al cliente
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));

        // Notificar al cocinero (otra notificación diferente sería ideal, pero por ahora usamos esta)
        if ($this->cook && $this->cook->user) {
            $this->cook->user->notify(new \App\Notifications\OrderStatusNotification($this));
        }
    }

    /**
     * Aceptar pedido por el cocinero
     */
    public function acceptByCook(): void
    {
        if ($this->status !== self::STATUS_AWAITING_COOK) {
            throw new \Exception('El pedido no está en estado de espera de aceptación');
        }

        $this->status = self::STATUS_PREPARING;
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));
    }

    /**
     * Rechazar pedido por el cocinero
     */
    public function rejectByCook(string $reason): void
    {
        if ($this->status !== self::STATUS_AWAITING_COOK) {
            throw new \Exception('El pedido no está en estado de espera de aceptación');
        }

        $this->status = self::STATUS_REJECTED;
        $this->rejection_reason = $reason;
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));
    }

    /**
     * Marcar como en preparación
     */
    public function markAsPreparing(): void
    {
        $this->status = self::STATUS_PREPARING;
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));
    }

    /**
     * Marcar como listo
     */
    public function markAsReady(): void
    {
        // Por defecto asumimos pickup, solo si explícitamente es delivery lo tratamos diferente
        if ($this->delivery_type === 'delivery') {
            $this->status = self::STATUS_ASSIGNED_DELIVERY;
        } else {
            $this->status = self::STATUS_READY_FOR_PICKUP;
        }
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));
    }

    /**
     * Marcar como en camino
     */
    public function markAsOnTheWay(): void
    {
        $this->status = self::STATUS_ON_THE_WAY;
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));
    }

    /**
     * Marcar como entregado
     */
    public function markAsDelivered(): void
    {
        $this->status = self::STATUS_DELIVERED;
        $this->completed_at = now();
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));
    }

    /**
     * Cancelar pedido
     */
    public function cancel(string $reason = null): void
    {
        $this->status = self::STATUS_CANCELLED;
        if ($reason) {
            $this->rejection_reason = $reason;
        }
        $this->save();

        event(new \App\Events\OrderStatusUpdated($this));
        $this->customer->notify(new \App\Notifications\OrderStatusNotification($this));
    }

    /**
     * Calcular comisión de la plataforma
     */
    public function calculateCommission(float $commissionRate = 0.12): void
    {
        // Multiplicar subtotal (string) por rate (float) usando BCMath
        $result = bcmul($this->subtotal, (string) $commissionRate, 2);

        $this->commission_amount = (string) $result; // decimal cast de Laravel lo convierte perfecto
        $this->save();
    }


    /**
     * Verificar si el pedido puede ser revisado
     */
    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_DELIVERED && !$this->review;
    }

    /**
     * Scope para pedidos pendientes
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [
            self::STATUS_AWAITING_COOK,
            self::STATUS_PREPARING,
        ]);
    }

    /**
     * Scope para pedidos completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }
}
