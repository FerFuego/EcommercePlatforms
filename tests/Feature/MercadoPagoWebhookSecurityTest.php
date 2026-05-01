<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MercadoPagoWebhookSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $testSecret = 'test_secret_key_12345';

    protected function setUp(): void
    {
        parent::setUp();
        // Configuramos un secreto de prueba fijo
        Config::set('services.mercadopago.webhook_secret', $this->testSecret);
    }

    /** @test */
    public function it_allows_webhook_with_valid_signature()
    {
        $payload = [
            'action' => 'payment.created',
            'data' => ['id' => '123456789'],
            'type' => 'payment'
        ];

        $ts = time();
        $requestId = 'req_' . uniqid();
        $resourceId = '123456789';
        
        // Construir el manifiesto tal cual lo hace el middleware
        $manifest = "id:{$resourceId};request-id:{$requestId};ts:{$ts};";
        $v1 = hash_hmac('sha256', $manifest, $this->testSecret);
        
        $signatureHeader = "ts={$ts},v1={$v1}";

        $response = $this->withHeaders([
            'x-signature' => $signatureHeader,
            'x-request-id' => $requestId,
        ])->postJson('/api/mercadopago/webhook', $payload);

        // Debería ser 200 o el código que devuelva el controlador (no 401)
        $response->assertStatus(200);
    }

    /** @test */
    public function it_rejects_webhook_with_invalid_signature()
    {
        $payload = [
            'action' => 'payment.created',
            'data' => ['id' => '123456789'],
            'type' => 'payment'
        ];

        $response = $this->withHeaders([
            'x-signature' => 'ts=123,v1=invalid_hash',
            'x-request-id' => 'req_123',
        ])->postJson('/api/mercadopago/webhook', $payload);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Invalid signature']);
    }

    /** @test */
    public function it_rejects_webhook_missing_signature_header()
    {
        $payload = ['data' => ['id' => '123']];

        $response = $this->postJson('/api/mercadopago/webhook', $payload);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);
    }

    /** @test */
    public function it_validates_signature_for_preapproval_events()
    {
        // Algunos eventos tienen el id en el root, no en data.id
        $payload = [
            'id' => 'pre_987654321',
            'type' => 'subscription_preapproval',
            'action' => 'created'
        ];

        $ts = time();
        $requestId = 'req_sub_123';
        $resourceId = 'pre_987654321';
        
        $manifest = "id:{$resourceId};request-id:{$requestId};ts:{$ts};";
        $v1 = hash_hmac('sha256', $manifest, $this->testSecret);
        
        $signatureHeader = "ts={$ts},v1={$v1}";

        $response = $this->withHeaders([
            'x-signature' => $signatureHeader,
            'x-request-id' => $requestId,
        ])->postJson('/api/mercadopago/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'context' => 'subscription_lifecycle']);
    }
}
