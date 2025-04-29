<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;
use App\Models\VehicleType;
use App\Models\Make;
use App\Models\State;
use App\Models\Location;
use App\Models\Vehicle;

class MasterFileSeeder extends Seeder
{
    public function run()
    {
        // Load the HTML from seed.txt (assuming you placed seed.txt in public/)
        $path = public_path('seed.txt');
        if (! file_exists($path)) {
            $this->command->error("File not found at $path");
            return;
        }

        $html = file_get_contents($path);

        // Extract & seed each data set
        $this->seedVehicleTypes($html);
        $this->seedMakes($html);
        $this->seedStates($html);
        $this->seedLocations($html);
        $this->seedVehicles($html);

        $this->command->info('MasterFileSeeder: Completed extracting data from seed.txt!');
    }

    protected function seedVehicleTypes(string $html)
    {
        /**
         * We're looking for something like:
         *
         * <div id="type" class="filters-block mt-2">
         *   ...
         *   <ul class="list p-0 m-0">
         *     <li id="type-0">
         *       <label class="label">
         *         <input ... value="atv">ATVs
         *       </label>
         *     </li>
         *     ...
         *   </ul>
         * </div>
         */

        // 1) Capture the <ul class="list p-0 m-0">...</ul> block inside <div id="type"...>
        //    We'll do a single pass: look for <div id="type" ... <ul class="list p-0 m-0"> (group) </ul>
        if (! preg_match(
            '/<div\s+id="type"[^>]*>.*?<ul\s+class="list p-0 m-0">(.*?)<\/ul>/s',
            $html,
            $block
        )) {
            $this->command->warn("No <ul class='list p-0 m-0'> block found inside <div id='type'>.");
            return;
        }

        // $block[1] now contains everything between <ul class="list p-0 m-0"> and </ul>
        $ulHtml = $block[1];

        // 2) Inside that <ul>, each <li> looks like:
        //    <li id="type-0">
        //      <label class="label">
        //        <input name="type[]" class="mr-2" type="checkbox" value="atv">ATVs
        //      </label>
        //    </li>
        // We want the 'value="atv"' and the text "ATVs".
        preg_match_all(
            '/<li[^>]*>\s*<label[^>]*>\s*<input[^>]*value="([^"]+)"[^>]*>(.*?)<\/label>/s',
            $ulHtml,
            $rows,
            PREG_SET_ORDER
        );

        foreach ($rows as $row) {
            // row[1] = e.g. "atv"
            // row[2] = e.g. "ATVs"
            $code = trim($row[1]);
            $displayName = trim(strip_tags($row[2])); // remove HTML tags, e.g. "ATVs"

            // Insert or update in your vehicle_types table.
            // If you also want to store the code, add a 'code' column, or ignore it if not needed.
            VehicleType::updateOrCreate(
                ['name' => $displayName],  // e.g. "ATVs"
                ['icon' => null]           // or set icons if needed
            );
        }

        $this->command->info("Vehicle types seeded from the <div id='type'> snippet.");
    }


    /**
     * Extract makes from the “Search by Make” block.
     */
    protected function seedMakes(string $html)
    {
        // 1) Match the entire block that starts with <h4>Makes</h4> 
        if (! preg_match(
            '/<div\s+id="make"[^>]*>.*?<ul\s+class="list p-0 m-0">(.*?)<\/ul>/s',
            $html,
            $block
        )) {
            $this->command->warn("No Makes block found.");
            return;
        }
        // $block[1] now contains everything between <ul class="list p-0 m-0"> and </ul>
        $ulHtml = $block[1];

        // 2) Inside that <ul>, each <li> looks like:
        //    <li id="type-0">
        //      <label class="label">
        //        <input name="type[]" class="mr-2" type="checkbox" value="atv">ATVs
        //      </label>
        //    </li>
        // We want the 'value="atv"' and the text "ATVs".
        preg_match_all(
            '/<li[^>]*>\s*<label[^>]*>\s*<input[^>]*value="([^"]+)"[^>]*>(.*?)<\/label>/s',
            $ulHtml,
            $rows,
            PREG_SET_ORDER
        );


        foreach ($rows as $row) {
            // row[1] = e.g. "atv"
            // row[2] = e.g. "ATVs"
            $logo = trim($row[1]);
            $name = trim(strip_tags($row[2])); // remove HTML tags, e.g. "ATVs"
            
            // Insert or update in your vehicle_types table.
            // If you also want to store the code, add a 'code' column, or ignore it if not needed.
            Make::updateOrCreate(
                ['name' => $name],
                ['logo' => $logo]
            );
        }

        $this->command->info("Vehicle types seeded from the <div id='type'> snippet.");
    }



    /**
     * Extract states from the "Search by State" block, e.g.:
     *   <h4 class="text-black-50">States</h4>
     *   <div class="row">
     *     <div class="col-sm-4">
     *       <a class="d-inline-block my-1 mb-lg-2 alabama">Alabama</a>
     *     </div>
     */
    protected function seedStates(string $html)
    {
        // Get the country instance for the United States
        $us = country('US');
        // Loop through all subdivisions (US states)
        foreach ($us->getDivisions() as $stateCode => $state) {
            // echo $state->name . ' (' . $state->abbr . ')<br>';

            State::updateOrCreate(
                 ['name' => $state["name"]],
                 ['abbreviation' => $stateCode]
            );
        }

        $this->command->info("States seeded from rinvex/countries package");
    }

    /**
     * Extract locations from the "Search by Location" block, e.g.:
     *   <h4 class="text-black-50">Locations</h4>
     *   <div class="row">
     *     <div class="col-sm-4">
     *       <a class="d-inline-block my-1 mb-lg-2 al-eight_mile">AL - Eight Mile</a>
     *     </div>
     */
    protected function seedLocations(string $html)
    {
        if (! preg_match(
            '/<div\s+id="location"[^>]*>.*?<ul\s+class="list p-0 m-0">(.*?)<\/ul>/s',
            $html,
            $block
        )) {
            $this->command->warn("No Locations block found.");
            return;
        }
        // $block[1] now contains everything between <ul class="list p-0 m-0"> and </ul>
        $ulHtml = $block[1];

        // We want the 'value="atv"' and the text "ATVs".
        preg_match_all(
            '/<li[^>]*>\s*<label[^>]*>\s*<input[^>]*value="([^"]+)"[^>]*>(.*?)<\/label>/s',
            $ulHtml,
            $rows,
            PREG_SET_ORDER
        );

        // dd($rows);
        // $locs = $matches[1] ?? [];

        foreach ($rows as $row) {
            // dd(explode(", ", $row[2])[1]);
            Location::updateOrCreate(
                ['name' => $row[2]],
                ['state_id' => State::where('abbreviation', explode(", ", $row[2])[1])->first()?->id] // or parse "AL" => "Alabama" => find ID if you want
            );
        }


        $this->command->info("Locations are seeded from the <div id='location'> snippet.");
    }

    /**
     * Extract vehicles from blocks marked by <!-- 1 -->, <!-- 2 -->, etc.
     * For each chunk, parse year, make, model, VIN partial, lot number,
     * damage, odometer, auction date, location, images, etc. Then link them.
     */
    protected function seedVehicles(string $html)
    {
        // Split on <!-- 1 -->, <!-- 2 -->, etc.
        $chunks = preg_split('/<!--\s*\d+\s*-->/', $html);
        foreach ($chunks as $index => $chunk) {
            $chunk = trim($chunk);
            if (empty($chunk)) {
                continue;
            }

            // Initialize the data array with additional fields.
            $data = [
                'year'                   => null,
                'make'                   => null,
                'model'                  => null,
                'vin'                    => null,
                'lot_number'             => null,
                'damage'                 => null,
                'odometer'               => null,
                'auction_date'           => null,
                'location'               => null,
                'images'                 => [],
                // New fields:
                'odometer_status'        => null, // Expected to be "Actual" or "Not-Actual"
                'estimated_retail_value' => null, // Numeric value
                'current_bid'            => null, // Numeric value
                'keys'                   => null, // "Available" or "Not Available"
            ];

            // 1) Year + Make
            if (preg_match('/(\d{4})\s+([A-Za-z0-9]+)/', $chunk, $m)) {
                $data['year'] = (int)$m[1];
                $data['make'] = $m[2];
            }

            // 2) Lot Number
            if (preg_match('/Lot Number:\s*<\/span>\s*<span[^>]*>(\d+\*+)/', $chunk, $m)) {
                $data['lot_number'] = $m[1];
            }

            // 3) VIN partial
            if (preg_match('/VIN Number:.*?([A-Z0-9]{8,}\*+)/', $chunk, $m)) {
                $data['vin'] = $m[1];
            }

            // 4) Damage
            if (preg_match('/Damage:\s*<\/span>\s*<span[^>]*>(.*?)<\/span>/', $chunk, $m)) {
                $data['damage'] = trim(strip_tags($m[1]));
            }

            // 5) Odometer
            if (preg_match('/(\d[\d,]+)\s*mi\s*\((.*?)\)/', $chunk, $m)) {
                // dd($m[1], $m[2], $m[0]);
                $data['odometer'] = (int)str_replace(',', '', $m[1]);
                $data['odometer_status'] = $m[2];
            } else {
                $data['odometer_status'] = 'Actual';
            }

            // 5a) Odometer Status (new field)
            // Look for text like "Odometer Status: </span><span ...>Actual" or "Not-Actual"
            // if (preg_match('/Odometer Status:\s*<\/span>\s*<span[^>]*>(Actual|Not-Actual)/i', $chunk, $m)) {
            //     $data['odometer_status'] = trim($m[1]);
            // } else {
            //     // Default value if not found:
            //     $data['odometer_status'] = 'Actual';
            // }

            // 6) Auction Date
            if (preg_match('/Sale Date:\s*<\/span>\s*<span[^>]*>(\d{2}\/\d{2}\/\d{4})<\/span>/', $chunk, $m)) {
                [$mm, $dd, $yyyy] = explode('/', $m[1]);
                $data['auction_date'] = "$yyyy-$mm-$dd";
            }

            // 7) Images
            if (preg_match_all('/<img\s+[^>]*src="([^"]+)"[^>]*>/', $chunk, $m)) {
                $data['images'] = array_values(array_unique($m[1]));
            }

            // 8) Model
            if (preg_match('/(\d{4})\s+([A-Za-z0-9]+)\s+(.*?)<\/a>/', $chunk, $mm)) {
                $data['model'] = trim(strip_tags($mm[3]));
            }

            // 9) Location
            // Use an alternate preg_split to separate the location block if needed.
            $chunks_2 = preg_split(
                '/(?:<!--\s*\d+\s*-->|(?=<div\s+class="col-12\s+col-lg-6\s+col-xl-4\s+px-lg-2\s+pl-xl-0">))/',
                $chunk,
                -1,
                PREG_SPLIT_NO_EMPTY
            );
            if (count($chunks_2) >= 2) {
                $data['location'] = $this->extractLocationFromHtml($chunks_2[1]) ?? null;
            } else {
                $data['location'] = null;
            }

            // 10) Estimated Retail Value (new field)
            // Example pattern: "Estimated Retail Value:</span><span ...>$5,000.00</span>"
            if (preg_match('/Actual Cash Value:\s*<\/span>\s*<span[^>]*>\s*\$?([\d,]+(?:\.\d{2})?)\s*<\/span>/i', $chunk, $m)) {
                $data['estimated_retail_value'] = (float)str_replace([',', '$'], '', $m[1]);
            }

            // 11) Current Bid (new field)
            // Example pattern: "Current Bid:</span><span ...>$1,200.00</span>"
            if (preg_match('/Current Bid\s*<\/span>\s*<span[^>]*>\s*\$?([\d,]+(?:\.\d{2})?)\s*<\/span>/i', $chunk, $m)) {
                $data['current_bid'] = (float)str_replace([',', '$'], '', $m[1]);
            }

            // 12) Keys (new field)
            // Example pattern: "Keys:</span><span ...>(Available|Not Available)</span>"
            if (preg_match('/Keys\s*<\/span>\s*<span[^>]*>(Available|Not\s*Available)/i', $chunk, $m)) {
                $data['keys'] = trim($m[1]);
            } else {
                // Default if not found:
                $data['keys'] = 'Available';
            }

            // Link to seeded tables
            // a) Make
            $makeRow = null;
            if (!empty($data['make'])) {
                $makeRow = Make::where('name', $data['make'])->first();
            }

            // b) VehicleType (naive guess from model name)
            $vehicleType = null;
            if (!empty($data['model'])) {
                $lowModel = strtolower($data['model']);
                
                if (str_contains($lowModel, 'mdx') || str_contains($lowModel, 'durango')) {
                    $vehicleType = VehicleType::where('name', 'SUVs')->first();
                } elseif (str_contains($lowModel, 'altima') || str_contains($lowModel, 'accord') || str_contains($lowModel, 'camry')) {
                    $vehicleType = VehicleType::where('name', 'Sedans')->first();
                } elseif (str_contains($lowModel, 'freightliner') || str_contains($lowModel, 'motor home')) {
                    $vehicleType = VehicleType::where('name', 'Medium Duty Trucks')->first();
                } elseif (str_contains($lowModel, 'atv') || str_contains($lowModel, 'all-terrain')) {
                    $vehicleType = VehicleType::where('name', 'ATVs')->first();
                } elseif (str_contains($lowModel, 'boat') || str_contains($lowModel, 'yacht') || str_contains($lowModel, 'sailboat')) {
                    $vehicleType = VehicleType::where('name', 'Boats')->first();
                } elseif (str_contains($lowModel, 'bus')) {
                    $vehicleType = VehicleType::where('name', 'Busses')->first();
                } elseif (str_contains($lowModel, 'convertible')) {
                    $vehicleType = VehicleType::where('name', 'Convertibles')->first();
                } elseif (str_contains($lowModel, 'coupe')) {
                    $vehicleType = VehicleType::where('name', 'Coupes')->first();
                } elseif (str_contains($lowModel, 'dirt bike') || str_contains($lowModel, 'motocross')) {
                    $vehicleType = VehicleType::where('name', 'Dirt Bikes')->first();
                } elseif (str_contains($lowModel, 'forklift')) {
                    $vehicleType = VehicleType::where('name', 'Forklifts')->first();
                } elseif (str_contains($lowModel, 'hatchback')) {
                    $vehicleType = VehicleType::where('name', 'Hatchbacks')->first();
                } elseif (str_contains($lowModel, 'excavator') || str_contains($lowModel, 'bulldozer') || str_contains($lowModel, 'industrial')) {
                    $vehicleType = VehicleType::where('name', 'Industrial Equipment')->first();
                } elseif (str_contains($lowModel, 'jetski') || str_contains($lowModel, 'jet ski')) {
                    $vehicleType = VehicleType::where('name', 'Jet Skis')->first();
                } elseif (str_contains($lowModel, 'motorcycle') || (str_contains($lowModel, 'bike') && !str_contains($lowModel, 'dirt bike'))) {
                    $vehicleType = VehicleType::where('name', 'Motorcycles')->first();
                } elseif (str_contains($lowModel, 'pickup')) {
                    $vehicleType = VehicleType::where('name', 'Pickup Trucks')->first();
                } elseif (str_contains($lowModel, 'rv') || str_contains($lowModel, 'recreational')) {
                    $vehicleType = VehicleType::where('name', 'Recreational Vehicles')->first();
                } elseif (str_contains($lowModel, 'snowmobile')) {
                    $vehicleType = VehicleType::where('name', 'Snowmobiles')->first();
                } elseif (str_contains($lowModel, 'trailer')) {
                    $vehicleType = VehicleType::where('name', 'Trailers')->first();
                } elseif (str_contains($lowModel, 'semi') || str_contains($lowModel, 'tractor') || str_contains($lowModel, 'heavy duty')) {
                    $vehicleType = VehicleType::where('name', 'Heavy Duty Trucks')->first();
                } elseif (str_contains($lowModel, 'van') || str_contains($lowModel, 'minivan')) {
                    $vehicleType = VehicleType::where('name', 'VANs/Minivans')->first();
                } elseif (str_contains($lowModel, 'wagon')) {
                    $vehicleType = VehicleType::where('name', 'Wagons')->first();
                } else {
                    $vehicleType = VehicleType::where('name', 'Other')->first();
                }
            }

            // c) Location
            $locRow = null;
            if (!empty($data['location'])) {
                $locRow = Location::where('name', $data['location'])->first();
            }

            // Finally, insert the Vehicle along with the new fields.
            Vehicle::create([
                'vehicle_type_id'       => optional($vehicleType)->id,
                'make_id'               => optional($makeRow)->id,
                'state_id'              => $locRow ? $locRow->state_id : null,
                'location_id'           => optional($locRow)->id,
                'vin'                   => $data['vin'] ?: Str::random(10),
                'lot_number'            => $data['lot_number'] ?: null,
                'model'                 => $data['model'] ?: null,
                'year'                  => $data['year'] ?: null,
                'damage'                => $data['damage'] ?: null,
                'odometer'              => $data['odometer'] ?: null,
                'auction_date'          => $data['auction_date'] ?: null,
                'odometer_status'       => $data['odometer_status'],
                'estimated_retail_value'=> $data['estimated_retail_value'],
                'current_bid'           => $data['current_bid'],
                'keys'                  => $data['keys'],
                'description'           => 'Imported from seed.txt',
                'images'                => $data['images'],
            ]);
        }
    }

    private function extractLocationFromHtml(string $html)
    {
        // Create a new Crawler instance with your HTML string
        $crawler = new Crawler($html);



        // Use a CSS selector to find the <a> element within the container
        // Adjust the selector if your HTML structure varies
        $locationNode = $crawler->filter('.col-12.col-lg-6.col-xl-4.px-lg-2.pl-xl-0 .font-value a');
        $lotNumberNode = $crawler->filter('.col-12.col-lg-6.col-xl-4 .font-value .vehicle-model');

        // Check if the element was found and return its trimmed text
        if ($locationNode->count() > 0) {
            return trim($locationNode->text());
        }

        return null;
    }
}


