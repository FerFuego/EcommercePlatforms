<?php

namespace App\Notifications;

use App\Channels\WebPushChannel;
use App\Channels\WhatsAppChannel;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryDriverAssignedNotification extends Notification implements ShouldQueue
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

    public function toMail(object $notifiable): MailMessage
    {
        $this->order->loadMissing(['cook.user', 'customer']);
        $cookAddress = $this->order->cook->address ?? 'Cocina';
        $customerAddress = $this->order->delivery_address ?? 'Dirección del cliente';

        return (new MailMessage)
            ->subject('🛵 ¡Nuevo Pedido Asignado para Entrega! #' . $this->order->id)
            ->line('Se te ha asignado el pedido #' . $this->order->id)
            ->line('📍 Retiro: ' . $cookAddress)
            ->line('📍 Entrega: ' . $customerAddress)
            ->action('Ver detalle de entrega', route('delivery-driver.orders.show', $this->order->id));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => '🛵 ¡Nuevo Pedido Asignado! #' . $this->order->id,
            'body' => 'Retiro en cocina. Toca para ver el mapa y detalles.',
            'icon' => '/icon.png',
            'data' => [
                'url' => route('delivery-driver.orders.show', $this->order->id),
                'order_id' => $this->order->id,
            ],
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $this->order->loadMissing(['cook.user', 'customer']);
        $cookName = $this->order->cook->user->name ?? 'Cocinero';
        $cookAddress = $this->order->cook->address ?? 'Dirección de cocina';
        $customerName = $this->order->customer->name ?? 'Cliente';
        $deliveryAddress = $this->order->delivery_address ?? 'Sin dirección';
        $fee = number_format($this->order->delivery_fee ?? 500, 0, ',', '.');
        $url = route('delivery-driver.orders.show', $this->order->id);

        $lines = [];
        $lines[] = "🛵 *¡Nuevo Pedido Asignado para Reparto! #{$this->order->id}*";
        $lines[] = "";
        $lines[] = "¡Hola {$notifiable->name}! Tenés un pedido disponible para entrega:";
        $lines[] = "";
        $lines[] = "👨‍🍳 *Cocina (Retiro):* {$cookName}";
        $lines[] = "📍 *Dirección Retiro:* {$cookAddress}";
        $lines[] = "";
        $lines[] = "👤 *Cliente (Entrega):* {$customerName}";
        $lines[] = "🏠 *Dirección Entrega:* {$deliveryAddress}";
        $lines[] = "💵 *Ganancia Envío:* \${$fee}";
        $lines[] = "";
        $lines[] = "👉 Ver detalle en la app: {$url}";

        return implode("\n", $lines);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Nuevo pedido #' . $this->order->id . ' asignado para entrega',
        ];
    }
}
