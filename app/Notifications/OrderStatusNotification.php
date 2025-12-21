<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Order;
use App\Channels\WhatsAppChannel;

use App\Channels\WebPushChannel;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class, WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Actualización de tu pedido #' . $this->order->id)
            ->line('Tu pedido ha cambiado de estado.')
            ->line('Nuevo estado: ' . $this->getStatusLabel())
            ->action('Ver mi pedido', route('orders.show', $this->order->id))
            ->line('¡Gracias por elegirnos!');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $label = $this->getStatusLabel();
        return "🍱 *Actualización de Pedido #{$this->order->id}*\n\n" .
            "Hola {$notifiable->name},\n" .
            "Tu pedido ha cambiado al estado: *{$label}*.\n\n" .
            "Puedes ver el detalle aquí: " . route('orders.show', $this->order->id);
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Actualización de Pedido #' . $this->order->id,
            'body' => 'Tu pedido está ' . $this->getStatusLabel(),
            'icon' => '/icon.png',
            'data' => [
                'url' => route('orders.show', $this->order->id),
                'order_id' => $this->order->id,
            ],
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => 'Tu pedido #' . $this->order->id . ' está ' . $this->getStatusLabel(),
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
