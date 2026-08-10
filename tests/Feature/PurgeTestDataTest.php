<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;

class PurgeTestDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_command_purges_test_data_and_keeps_admin(): void
    {
        // 1. Crear admin y datos de prueba
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin', 'email' => 'admin@cocinarte.app'])->save();

        $customer = User::factory()->create();
        $customer->forceFill(['role' => 'customer'])->save();

        $cookUser = User::factory()->create();
        $cookUser->forceFill(['role' => 'cook'])->save();

        $cook = Cook::factory()->create(['user_id' => $cookUser->id]);
        $dish = Dish::factory()->create(['cook_id' => $cook->id]);

        // 2. Ejecutar comando Artisan
        $this->artisan('app:purge-test-data', ['--force' => true])
            ->assertExitCode(0);

        // 3. Verificar la preservación del admin
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);

        // 4. Verificar la eliminación de datos de prueba
        $this->assertDatabaseMissing('users', ['id' => $customer->id]);
        $this->assertDatabaseMissing('users', ['id' => $cookUser->id]);
        $this->assertDatabaseMissing('cooks', ['id' => $cook->id]);
        $this->assertDatabaseMissing('dishes', ['id' => $dish->id]);
    }

    public function test_admin_can_purge_test_data_via_web_settings(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $customer = User::factory()->create();
        $customer->forceFill(['role' => 'customer'])->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.settings.purge-test-data'), [
                'confirm_text' => 'BORRAR',
            ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseMissing('users', ['id' => $customer->id]);
    }
}
