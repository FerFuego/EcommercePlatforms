<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Cook;
use App\Models\SubscriptionPlan;
use App\Models\CookSubscription;
use App\Models\SubscriptionPayment;
use App\Services\MercadoPagoService;
use App\Services\SubscriptionService;
use Mockery;

class MercadoPagoSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected $mpServiceMock;
    protected $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed plans
        $this->artisan('db:seed', ['--class' => 'SubscriptionPlanSeeder']);

        // Mock MercadoPagoService
        $this->mpServiceMock = Mockery::mock(MercadoPagoService::class);
        $this->app->instance(MercadoPagoService::class, $this->mpServiceMock);

        // Resolve SubscriptionService with mocked MP service
        $this->subscriptionService = app(SubscriptionService::class);
    }

    public function test_initiate_subscription_creates_pending_record_and_returns_init_point()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id]);
        $plan = SubscriptionPlan::where('slug', 'premium')->first();
        $plan->update(['mp_plan_id' => 'MP-PLAN-123']);

        $this->mpServiceMock->shouldReceive('createSubscription')
            ->once()
            ->andReturn((object) [
                'id' => 'MP-SUB-456',
                'init_point' => 'https://mercadopago.com/init/456'
            ]);

        $result = $this->subscriptionService->initiateSubscription($cook, $plan);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('https://mercadopago.com/init/456', $result['init_point']);

        $this->assertDatabaseHas('cook_subscriptions', [
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'provider_subscription_id' => 'MP-SUB-456'
        ]);
    }

    public function test_activate_subscription_updates_status_to_active()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id]);
        $plan = SubscriptionPlan::where('slug', 'premium')->first();

        $subscription = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'provider_subscription_id' => 'MP-SUB-789'
        ]);

        $this->mpServiceMock->shouldReceive('getSubscription')
            ->once()
            ->with('MP-SUB-789')
            ->andReturn((object) [
                'id' => 'MP-SUB-789',
                'status' => 'authorized',
                'next_payment_date' => '2026-04-01T10:00:00.000Z'
            ]);

        $success = $this->subscriptionService->activateSubscription('MP-SUB-789');

        $this->assertTrue($success);
        $this->assertDatabaseHas('cook_subscriptions', [
            'id' => $subscription->id,
            'status' => 'active'
        ]);

        $cook->refresh();
        $this->assertEquals($subscription->id, $cook->current_subscription_id);
    }

    public function test_webhook_handles_authorized_payment_extension()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id]);
        $plan = SubscriptionPlan::where('slug', 'premium')->first();

        $subscription = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'provider_subscription_id' => 'MP-SUB-RECURRING',
            'current_period_end' => now()
        ]);

        $this->mpServiceMock->shouldReceive('getPayment')
            ->once()
            ->with('PAY-123')
            ->andReturn((object) [
                'id' => 'PAY-123',
                'status' => 'approved',
                'transaction_amount' => 1000,
                'currency_id' => 'ARS',
                'preapproval_id' => 'MP-SUB-RECURRING'
            ]);

        $payload = [
            'type' => 'subscription_authorized_payment',
            'data' => ['id' => 'PAY-123']
        ];

        $success = $this->subscriptionService->handleWebhook($payload);

        $this->assertTrue($success);
        $this->assertDatabaseHas('subscription_payments', [
            'provider_payment_id' => 'PAY-123',
            'amount' => 1000,
            'status' => 'approved'
        ]);

        $subscription->refresh();
        $this->assertTrue($subscription->current_period_end->isFuture());
    }

    public function test_middleware_blocks_access_to_unsubscribed_cooks()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id]);

        // No subscription
        $this->actingAs($user)
            ->get(route('cook.dishes.index'))
            ->assertRedirect(route('cook.subscription.index'))
            ->assertSessionHas('warning');

        // Expired/Cancelled subscription
        $plan = SubscriptionPlan::where('slug', 'basico-free')->first();
        CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'cancelled'
        ]);

        $this->actingAs($user)
            ->get(route('cook.dishes.index'))
            ->assertRedirect(route('cook.subscription.index'));

        // Active subscription
        $sub = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'active'
        ]);
        $cook->update(['current_subscription_id' => $sub->id]);

        $user->refresh();
        $cook->refresh();

        $this->actingAs($user)
            ->get(route('cook.dishes.index'))
            ->assertOk();
    }

    public function test_cancel_subscription_updates_mp_and_local_db()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create(['user_id' => $user->id]);
        $plan = SubscriptionPlan::where('slug', 'premium')->first();

        $subscription = CookSubscription::create([
            'cook_id' => $cook->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'provider' => 'mercadopago',
            'provider_subscription_id' => 'MP-SUB-CANCEL'
        ]);
        $cook->update(['current_subscription_id' => $subscription->id]);

        $this->mpServiceMock->shouldReceive('updateSubscription')
            ->once()
            ->with('MP-SUB-CANCEL', ['status' => 'cancelled'])
            ->andReturn((object) ['status' => 'cancelled']);

        $success = $this->subscriptionService->cancelSubscription($cook);

        $this->assertTrue($success);
        $this->assertDatabaseHas('cook_subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled'
        ]);
    }
}
