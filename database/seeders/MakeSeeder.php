<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Make;

class MakeSeeder extends Seeder
{
    public function run()
    {
        $makes = [
            ['name' => 'Audi',           'logo' => 'images/audi.webp'],
            ['name' => 'BMW',            'logo' => 'images/bmw.webp'],
            ['name' => 'Chevrolet',      'logo' => 'images/chevrolet.webp'],
            ['name' => 'Dodge',          'logo' => 'images/dodge.webp'],
            ['name' => 'Ford',           'logo' => 'images/ford.webp'],
            ['name' => 'GMC',            'logo' => 'images/gmc.webp'],
            ['name' => 'Honda',          'logo' => 'images/honda.webp'],
            ['name' => 'Mazda',          'logo' => 'images/mazda.webp'],
            ['name' => 'Mercedes-Benz',  'logo' => 'images/mercedes-benz.webp'],
            ['name' => 'Nissan',         'logo' => 'images/nissan.webp'],
            ['name' => 'Toyota',         'logo' => 'images/toyota.webp'],
            ['name' => 'Volkswagen',     'logo' => 'images/volkswagen.webp'],
        ];

        foreach ($makes as $make) {
            Make::create($make);
        }
    }
}
