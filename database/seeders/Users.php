<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;



class Users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('registered_users')->insert([
        [
            'user_name'  => 6746,
            'first_name' => 'Vincent Joseph',
            'last_name'  => 'Vicencio',
            'password' => Hash::make('vice'),
            'user_type'  => 1,
            'location'   => json_encode(['1','2','3','4','5']),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_name'  => 4742,
            'first_name' => 'Beejay',
            'last_name'  => 'Icamina',
            'password'   => Hash::make('Magellan01!!'),
            'user_type'  => 2,
            'location'   => json_encode(['1']),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_name'  => 'andi.lim',
            'first_name' => 'andi',
            'last_name'  => 'lim',
            'password' => Hash::make('andilim'),
            'user_type'  => 3,
            'location'   => json_encode(['1']),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);
    }
}
