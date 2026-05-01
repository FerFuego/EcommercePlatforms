<?php

namespace Tests\Feature\Admin;

use App\Models\Cook;
use App\Models\CookSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\MercadoPagoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SubscriptionPaymentSyncTest extends TestCase
{
    use RefreshDatabase;

    protected $mpServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'SubscriptionPlanSeeder']);
        
        $this->mpServiceMock = Mockery::mock(MercadoPagoService::class);
        $this->app->instance(MercadoPagoService::class, $this->mpServiceMock);
    }

    public function test_admin_can_sync_payments_from_mercadopago()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Setup a cook and subscription that matches the "synced" payment
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id]);
        $plan = SubscriptionPlan::where('slug', 'premium')->first();
        
        CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'provider_subscription_id' => 'MP-SUB-123'
        ]);

        // Mock Search Response
        $this->mpServiceMock->shouldReceive('searchPayments')
            ->once()
            ->andReturn((object) [
                'results' => [
                    (object) [
                        'id' => 'PAY-999',
                        'status' => 'approved',
                        'date_created' => '2026-05-01T10:00:00.000-04:00'
                    ]
                ]
            ]);

        // Mock Get Payment details (called by handleAuthorizedPayment)
        $this->mpServiceMock->shouldReceive('getPayment')
            ->once()
            ->with('PAY-999')
            ->andReturn((object) [
                'id' => 'PAY-999',
                'status' => 'approved',
                'transaction_amount' => 10000,
                'currency_id' => 'ARS',
                'preapproval_id' => 'MP-SUB-123'
            ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.subscription-payments.sync'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscription_payments', [
            'payment_id' => 'PAY-999',
            'amount' => 10000,
            'status' => 'approved'
        ]);
    }
}
