<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuardLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('locations')->insert([
            ['location_id' => 1, 'name' => 'Facilities Centre - Front Door'],
            ['location_id' => 1, 'name' => 'Facilities Centre - Back Door'],
            ['location_id' => 2, 'name' => 'Summit'],
            ['location_id' => 3, 'name' => 'Centris - Front Door'],
            ['location_id' => 3, 'name' => 'Centris - Back Door'],
            ['location_id' => 4, 'name' => 'Mezzanine'],
        ]);
    }
}
