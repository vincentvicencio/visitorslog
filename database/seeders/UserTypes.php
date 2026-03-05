<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserTypes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_types')->insert([
        [
            'name'       => 'Admin',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
        ],
        [
            'name'       => 'Receptionist',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
        ],
        [
            'name'       => 'Guard',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
        ]
    ]);

}
}
