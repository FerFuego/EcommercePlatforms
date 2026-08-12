<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Geocode an address string to ['lat' => float, 'lng' => float]
     *
     * @param string $address
     * @return array{lat: float, lng: float}|null
     */
    public static function geocodeAddress(string $address): ?array
    {
        $address = trim($address);
        if (empty($address)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'CocinarteApp/1.0'
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $address,
                'limit' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    return [
                        'lat' => (float) $data[0]['lat'],
                        'lng' => (float) $data[0]['lon'],
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Geocoding error for address "' . $address . '": ' . $e->getMessage());
        }

        return null;
    }
}
