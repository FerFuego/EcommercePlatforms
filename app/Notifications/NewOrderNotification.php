<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Channels\WebPushChannel;
use App\Channels\WhatsAppChannel;

class NewOrderNotification extends Notification
{
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

    public function toWhatsApp(object $notifiable): ?string
    {
        $order = $this->order;
        $order->loadMissing(['customer', 'items.dish']);

        $customerName = $order->customer->name ?? 'Cliente';
        $lines = [];
        $lines[] = "🍲 *Nuevo Pedido Cocinarte* #{$order->id}";
        $lines[] = "";
        $lines[] = "¡Hola! Recibiste un pedido de *{$customerName}*:";
        $lines[] = "";

        foreach ($order->items as $item) {
            $dishName = $item->dish->name ?? 'Plato';
            $lines[] = "• {$item->quantity}x {$dishName}";
        }

        $lines[] = "";
        $lines[] = "💰 *Total: \$" . number_format($order->total_amount, 0, ',', '.') . "*";

        if ($order->delivery_type === 'delivery') {
            $lines[] = "🛵 *Delivery* a: {$order->delivery_address}";
        } else {
            $lines[] = "🏃 *Retiro en cocina*";
        }

        if ($order->scheduled_time) {
            $lines[] = "📅 " . $order->scheduled_time->format('d/m/Y H:i');
        }

        if ($order->notes) {
            $lines[] = "📝 {$order->notes}";
        }

        $lines[] = "";
        $lines[] = "👉 " . route('cook.orders.index');

        return implode("\n", $lines);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Nuevo pedido #' . $this->order->id,
        ];
    }
}
