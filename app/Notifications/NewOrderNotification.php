<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Channels\WebPushChannel;

class NewOrderNotification extends Notification implements ShouldQueue
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
        // For now, let's use WebPush (Push) and Mail
        return ['mail', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Nuevo pedido recibido! #' . $this->order->id)
            ->line('Has recibido un nuevo pedido de ' . $this->order->customer->name)
            ->line('Total: $' . $this->order->total_amount)
            ->action('Ver pedido', route('cook.orders.index'))
            ->line('¡A cocinar!');
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => '¡Nuevo Pedido Recibido! 🍱',
            'body' => 'Has recibido el pedido #' . $this->order->id . ' de ' . $this->order->customer->name,
            'icon' => '/icon.png',
            'data' => [
                'url' => route('cook.orders.index'),
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
            'message' => 'Nuevo pedido #' . $this->order->id,
        ];
    }
}
