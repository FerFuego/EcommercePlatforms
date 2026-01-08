<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPushToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_store_push_token()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/push-tokens', [
                'token' => 'test-token-123',
                'device_type' => 'web',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Token saved successfully']);

        $this->assertDatabaseHas('user_push_tokens', [
            'user_id' => $user->id,
            'token' => 'test-token-123',
            'device_type' => 'web',
        ]);
    }

    /** @test */
    public function guest_cannot_store_push_token()
    {
        $response = $this->postJson('/push-tokens', [
            'token' => 'test-token-123',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_delete_push_token()
    {
        $user = User::factory()->create();
        UserPushToken::create([
            'user_id' => $user->id,
            'token' => 'test-token-123',
        ]);

        $response = $this->actingAs($user)
            ->deleteJson('/push-tokens', [
                'token' => 'test-token-123',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Token deleted successfully']);

        $this->assertDatabaseMissing('user_push_tokens', [
            'token' => 'test-token-123',
        ]);
    }
}
