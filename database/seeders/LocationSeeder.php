<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\State;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locationsData = [
            ['name' => 'AL - Eight Mile',    'state' => 'Alabama'],
            ['name' => 'AZ - Phoenix',       'state' => 'Arizona'],
            ['name' => 'CA - Sacramento',    'state' => 'California'],
            ['name' => 'CA - Los Angeles',   'state' => 'California'],
            ['name' => 'CA - San Diego',     'state' => 'California'],
            ['name' => 'CT - New Britain',   'state' => null],
            ['name' => 'FL - Jacksonville',  'state' => 'Florida'],
            ['name' => 'FL - Homestead',     'state' => 'Florida'],
            ['name' => 'FL - Miami',         'state' => 'Florida'],
            ['name' => 'GA - Austell',       'state' => 'Georgia'],
            ['name' => 'GA - Cartersville',  'state' => 'Georgia'],
            ['name' => 'GA - Loganville',    'state' => 'Georgia'],
            ['name' => 'IL - Elgin',         'state' => 'Illinois'],
            ['name' => 'IN - Indianapolis',  'state' => 'Indiana'],
            ['name' => 'KY - Louisville',    'state' => 'Kentucky'],
            ['name' => 'LA - New Orleans',   'state' => 'Louisiana'],
            ['name' => 'MA - North Billerica','state' => 'Massachusetts'],
            ['name' => 'MD - Waldorf',       'state' => 'Maryland'],
            ['name' => 'MI - Woodhaven',     'state' => 'Michigan'],
            ['name' => 'MN - Ham Lake',      'state' => 'Minnesota'],
            ['name' => 'NC - China Grove',   'state' => 'North Carolina'],
            ['name' => 'NC - Dunn',          'state' => 'North Carolina'],
            ['name' => 'NC - Lumberton',     'state' => 'North Carolina'],
            ['name' => 'NJ - Glassboro',     'state' => 'New Jersey'],
            ['name' => 'NV - Las Vegas',     'state' => 'Nevada'],
            ['name' => 'NY - Marlboro',      'state' => 'New York'],
            ['name' => 'OK - Oklahoma City', 'state' => 'Oklahoma'],
            ['name' => 'OR - Portland',      'state' => 'Oregon'],
            ['name' => 'SC - Gaston',        'state' => 'South Carolina'],
            ['name' => 'SC - Greer',         'state' => 'South Carolina'],
            ['name' => 'TN - Lebanon',       'state' => 'Tennessee'],
            ['name' => 'TN - Memphis',       'state' => 'Tennessee'],
            ['name' => 'TX - Grand Prairie', 'state' => 'Texas'],
            ['name' => 'TX - Haslet',        'state' => 'Texas'],
            ['name' => 'TX - Houston',       'state' => 'Texas'],
            ['name' => 'TX - Wilmer',        'state' => 'Texas'],
        ];

        foreach ($locationsData as $loc) {
            $state = $loc['state'] 
                ? State::where('name', $loc['state'])->first() 
                : null;
            Location::create([
                'state_id' => $state ? $state->id : null,
                'name' => $loc['name']
            ]);
        }
    }
}
