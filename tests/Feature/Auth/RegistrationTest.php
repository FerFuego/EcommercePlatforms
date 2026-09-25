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

    public function test_registration_screen_preselects_cook_role_when_param_provided(): void
    {
        $response = $this->get(route('register', ['role' => 'cook']));

        $response->assertStatus(200);
        $response->assertSee("role: 'cook'", false);
        $response->assertSee('Registro de Cocinero');
    }

    public function test_registration_screen_preselects_cook_role_with_spanish_alias(): void
    {
        $response = $this->get('/register?tipo=cocinero');

        $response->assertStatus(200);
        $response->assertSee("role: 'cook'", false);
        $response->assertSee('Registro de Cocinero');
    }

    public function test_registration_screen_preselects_delivery_driver_role(): void
    {
        $response = $this->get(route('register', ['role' => 'delivery_driver']));

        $response->assertStatus(200);
        $response->assertSee("role: 'delivery_driver'", false);
        $response->assertSee('Registro de Repartidor');
    }

    public function test_registration_screen_defaults_to_empty_role_when_no_param(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee("role: ''", false);
    }
}

