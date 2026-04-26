<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\User;
use App\Mail\CookApprovedMail;
use App\Mail\CookRejectedMail;
use App\Mail\DriverApprovedMail;
use App\Mail\DriverRejectedMail;
use App\Models\DeliveryDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function approving_cook_sends_email()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $cook = Cook::factory()->create(['is_approved' => false, 'active' => false]);

        $this->actingAs($admin)->post(route('admin.cooks.approve', $cook->id));

        Mail::assertSent(CookApprovedMail::class, function ($mail) use ($cook) {
            return $mail->cook->id === $cook->id;
        });
    }

    /** @test */
    public function rejecting_cook_sends_email_with_reason()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $cook = Cook::factory()->create(['is_approved' => false]);

        $this->actingAs($admin)->post(route('admin.cooks.reject', $cook->id), [
            'rejection_reason' => 'Documentación incompleta',
        ]);

        Mail::assertSent(CookRejectedMail::class, function ($mail) {
            return $mail->rejectionReason === 'Documentación incompleta';
        });
    }

    /** @test */
    public function approving_driver_sends_email()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $driverUser = User::factory()->create(['role' => 'delivery_driver']);
        $driver = DeliveryDriver::create([
            'user_id' => $driverUser->id,
            'is_approved' => false,
            'vehicle_type' => 'motorcycle',
            'dni_number' => 'DNI' . rand(10000000, 99999999),
            'dni_photo' => 'test.jpg',
            'location_lat' => -34.6037,
            'location_lng' => -58.3816,
        ]);

        $this->actingAs($admin)->post(route('admin.drivers.approve', $driver->id));

        Mail::assertSent(DriverApprovedMail::class, function ($mail) use ($driver) {
            return $mail->driver->id === $driver->id;
        });
    }

    /** @test */
    public function rejecting_driver_sends_email_with_reason()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $driverUser = User::factory()->create(['role' => 'delivery_driver']);
        $driver = DeliveryDriver::create([
            'user_id' => $driverUser->id,
            'is_approved' => false,
            'vehicle_type' => 'motorcycle',
            'dni_number' => 'DNI' . rand(10000000, 99999999),
            'dni_photo' => 'test.jpg',
            'location_lat' => -34.6037,
            'location_lng' => -58.3816,
        ]);

        $this->actingAs($admin)->post(route('admin.drivers.reject', $driver->id), [
            'rejection_reason' => 'Vehículo no apto',
        ]);

        Mail::assertSent(DriverRejectedMail::class, function ($mail) {
            return $mail->rejectionReason === 'Vehículo no apto';
        });
    }
}
