<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'customer',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\WelcomeEmail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\NewUserAdminNotification::class, function ($mail) {
            return $mail->hasTo('info@cocinarte.app');
        });
    }

    public function test_cook_can_register_and_is_redirected_to_profile_create(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Anttonella Catalano',
            'email' => 'antocata@example.com',
            'phone' => '3541217439',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'cook',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('cook.profile.create'));

        // Follow redirection to cook profile create
        $responseCreateProfile = $this->get(route('cook.profile.create'));
        $responseCreateProfile->assertStatus(200);
        $responseCreateProfile->assertSee('Únete como Cocinero');
    }
}

