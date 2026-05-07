<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_cannot_change_role_via_mass_assignment()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $user->update([
            'role' => 'admin',
            'is_suspended' => true
        ]);

        $this->assertEquals('customer', $user->fresh()->role);
        $this->assertFalse((bool)$user->fresh()->is_suspended);
    }

    /** @test */
    public function cook_cannot_auto_approve_via_mass_assignment()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create([
            'user_id' => $user->id,
            'is_approved' => false
        ]);

        $cook->update([
            'is_approved' => true,
            'monthly_sales_accumulated' => 999999
        ]);

        $this->assertFalse((bool)$cook->fresh()->is_approved);
        $this->assertEquals(0, (float)$cook->fresh()->monthly_sales_accumulated);
    }

    /** @test */
    public function admin_cannot_suspend_another_admin()
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin1)
            ->post(route('admin.users.toggle-status', $admin2->id))
            ->assertSessionHas('error', 'No puedes suspender la cuenta de otro administrador');

        $this->assertFalse((bool)$admin2->fresh()->is_suspended);
    }
}
