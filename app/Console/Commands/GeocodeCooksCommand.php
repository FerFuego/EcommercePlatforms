<?php

namespace App\Console\Commands;

use App\Models\Cook;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class GeocodeCooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cooks:geocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode cooks that do not have valid latitude and longitude coordinates based on their registered address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cooks = Cook::with('user')
            ->where(function ($query) {
                $query->whereNull('location_lat')
                    ->orWhereNull('location_lng')
                    ->orWhere('location_lat', 0)
                    ->orWhere('location_lng', 0);
            })
            ->get();

        if ($cooks->isEmpty()) {
            $this->info('All cooks already have valid coordinates.');
            return 0;
        }

        $this->info("Found {$cooks->count()} cook(s) missing coordinates. Geocoding...");

        $updatedCount = 0;
        foreach ($cooks as $cook) {
            $address = $cook->user->address ?? null;
            if (empty($address)) {
                $this->warn("Cook ID {$cook->id} (User: {$cook->user->name}) has no address configured.");
                continue;
            }

            $coords = GeocodingService::geocodeAddress($address);
            if ($coords) {
                $cook->update([
                    'location_lat' => $coords['lat'],
                    'location_lng' => $coords['lng'],
                ]);
                $this->info("Geocoded Cook ID {$cook->id} ({$cook->user->name}) -> Lat: {$coords['lat']}, Lng: {$coords['lng']}");
                $updatedCount++;
            } else {
                $this->error("Could not geocode address for Cook ID {$cook->id}: '{$address}'");
            }
        }

        $this->info("Finished! Successfully updated {$updatedCount} cook(s).");
        return 0;
    }
}
