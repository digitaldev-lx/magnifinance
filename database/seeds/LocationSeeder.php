<?php

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            [
                'name' => 'Lisbon, Portugal',
                'status' => 'active',
            ],
            [
                'name' => 'New York, USA',
                'status' => 'active',
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }

}
