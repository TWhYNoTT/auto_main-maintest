<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Make;
use App\Models\State;
use App\Models\Location;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seed.txt');
        if (! file_exists($path)) {
            $this->command->error("seed.txt not found at $path");
            return;
        }
        $html = file_get_contents($path);

        // Split on <!-- X --> to find each vehicle block
        $chunks = preg_split('/<!--\s*\d+\s*-->/', $html);
        foreach ($chunks as $chunk) {
            $trim = trim($chunk);
            if (empty($trim)) {
                continue;
            }

            // Parse fields
            $data = [
                'year'         => null,
                'make'         => null,
                'model'        => null,
                'vin'          => null,
                'lot_number'   => null,
                'location'     => null,
                'odometer'     => null,
                'damage'       => null,
                'auction_date' => null,
                'images'       => [],
            ];

            // Example regex patterns (naive approach!)
            if (preg_match('/(\d{4})\s+([A-Za-z0-9]+)/', $chunk, $m)) {
                $data['year'] = (int)$m[1];
                $data['make'] = $m[2];
            }
            if (preg_match('/Lot Number:\s*<\/span>\s*<span.*?>(\d+[\*]+)/', $chunk, $m)) {
                $data['lot_number'] = $m[1];
            }
            if (preg_match('/VIN Number:.*?([A-Z0-9]{8,}\*+)/', $chunk, $m)) {
                $data['vin'] = $m[1];
            }
            if (preg_match('/Damage:\s*<\/span>\s*<span.*?>(.*?)<\/span>/', $chunk, $m)) {
                $data['damage'] = strip_tags($m[1]);
            }
            if (preg_match('/(\d[\d,]+)\s*mi\s*\((.*?)\)/', $chunk, $m)) {
                $data['odometer'] = (int)str_replace(',', '', $m[1]);
            }
            if (preg_match('/Sale Date:\s*<\/span>\s*<span.*?>(\d{2}\/\d{2}\/\d{4})<\/span>/', $chunk, $m)) {
                [$mm,$dd,$yyyy] = explode('/', $m[1]);
                $data['auction_date'] = "$yyyy-$mm-$dd";
            }
            if (preg_match_all('/<img\s+[^>]*src="([^"]+)"[^>]*>/', $chunk, $m)) {
                $data['images'] = array_values(array_unique($m[1]));
            }
            // Location: e.g. "Florence, MS" => look for something like: "Location:.*Florence, MS"
            if (preg_match('/Location:\s*.*?<a[^>]*>(.*?)<\/a>/', $chunk, $m)) {
                $data['location'] = strip_tags($m[1]);
            }

            // Attempt to parse "model" from the line that has year + make + model
            // e.g. "2022 Acura MDX"
            if (preg_match('/(\d{4})\s+([A-Za-z0-9]+)\s+(.*?)<\/a>/', $chunk, $m)) {
                // m[3] might contain "MDX" or "Durango SXT" etc.
                $data['model'] = trim($m[3]);
            }

            // Attempt to guess vehicle_type from the model or from the snippet
            // For demonstration, a naive approach:
            $vehicleTypeName = 'Sedans';
            if (!empty($data['model'])) {
                $lowerModel = strtolower($data['model']);
                if (Str::contains($lowerModel, 'mdx') || Str::contains($lowerModel, 'durango')) {
                    $vehicleTypeName = 'SUVs';
                } elseif (Str::contains($lowerModel, 'motor home')) {
                    $vehicleTypeName = 'Medium Duty Trucks'; // or "Recreational Vehicles" if you added that
                } elseif (Str::contains($lowerModel, 'altima')) {
                    $vehicleTypeName = 'Sedans';
                }
            }

            // 1) Find or guess vehicle_type_id
            $type = VehicleType::where('name', $vehicleTypeName)->first();

            // 2) Find make_id
            // If $data['make'] is "Acura", we do Make::where('name','Acura')->first().
            $make = null;
            if (!empty($data['make'])) {
                $make = Make::where('name', $data['make'])->first();
            }

            // 3) Find location_id from $data['location']
            $loc = null;
            if (!empty($data['location'])) {
                $loc = Location::where('name', $data['location'])->first();
            }

            // 4) Insert into vehicles
            Vehicle::create([
                'vehicle_type_id' => $type ? $type->id : null,
                'make_id'         => $make ? $make->id : null,
                'state_id'        => $loc ? $loc->state_id : null,
                'location_id'     => $loc ? $loc->id : null,
                'vin'             => $data['vin'] ?: Str::random(10),
                'lot_number'      => $data['lot_number'] ?: null,
                'model'           => $data['model'] ?: null,
                'year'            => $data['year'] ?: null,
                'damage'          => $data['damage'] ?: null,
                'odometer'        => $data['odometer'] ?: null,
                'auction_date'    => $data['auction_date'] ?: null,
                'description'     => 'Imported from seed.txt',
                'images'          => $data['images'],
            ]);
        }

        $this->command->info("VehicleFileSeeder: Completed reading seed.txt and linking vehicles to IDs.");
    }
}
