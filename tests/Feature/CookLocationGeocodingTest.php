<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\User;
use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CookLocationGeocodingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('uploads');
    }

    public function test_cook_profile_creation_geocodes_address_when_lat_lng_are_missing()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '-32.6471',
                    'lon' => '-63.0347',
                    'display_name' => 'Bell Ville, Córdoba, Argentina'
                ]
            ], 200)
        ]);

        $user = User::factory()->create(['role' => 'cook', 'address' => 'Av. San Martín 100, Bell Ville']);

        $dniFile = UploadedFile::fake()->image('dni.jpg');
        $kitchenPhotos = [
            UploadedFile::fake()->image('k1.jpg'),
            UploadedFile::fake()->image('k2.jpg'),
            UploadedFile::fake()->image('k3.jpg'),
        ];

        $response = $this->actingAs($user)->post(route('cook.profile.store'), [
            'bio' => 'Cocinero apasionado con amplia experiencia en comida casera tradicional.',
            'dni_photo' => $dniFile,
            'kitchen_photos' => $kitchenPhotos,
            'address' => 'Av. San Martín 100, Bell Ville',
            'location_lat' => null,
            'location_lng' => null,
            'coverage_radius_km' => 10,
            'terms' => 'on',
        ]);

        $this->assertDatabaseHas('cooks', [
            'user_id' => $user->id,
            'location_lat' => -32.6471,
            'location_lng' => -63.0347,
        ]);
    }

    public function test_cook_profile_update_geocodes_new_address_when_address_changes()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '-31.4201',
                    'lon' => '-64.1888',
                    'display_name' => 'Córdoba Capital, Argentina'
                ]
            ], 200)
        ]);

        $user = User::factory()->create(['role' => 'cook', 'address' => 'Bell Ville 123']);
        $cook = Cook::factory()->create([
            'user_id' => $user->id,
            'location_lat' => -32.6471,
            'location_lng' => -63.0347,
        ]);

        $response = $this->actingAs($user)->put(route('cook.profile.update'), [
            'bio' => 'Cocinero apasionado con amplia experiencia en comida casera tradicional.',
            'address' => 'Av. Colón 500, Córdoba',
            'coverage_radius_km' => 10,
            'location_lat' => null,
            'location_lng' => null,
        ]);

        $cook->refresh();
        $this->assertEquals(-31.4201, (float) $cook->location_lat);
        $this->assertEquals(-64.1888, (float) $cook->location_lng);
    }

    public function test_catalog_renders_successfully_even_if_cook_has_null_coordinates()
    {
        $user = User::factory()->create(['name' => 'Cocinero Sin Coordenadas']);
        $cook = Cook::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'active' => true,
            'location_lat' => null,
            'location_lng' => null,
        ]);

        $response = $this->get(route('marketplace.catalog'));
        $response->assertStatus(200);
    }
}
