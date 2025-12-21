<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cook;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Panel de Administración');
    }

    /** @test */
    public function admin_can_view_pending_cooks()
    {
        Cook::factory()->count(3)->create(['is_approved' => false]);
        Cook::factory()->count(2)->create(['is_approved' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.cooks.index'));

        $response->assertStatus(200);
        // Should show pending cooks by default
    }

    /** @test */
    public function admin_can_approve_cook()
    {
        $cook = Cook::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($this->admin)->post(route('admin.cooks.approve', $cook));

        $response->assertRedirect();
        $this->assertTrue($cook->fresh()->is_approved);
    }

    /** @test */
    public function admin_can_reject_cook()
    {
        $cook = Cook::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($this->admin)->post(route('admin.cooks.reject', $cook), [
            'rejection_reason' => 'No cumple requisitos',
        ]);

        $response->assertRedirect();
        // Cook should be deleted or marked as rejected
        $this->assertDatabaseMissing('cooks', ['id' => $cook->id]);
    }

    /** @test */
    public function admin_can_view_all_orders()
    {
        Order::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_filter_orders_by_status()
    {
        Order::factory()->count(2)->create(['status' => Order::STATUS_AWAITING_COOK]);
        Order::factory()->count(3)->create(['status' => Order::STATUS_DELIVERED]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', ['status' => 'delivered']));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_statistics()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.statistics'));

        $response->assertStatus(200);
        $response->assertSee('Estadísticas');
    }

    /** @test */
    public function non_admin_cannot_access_admin_dashboard()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_approve_cooks()
    {
        $cook = Cook::factory()->create(['is_approved' => false]);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->post(route('admin.cooks.approve', $cook));

        $response->assertStatus(403);
        $this->assertFalse($cook->fresh()->is_approved);
    }

    /** @test */
    public function guest_cannot_access_admin_routes()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
