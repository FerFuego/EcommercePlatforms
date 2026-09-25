<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía un mensaje de WhatsApp según el driver configurado (meta u openwa).
     */
    public function sendMessage(string $to, string $message): bool
    {
        $driver = config('services.whatsapp.driver', 'meta');

        if ($driver === 'openwa') {
            return $this->sendOpenWAMessage($to, $message);
        }

        return $this->sendMetaMessage($to, $message);
    }

    /**
     * Envía una plantilla de WhatsApp según el driver configurado.
     */
    public function sendTemplateMessage(string $to, string $templateName, array $components = [], string $languageCode = 'es', array $buttons = []): bool
    {
        $driver = config('services.whatsapp.driver', 'meta');

        if ($driver === 'openwa') {
            $formattedText = $this->formatTemplateToText($templateName, $components, $buttons);
            return $this->sendOpenWAMessage($to, $formattedText);
        }

        return $this->sendMetaTemplateMessage($to, $templateName, $components, $languageCode, $buttons);
    }

    /**
     * Envía mensaje usando OpenWA Gateway (Pasarela Libre).
     */
    protected function sendOpenWAMessage(string $to, string $message): bool
    {
        $baseUrl = config('services.whatsapp.openwa.url', 'http://localhost:3000');
        $apiKey = config('services.whatsapp.openwa.api_key');

        if (!$baseUrl) {
            Log::warning('OpenWA not configured: missing OPENWA_BASE_URL');
            return false;
        }

        $formattedPhone = $this->formatPhoneOpenWA($to);
        $endpoint = rtrim($baseUrl, '/') . '/send-message';

        $payload = [
            'to' => $formattedPhone,
            'content' => $message,
            'message' => $message,
        ];

        $request = Http::timeout(10);
        if ($apiKey) {
            $request->withToken($apiKey);
        }

        $response = $request->post($endpoint, $payload);

        if ($response->successful()) {
            Log::info("OpenWA message sent to {$to}", [
                'phone' => $formattedPhone,
                'status' => $response->status(),
            ]);
            return true;
        }

        Log::error('OpenWA API error', [
            'status' => $response->status(),
            'body' => $response->body(),
            'to' => $to,
        ]);
        return false;
    }

    /**
     * Envía texto simple vía Meta Cloud API.
     */
    protected function sendMetaMessage(string $to, string $message): bool
    {
        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $apiVersion = config('services.whatsapp.api_version', 'v22.0');

        if (!$token || !$phoneNumberId) {
            Log::warning('WhatsApp Meta API not configured: missing token or phone_number_id');
            return false;
        }

        $url = "https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneApi($to),
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message,
            ],
        ];

        $response = Http::timeout(6)->withToken($token)->post($url, $payload);

        if ($response->successful()) {
            Log::info("Meta WhatsApp message sent to {$to}", [
                'message_id' => $response->json('messages.0.id'),
            ]);
            return true;
        }

        Log::error('Meta WhatsApp API error', [
            'status' => $response->status(),
            'body' => $response->body(),
            'to' => $to,
        ]);
        return false;
    }

    /**
     * Envía plantilla vía Meta Cloud API.
     */
    protected function sendMetaTemplateMessage(
        string $to,
        string $templateName,
        array $components = [],
        string $languageCode = 'es',
        array $buttons = []
    ): bool {
        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $apiVersion = config('services.whatsapp.api_version', 'v22.0');

        if (!$token || !$phoneNumberId) {
            Log::warning('WhatsApp Meta API not configured: missing token or phone_number_id');
            return false;
        }

        $url = "https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages";

        $formattedComponents = array_map(function ($value) {
            return [
                'type' => 'text',
                'text' => (string) $value,
            ];
        }, $components);

        $templateComponents = [];

        if (!empty($formattedComponents)) {
            $templateComponents[] = [
                'type' => 'body',
                'parameters' => $formattedComponents,
            ];
        }

        if (!empty($buttons)) {
            foreach ($buttons as $button) {
                if (isset($button['type']) && $button['type'] === 'button') {
                    $templateComponents[] = $button;
                } else {
                    $subType = $button['sub_type'] ?? $button['type'] ?? 'url';
                    $index = (string) ($button['index'] ?? '0');
                    $paramValue = $button['parameter'] ?? $button['text'] ?? $button['value'] ?? '';

                    $templateComponents[] = [
                        'type' => 'button',
                        'sub_type' => $subType,
                        'index' => $index,
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => (string) $paramValue,
                            ],
                        ],
                    ];
                }
            }
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhoneApi($to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
                'components' => $templateComponents,
            ],
        ];

        $response = Http::timeout(6)->withToken($token)->post($url, $payload);

        if ($response->successful()) {
            Log::info("Meta WhatsApp template '{$templateName}' sent to {$to}", [
                'message_id' => $response->json('messages.0.id'),
            ]);
            return true;
        }

        Log::error('Meta WhatsApp Template API error', [
            'status' => $response->status(),
            'body' => $response->body(),
            'to' => $to,
            'template' => $templateName,
        ]);
        return false;
    }

    /**
     * Convierte las plantillas de Meta a texto plano enriquecido para OpenWA.
     */
    protected function formatTemplateToText(string $templateName, array $components, array $buttons = []): string
    {
        if ($templateName === 'actualizacion_pedido_cliente_v1' || $templateName === 'actualizacion_pedido_cliente') {
            $orderId = $components[0] ?? '';
            $name = $components[1] ?? 'Cliente';
            $status = $components[2] ?? '';
            $url = $components[3] ?? ($buttons[0]['parameter'] ?? ($buttons[0]['parameters'][0]['text'] ?? ''));
            if ($url && !str_starts_with($url, 'http')) {
                $url = rtrim(config('app.url'), '/') . '/orders/' . $url;
            }
            if (!$url) {
                $url = route('orders.show', $orderId ?: 1);
            }
            return "🍱 *Actualización de tu pedido #{$orderId}*\n\nHola {$name},\nTu pedido tiene un nuevo estado.\n>> *{$status}*\n\n¡Gracias por elegirnos! 😍\n\n🔗 *Ver detalle:* {$url}";
        }

        if ($templateName === 'nuevo_pedido_cocinero_v1' || $templateName === 'nuevo_pedido_cocinero') {
            $orderId = $components[0] ?? '';
            $customerName = $components[1] ?? 'Cliente';
            $detailString = $components[2] ?? '';
            $totalAmount = $components[3] ?? '';
            $deliveryType = $components[4] ?? '';
            $url = $components[5] ?? route('cook.orders.index');
            return "🍲 *Pedido #{$orderId}*\n\nHola, recibiste un pedido de *{$customerName}*.\n\n📋 *Detalle del pedido:*\n{$detailString}\n\n💰 *Total:* \${$totalAmount}\n🛵 *Entrega:* {$deliveryType}\n\n¡Vamos a cocinar!! 😝\n\n👉 Ver en el panel: {$url}";
        }

        // Fallback genérico para cualquier otra plantilla
        return "📌 *Notificación de Cocinarte*\n\n" . implode("\n", array_filter($components));
    }

    /**
     * Formatea un número de teléfono para OpenWA (ej: 5493537675680).
     */
    public function formatPhoneOpenWA(string $phone): string
    {
        return $this->formatPhone($phone);
    }


    /**
     * Genera URL de WhatsApp para que el CLIENTE contacte al COCINERO sobre un pedido.
     * Usa wa.me deep links (gratis, sin API de WhatsApp Business).
     */
    public function generateOrderLink(Order $order): ?string
    {
        $order->loadMissing(['cook.user', 'customer', 'items.dish']);

        $cookPhone = $order->cook?->user?->phone;
        if (!$cookPhone) {
            return null;
        }

        $phone = $this->formatPhone($cookPhone);
        $message = $this->buildOrderMessage($order);

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Genera URL de WhatsApp para que el COCINERO contacte al CLIENTE.
     */
    public function generateCustomerLink(Order $order): ?string
    {
        $order->loadMissing(['cook.user', 'customer', 'items.dish']);

        $customerPhone = $order->customer?->phone;
        if (!$customerPhone) {
            return null;
        }

        $phone = $this->formatPhone($customerPhone);
        $message = $this->buildCookToCustomerMessage($order);

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Construye el mensaje pre-armado del CLIENTE al COCINERO.
     */
    private function buildOrderMessage(Order $order): string
    {
        $customerName = $order->customer->name ?? 'Cliente';
        $cookName = $order->cook->user->name ?? 'Cocinero';

        $lines = [];
        $lines[] = "🍲 *Nuevo Pedido de Cocinarte* #{$order->id}";
        $lines[] = "";
        $lines[] = "¡Hola {$cookName}! Soy {$customerName}, te hice un pedido por Cocinarte:";
        $lines[] = "";
        $lines[] = "📋 *Detalle del pedido:*";

        foreach ($order->items as $item) {
            $dishName = $item->dish->name ?? 'Plato';
            $itemTotal = number_format($item->total_price, 0, ',', '.');
            $lines[] = "• {$item->quantity}x {$dishName} — \${$itemTotal}";
        }

        $lines[] = "";
        $lines[] = "💰 *Total: \$" . number_format($order->total_amount, 0, ',', '.') . "*";

        // Tipo de entrega
        if ($order->delivery_type === 'delivery') {
            $lines[] = "🛵 *Entrega:* Delivery";
            if ($order->delivery_address) {
                $lines[] = "📍 *Dirección:* {$order->delivery_address}";
            }
            if ($order->delivery_fee > 0) {
                $lines[] = "🚚 *Costo envío:* \$" . number_format($order->delivery_fee, 0, ',', '.');
            }
        } else {
            $lines[] = "🏃 *Entrega:* Retiro en cocina";
        }

        // Programación
        if ($order->scheduled_time) {
            $lines[] = "📅 *Cuándo:* " . $order->scheduled_time->format('d/m/Y H:i');
        } else {
            $lines[] = "📅 *Cuándo:* Lo antes posible";
        }

        // Notas
        if ($order->notes) {
            $lines[] = "";
            $lines[] = "📝 *Notas:* {$order->notes}";
        }

        $lines[] = "";
        $lines[] = "¿Cómo coordinamos el pago? 😊";

        return implode("\n", $lines);
    }

    /**
     * Construye el mensaje pre-armado del COCINERO al CLIENTE.
     */
    private function buildCookToCustomerMessage(Order $order): string
    {
        $customerName = $order->customer->name ?? 'Cliente';
        $cookName = $order->cook->user->name ?? 'Cocinero';

        $lines = [];
        $lines[] = "🍲 *Cocinarte — Pedido #{$order->id}*";
        $lines[] = "";
        $lines[] = "¡Hola {$customerName}! Soy {$cookName} de Cocinarte.";
        $lines[] = "Te escribo por tu pedido #{$order->id}.";
        $lines[] = "";
        $lines[] = "💰 *Total: \$" . number_format($order->total_amount, 0, ',', '.') . "*";

        if ($order->delivery_type === 'delivery') {
            $lines[] = "🛵 Con entrega a: {$order->delivery_address}";
        } else {
            $lines[] = "🏃 Retiro en mi cocina";
        }

        return implode("\n", $lines);
    }

    /**
     * Formatea un número a E.164 para Meta Cloud API.
     * Meta requiere formato internacional SIN el 9 adicional argentino: 541112345678
     */
    private function formatPhoneApi(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Si ya empieza con 549, quitar el 9
        if (str_starts_with($digits, '549')) {
            return '54' . substr($digits, 3);
        }

        // Si empieza con 54 pero sin 9
        if (str_starts_with($digits, '54')) {
            return $digits;
        }

        // Si empieza con 0 (formato nacional)
        if (str_starts_with($digits, '0')) {
            $rest = substr($digits, 1);
            if (preg_match('/^(\d{2,4})15(\d{6,8})$/', $rest, $matches)) {
                return '54' . $matches[1] . $matches[2];
            }
            return '54' . $rest;
        }

        // Número local de 10 dígitos
        if (strlen($digits) === 10) {
            return '54' . $digits;
        }

        return $digits;
    }

    /**
     * Formatea un número de teléfono argentino para wa.me.
     *
     * wa.me requiere el formato internacional sin + ni espacios: 549XXXXXXXXXX
     *
     * Acepta formatos:
     *  - +54 9 11 1234-5678
     *  - 011 15 1234-5678
     *  - 11 1234-5678
     *  - 549111234567
     *  - Cualquier número internacional (intenta limpiar y devolver)
     */
    private function formatPhone(string $phone): string
    {
        // Eliminar todo excepto dígitos
        $digits = preg_replace('/\D/', '', $phone);

        // Si ya tiene formato internacional argentino completo (549...)
        if (str_starts_with($digits, '549') && strlen($digits) >= 12) {
            return $digits;
        }

        // Si empieza con 54 pero sin el 9 (formato +54 11... sin celular)
        if (str_starts_with($digits, '54') && !str_starts_with($digits, '549')) {
            $rest = substr($digits, 2);
            // Quitar el 15 si lo tiene al inicio del número local
            if (str_starts_with($rest, '15')) {
                $rest = substr($rest, 2);
            }
            return '549' . $rest;
        }

        // Si empieza con 0 (formato nacional: 011, 0351, etc.)
        if (str_starts_with($digits, '0')) {
            $rest = substr($digits, 1); // Quitar el 0
            // Quitar el 15 si lo tiene
            if (preg_match('/^(\d{2,4})15(\d{6,8})$/', $rest, $matches)) {
                return '549' . $matches[1] . $matches[2];
            }
            return '549' . $rest;
        }

        // Si tiene 10 dígitos (número local sin prefijo: 1112345678)
        if (strlen($digits) === 10) {
            return '549' . $digits;
        }

        // Fallback: devolver lo que haya, limpio
        return $digits;
    }
}
