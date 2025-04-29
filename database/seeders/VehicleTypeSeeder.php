<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Sedans',            'icon' => 'images/car.webp'],
            ['name' => 'Hatchbacks',        'icon' => 'images/hatchback.webp'],
            ['name' => 'Pickup Trucks',     'icon' => 'images/pickup.webp'],
            ['name' => 'Motorcycles',       'icon' => 'images/motorcycle.webp'],
            ['name' => 'SUVs',              'icon' => 'images/suv.webp'],
            ['name' => 'Coupes',            'icon' => 'images/coupe.webp'],
            ['name' => 'VANs/Minivans',     'icon' => 'images/van.webp'],
            ['name' => 'Medium Duty Trucks','icon' => 'images/mediumtruck.webp'],
            ['name' => 'Boats',             'icon' => 'images/boat.webp'],
            ['name' => 'Wagons',            'icon' => 'images/wagon.webp'],
            ['name' => 'ATVs',              'icon' => 'images/atv.webp'],
            ['name' => 'Convertibles',      'icon' => 'images/convertible.webp'],
        ];

        foreach ($types as $type) {
            VehicleType::create($type);
        }
    }
}
