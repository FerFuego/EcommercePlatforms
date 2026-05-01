<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\MercadoPagoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class MercadoPagoCredentialsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function only_admins_can_test_mercadopago_credentials()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)
            ->postJson(route('admin.settings.test-mp'), ['token' => 'APP_USR-123456-TOKEN']);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_token_presence()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test-mp'), ['token' => '']);

        $response->assertJson([
            'status' => 'error',
            'message' => 'No se proporcionó un Access Token.'
        ]);
    }

    /** @test */
    public function it_detects_production_environment()
    {
        $this->mock(MercadoPagoService::class, function (MockInterface $mock) {
            $mock->shouldReceive('testToken')
                ->with('APP_USR-123456-0101-ABC')
                ->andReturn([
                    'status' => 'success',
                    'environment' => 'production',
                    'user' => ['nickname' => 'PROD_USER']
                ]);
        });

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test-mp'), [
                'token' => 'APP_USR-123456-0101-ABC'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'environment' => 'production'
        ]);
        $response->assertJsonPath('message', 'Conexión exitosa. Las credenciales son de **Producción**.');
    }

    /** @test */
    public function it_detects_sandbox_environment()
    {
        $this->mock(MercadoPagoService::class, function (MockInterface $mock) {
            $mock->shouldReceive('testToken')
                ->with('TEST-654321-0101-XYZ')
                ->andReturn([
                    'status' => 'success',
                    'environment' => 'sandbox',
                    'user' => ['nickname' => 'SANDBOX_USER']
                ]);
        });

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test-mp'), [
                'token' => 'TEST-654321-0101-XYZ'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'environment' => 'sandbox'
        ]);
        $response->assertJsonPath('message', 'Conexión exitosa. Las credenciales son de **Desarrollo (Sandbox)**.');
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        $this->mock(MercadoPagoService::class, function (MockInterface $mock) {
            $mock->shouldReceive('testToken')
                ->andReturn([
                    'status' => 'error',
                    'message' => 'Invalid Token'
                ]);
        });

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test-mp'), [
                'token' => 'APP_USR-111111-ERROR'
            ]);

        $response->assertJson([
            'status' => 'error'
        ]);
        $this->assertStringContainsString('Error de conexión', $response->json('message'));
        $this->assertStringContainsString('Invalid Token', $response->json('message'));
    }
}
