<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteCookTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $cook;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->cook = Cook::factory()->create([
            'is_approved' => true,
            'active' => true,
        ]);
    }

    /** @test */
    public function user_can_add_cook_to_favorites()
    {
        $response = $this->actingAs($this->customer)
            ->post(route('favorites.toggle', $this->cook->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'added']);

        $this->assertTrue($this->customer->favoriteCooks()->where('cook_id', $this->cook->id)->exists());
    }

    /** @test */
    public function user_can_remove_cook_from_favorites()
    {
        $this->customer->favoriteCooks()->attach($this->cook->id);

        $response = $this->actingAs($this->customer)
            ->post(route('favorites.toggle', $this->cook->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'removed']);

        $this->assertFalse($this->customer->favoriteCooks()->where('cook_id', $this->cook->id)->exists());
    }

    /** @test */
    public function user_can_view_favorite_cooks_list()
    {
        $this->customer->favoriteCooks()->attach($this->cook->id);

        $response = $this->actingAs($this->customer)
            ->get(route('favorites.index'));

        $response->assertStatus(200);
        $response->assertSee($this->cook->user->name);
    }

    /** @test */
    public function guest_cannot_toggle_favorites()
    {
        $response = $this->post(route('favorites.toggle', $this->cook->id));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('favorite_cooks');
    }
}
