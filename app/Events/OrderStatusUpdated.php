<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.' . $this->order->id),
        ];
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'status' => $this->order->status,
            'status_label' => $this->getStatusLabel(),
        ];
    }

    protected function getStatusLabel(): string
    {
        $labels = [
            Order::STATUS_PENDING_PAYMENT => 'Pendiente de Pago',
            Order::STATUS_PAID => 'Pagado',
            Order::STATUS_AWAITING_COOK => 'Esperando Cocinero',
            Order::STATUS_REJECTED => 'Rechazado',
            Order::STATUS_PREPARING => 'En Preparación',
            Order::STATUS_READY => 'Listo para Retirar',
            Order::STATUS_ASSIGNED_DELIVERY => 'Asignado a Repartidor',
            Order::STATUS_ON_THE_WAY => 'En Camino',
            Order::STATUS_DELIVERED => 'Entregado',
            Order::STATUS_CANCELLED => 'Cancelado',
        ];

        return $labels[$this->order->status] ?? $this->order->status;
    }
}
