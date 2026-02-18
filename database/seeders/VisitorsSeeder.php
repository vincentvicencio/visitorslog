<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    DB::table('visitors')->insert([
        [
            // Varchars (Strings)
            'full_name'    => 'Charle B. Loreto',
            'first_name'   => 'Charle',
            'middle_name'  => 'B', 
            'last_name'    => 'Loreto',
            'phone_number' => '09123456789',
            'image_path'   => 'default.png',
            'created_by'   => 1,
            'updated_by'   => 1,

            // Integers (Numbers only - NO strings like 'Summit One')
            'visitor_type' => 1, 
            'visitor_id'   => 101,
            'location'     => 1,
            'status'       => 0, // tinyint

            // Timestamps
            'time_in'      => now(),
            'time_out'     => now()->addHours(2),
            'created_at'   => now(),
            'updated_at'   => now(), // Your DB says this cannot be null!
        ]
    ]);
}
}
