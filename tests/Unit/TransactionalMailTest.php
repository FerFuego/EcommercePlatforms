<?php

namespace Tests\Unit;

use App\Mail\CookApprovedMail;
use App\Mail\CookRejectedMail;
use App\Mail\DriverApprovedMail;
use App\Mail\DriverRejectedMail;
use App\Models\Cook;
use App\Models\DeliveryDriver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionalMailTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Cook Approved Mail
    // ──────────────────────────────────────────────

    /** @test */
    public function cook_approved_mail_has_correct_subject()
    {
        $cook = Cook::factory()->create();
        $mail = new CookApprovedMail($cook);

        $this->assertEquals('¡Bienvenido a Cocinarte! Tu cuenta fue aprobada 🎉', $mail->envelope()->subject);
    }

    /** @test */
    public function cook_approved_mail_uses_correct_view()
    {
        $cook = Cook::factory()->create();
        $mail = new CookApprovedMail($cook);

        $this->assertEquals('emails.cook-approved', $mail->content()->view);
    }

    /** @test */
    public function cook_approved_mail_renders_with_cook_data()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['name' => 'Chef Pepe']);
        $mail = new CookApprovedMail($cook);

        $rendered = $mail->render();

        $this->assertStringContainsString('Chef Pepe', $rendered);
        $this->assertStringContainsString('aprobada', $rendered);
    }

    // ──────────────────────────────────────────────
    // Cook Rejected Mail
    // ──────────────────────────────────────────────

    /** @test */
    public function cook_rejected_mail_has_correct_subject()
    {
        $cook = Cook::factory()->create();
        $mail = new CookRejectedMail($cook, 'Documentación incompleta');

        $this->assertEquals('Actualización sobre tu solicitud en Cocinarte', $mail->envelope()->subject);
    }

    /** @test */
    public function cook_rejected_mail_renders_with_rejection_reason()
    {
        $cook = Cook::factory()->create();
        $cook->user->update(['name' => 'María']);
        $mail = new CookRejectedMail($cook, 'Documentación incompleta');

        $rendered = $mail->render();

        $this->assertStringContainsString('María', $rendered);
        $this->assertStringContainsString('Documentación incompleta', $rendered);
    }

    // ──────────────────────────────────────────────
    // Driver Approved Mail
    // ──────────────────────────────────────────────

    /** @test */
    public function driver_approved_mail_has_correct_subject()
    {
        $user = User::factory()->create(['role' => 'delivery_driver']);
        $driver = DeliveryDriver::create([
            'user_id' => $user->id,
            'is_approved' => false,
            'vehicle_type' => 'motorcycle',
            'dni_number' => 'DNI' . rand(10000000, 99999999),
            'dni_photo' => 'test.jpg',
            'location_lat' => -34.6037,
            'location_lng' => -58.3816,
        ]);
        $mail = new DriverApprovedMail($driver);

        $this->assertEquals('¡Bienvenido a Cocinarte! Tu cuenta de repartidor fue aprobada 🎉', $mail->envelope()->subject);
    }

    /** @test */
    public function driver_approved_mail_renders_with_driver_data()
    {
        $user = User::factory()->create(['role' => 'delivery_driver', 'name' => 'Pedro Repartidor']);
        $driver = DeliveryDriver::create([
            'user_id' => $user->id,
            'is_approved' => false,
            'vehicle_type' => 'motorcycle',
            'dni_number' => 'DNI' . rand(10000000, 99999999),
            'dni_photo' => 'test.jpg',
            'location_lat' => -34.6037,
            'location_lng' => -58.3816,
        ]);
        $mail = new DriverApprovedMail($driver);

        $rendered = $mail->render();

        $this->assertStringContainsString('Pedro Repartidor', $rendered);
        $this->assertStringContainsString('aprobada', $rendered);
    }

    // ──────────────────────────────────────────────
    // Driver Rejected Mail
    // ──────────────────────────────────────────────

    /** @test */
    public function driver_rejected_mail_has_correct_subject()
    {
        $user = User::factory()->create(['role' => 'delivery_driver']);
        $driver = DeliveryDriver::create([
            'user_id' => $user->id,
            'is_approved' => false,
            'vehicle_type' => 'motorcycle',
            'dni_number' => 'DNI' . rand(10000000, 99999999),
            'dni_photo' => 'test.jpg',
            'location_lat' => -34.6037,
            'location_lng' => -58.3816,
        ]);
        $mail = new DriverRejectedMail($driver, 'Vehículo no apto');

        $this->assertEquals('Actualización sobre tu solicitud en Cocinarte', $mail->envelope()->subject);
    }

    /** @test */
    public function driver_rejected_mail_renders_with_rejection_reason()
    {
        $user = User::factory()->create(['role' => 'delivery_driver', 'name' => 'Juan Driver']);
        $driver = DeliveryDriver::create([
            'user_id' => $user->id,
            'is_approved' => false,
            'vehicle_type' => 'motorcycle',
            'dni_number' => 'DNI' . rand(10000000, 99999999),
            'dni_photo' => 'test.jpg',
            'location_lat' => -34.6037,
            'location_lng' => -58.3816,
        ]);
        $mail = new DriverRejectedMail($driver, 'Vehículo no apto');

        $rendered = $mail->render();

        $this->assertStringContainsString('Juan Driver', $rendered);
        $this->assertStringContainsString('Vehículo no apto', $rendered);
    }
}
