<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_login_page()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('orders.my'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_view_registration_page()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Register');
    }

    /** @test */
    public function user_can_register_as_customer()
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
            'phone' => '+54 3537 123456',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'customer',
        ]);
    }

    /** @test */
    public function user_can_register_as_cook()
    {
        $response = $this->post(route('register'), [
            'name' => 'Chef Mario',
            'email' => 'mario@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'cook',
            'phone' => '+54 3537 123456',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'mario@example.com',
            'role' => 'cook',
        ]);
    }

    /** @test */
    public function registration_requires_valid_email()
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_requires_password_confirmation()
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function authenticated_users_cannot_access_login_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function authenticated_users_cannot_access_register_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('register'));

        $response->assertRedirect(route('dashboard'));
    }
}
