<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{
    public function run()
    {
        $states = [
            ['name' => 'Alabama',        'abbreviation' => 'AL'],
            ['name' => 'Arkansas',       'abbreviation' => 'AR'],
            ['name' => 'Arizona',        'abbreviation' => 'AZ'],
            ['name' => 'California',     'abbreviation' => 'CA'],
            ['name' => 'Florida',        'abbreviation' => 'FL'],
            ['name' => 'Georgia',        'abbreviation' => 'GA'],
            ['name' => 'Illinois',       'abbreviation' => 'IL'],
            ['name' => 'Indiana',        'abbreviation' => 'IN'],
            ['name' => 'Kentucky',       'abbreviation' => 'KY'],
            ['name' => 'Louisiana',      'abbreviation' => 'LA'],
            ['name' => 'Massachusetts',  'abbreviation' => 'MA'],
            ['name' => 'Maryland',       'abbreviation' => 'MD'],
            ['name' => 'Michigan',       'abbreviation' => 'MI'],
            ['name' => 'Minnesota',      'abbreviation' => 'MN'],
            ['name' => 'Missouri',       'abbreviation' => 'MO'],
            ['name' => 'North Carolina', 'abbreviation' => 'NC'],
            ['name' => 'Nevada',         'abbreviation' => 'NV'],
            ['name' => 'New Hampshire',  'abbreviation' => 'NH'],
            ['name' => 'New Jersey',     'abbreviation' => 'NJ'],
            ['name' => 'New York',       'abbreviation' => 'NY'],
            ['name' => 'Ohio',           'abbreviation' => 'OH'],
            ['name' => 'Oklahoma',       'abbreviation' => 'OK'],
            ['name' => 'Oregon',         'abbreviation' => 'OR'],
            ['name' => 'Pennsylvania',   'abbreviation' => 'PA'],
            ['name' => 'South Carolina', 'abbreviation' => 'SC'],
            ['name' => 'Tennessee',      'abbreviation' => 'TN'],
            ['name' => 'Texas',          'abbreviation' => 'TX'],
            ['name' => 'Utah',           'abbreviation' => 'UT'],
            ['name' => 'Virginia',       'abbreviation' => 'VA'],
            ['name' => 'Washington',     'abbreviation' => 'WA'],
        ];

        foreach ($states as $state) {
            State::create($state);
        }
    }
}
