<?php

namespace App\Notifications;

use App\Channels\WebPushChannel;
use App\Channels\WhatsAppChannel;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDeliveryStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Order $order;
    public string $deliveryStatus;

    public function __construct(Order $order, string $deliveryStatus)
    {
        $this->order = $order;
        $this->deliveryStatus = $deliveryStatus;
    }

    public function via(object $notifiable): array
    {
        // No enviar email al cliente (solo WhatsApp y WebPush); mantener correo para cocineros
        if ($notifiable->id === $this->order->customer_id || ($notifiable->role ?? null) === 'customer') {
            return [WebPushChannel::class, WhatsAppChannel::class];
        }

        return ['mail', WebPushChannel::class, WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🛵 Actualización de tu envío #' . $this->order->id)
            ->line('Estado de la entrega: ' . $this->getDeliveryStatusLabel())
            ->action('Seguir mi pedido', route('orders.show', $this->order->id));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => '🛵 Estado de envío #' . $this->order->id,
            'body' => $this->getDeliveryStatusLabel(),
            'icon' => '/icon.png',
            'data' => [
                'url' => route('orders.show', $this->order->id),
                'order_id' => $this->order->id,
            ],
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $label = $this->getDeliveryStatusLabel();
        $url = route('orders.show', $this->order->id);

        $lines = [];
        $lines[] = "🛵 *Novedades de tu envío — Pedido #{$this->order->id}*";
        $lines[] = "";
        $lines[] = "¡Hola {$notifiable->name}!";
        $lines[] = "Tu pedido actualizó el estado de entrega:";
        $lines[] = "";
        $lines[] = "📌 *Estado:* {$label}";
        $lines[] = "";
        $lines[] = "📱 Ver mapa y seguimiento: {$url}";

        return implode("\n", $lines);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'delivery_status' => $this->deliveryStatus,
            'message' => 'Estado de entrega de pedido #' . $this->order->id . ': ' . $this->getDeliveryStatusLabel(),
        ];
    }

    protected function getDeliveryStatusLabel(): string
    {
        $labels = [
            'picked_up' => 'El repartidor retiró el pedido de la cocina 🍳',
            'on_the_way' => '¡El repartidor va en camino a tu domicilio! 🛵💨',
            'delayed' => 'El repartidor reportó una leve demora en el trayecto ⏳',
            'delivered' => '¡Pedido entregado con éxito! 🎉',
        ];

        return $labels[$this->deliveryStatus] ?? 'Actualización en el reparto';
    }
}
