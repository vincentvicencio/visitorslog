<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class VisitorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('visitor_types')->insert([
            ['name' => 'Applicant'],
            ['name' => 'Trainee'],
            ['name' => 'OJT']
        ]);
    }
}
