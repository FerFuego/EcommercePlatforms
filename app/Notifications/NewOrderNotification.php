<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Channels\WebPushChannel;
use App\Channels\WhatsAppChannel;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['mail', WebPushChannel::class, WhatsAppChannel::class];
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

    public function toWhatsApp(object $notifiable): array
    {
        $order = $this->order;
        $order->loadMissing(['customer', 'items.dish']);

        $customerName = $order->customer->name ?? 'Cliente';
        
        $details = [];
        foreach ($order->items as $item) {
            $dishName = $item->dish->name ?? 'Plato';
            $details[] = "- {$item->quantity}x {$dishName}";
        }
        $detailString = empty($details) ? 'Sin detalle' : implode(", ", $details);

        $deliveryType = $order->delivery_type === 'delivery' ? 'Delivery' : 'Retiro en cocina';
        
        return [
            'type' => 'template',
            'name' => 'nuevo_pedido_cocinero',
            'language' => 'es_ES',
            'components' => [
                $order->id,
                $customerName,
                $detailString,
                number_format($order->total_amount, 0, ',', '.'),
                $deliveryType,
                route('cook.orders.index')
            ]
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Nuevo pedido #' . $this->order->id,
        ];
    }
}
