<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Cook;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    use RefreshDatabase;

    private WhatsAppService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WhatsAppService();
    }

    // ──────────────────────────────────────────────
    // Phone number formatting (Argentine numbers)
    // ──────────────────────────────────────────────

    /** @test */
    public function it_formats_full_international_argentine_number()
    {
        // Already in correct format: 549XXXXXXXXXX
        $url = $this->generateLinkWithPhone('5491112345678');
        $this->assertStringContains('wa.me/5491112345678', $url);
    }

    /** @test */
    public function it_formats_number_with_plus_and_spaces()
    {
        $url = $this->generateLinkWithPhone('+54 9 11 1234-5678');
        $this->assertStringContains('wa.me/5491112345678', $url);
    }

    /** @test */
    public function it_formats_national_number_with_zero_prefix()
    {
        // 011 15 1234-5678 → 549 11 12345678
        $url = $this->generateLinkWithPhone('01115 12345678');
        $this->assertStringContains('wa.me/549', $url);
    }

    /** @test */
    public function it_formats_ten_digit_local_number()
    {
        // 1112345678 (10 digits) → 5491112345678
        $url = $this->generateLinkWithPhone('1112345678');
        $this->assertStringContains('wa.me/5491112345678', $url);
    }

    /** @test */
    public function it_formats_number_without_nine()
    {
        // +54 11 1234-5678 (sin el 9) → 5491112345678
        $url = $this->generateLinkWithPhone('+54 11 12345678');
        $this->assertStringContains('wa.me/5491112345678', $url);
    }

    // ──────────────────────────────────────────────
    // Link generation
    // ──────────────────────────────────────────────

    /** @test */
    public function it_generates_order_link_for_customer()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => '+54 9 11 1234-5678']);
        $customer = User::factory()->create(['name' => 'María']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'total_amount' => 2500,
            'delivery_type' => 'pickup',
        ]);

        $url = $this->service->generateOrderLink($order);

        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://wa.me/', $url);
        $this->assertStringContains('text=', $url);
    }

    /** @test */
    public function it_includes_order_details_in_message()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => '5491112345678', 'name' => 'Carlos']);
        $customer = User::factory()->create(['name' => 'Ana']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
            'total_amount' => 3500,
            'delivery_type' => 'delivery',
            'delivery_address' => 'Calle Falsa 123',
            'notes' => 'Sin cebolla',
        ]);

        $url = $this->service->generateOrderLink($order);
        $decodedUrl = urldecode($url);

        $this->assertStringContains('Nuevo Pedido de Cocinarte', $decodedUrl);
        $this->assertStringContains('Carlos', $decodedUrl);
        $this->assertStringContains('Ana', $decodedUrl);
        $this->assertStringContains('Total', $decodedUrl);
        $this->assertStringContains('Delivery', $decodedUrl);
        $this->assertStringContains('Calle Falsa 123', $decodedUrl);
        $this->assertStringContains('Sin cebolla', $decodedUrl);
    }

    /** @test */
    public function it_generates_customer_link_for_cook()
    {
        $cook = Cook::factory()->create();
        $customer = User::factory()->create([
            'phone' => '5491198765432',
            'name' => 'Pedro',
        ]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'cook_id' => $cook->id,
        ]);

        $url = $this->service->generateCustomerLink($order);

        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://wa.me/5491198765432', $url);
    }

    /** @test */
    public function it_returns_null_when_cook_has_no_phone()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => null]);

        $order = Order::factory()->create([
            'cook_id' => $cook->id,
        ]);

        $url = $this->service->generateOrderLink($order);

        $this->assertNull($url);
    }

    /** @test */
    public function it_returns_null_when_customer_has_no_phone()
    {
        $customer = User::factory()->create(['phone' => null]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $url = $this->service->generateCustomerLink($order);

        $this->assertNull($url);
    }

    /** @test */
    public function it_shows_pickup_when_delivery_type_is_pickup()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => '5491112345678']);

        $order = Order::factory()->create([
            'cook_id' => $cook->id,
            'delivery_type' => 'pickup',
        ]);

        $url = $this->service->generateOrderLink($order);
        $decoded = urldecode($url);

        $this->assertStringContains('Retiro en cocina', $decoded);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    private function generateLinkWithPhone(string $phone): ?string
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['phone' => $phone]);

        $order = Order::factory()->create([
            'cook_id' => $cook->id,
        ]);

        return $this->service->generateOrderLink($order);
    }

    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertStringContainsString($needle, $haystack);
    }
}
