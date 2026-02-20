<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegisteredId extends Seeder
{
    public function run(): void
    {
        DB::table('registered_visitor_ids')->insert([
        [
            'visitor_type' => 1,
            'id_number' => 1000,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'visitor_type' => 2,
            'id_number' => 2000,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'visitor_type' => 3,
            'id_number' => 3000,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);
    }
}
