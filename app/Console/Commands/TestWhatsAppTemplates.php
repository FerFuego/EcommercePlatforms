<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestWhatsAppTemplates extends Command
{
    protected $signature = 'test:whatsapp {phone} {order_id=1}';
    protected $description = 'Test WhatsApp templates by sending them to a specific phone number';

    public function handle()
    {
        $phone = $this->argument('phone');
        $orderId = $this->argument('order_id');

        $order = \App\Models\Order::find($orderId);
        if (!$order) {
            $this->error("Order {$orderId} not found.");
            return;
        }

        $this->info("Testing with Order #{$order->id} and Phone {$phone}");

        $notifiable = \App\Models\User::first();
        if (!$notifiable) {
            $this->error("No users found in database to act as notifiable.");
            return;
        }
        
        $notifiable->phone = $phone;
        $notifiable->email = 'test_whatsapp@example.com';

        try {
            $this->info("Sending NewOrderNotification (nuevo_pedido_cocinero)...");
            $notifiable->notifyNow(new \App\Notifications\NewOrderNotification($order));
            $this->info("NewOrderNotification dispatched successfully.");

            $this->info("Sending OrderStatusNotification (actualizacion_pedido_cliente)...");
            $notifiable->notifyNow(new \App\Notifications\OrderStatusNotification($order));
            $this->info("OrderStatusNotification dispatched successfully.");

        } catch (\Exception $e) {
            $this->error("Error sending notifications: " . $e->getMessage());
        }
    }
}
