<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IDTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idTypes = [
            ['id_type_name' => 'Driver\'s License',
            'created_by'    => 1,
            'created_at'    => now()
            ],
            ['id_type_name' => 'Passport',
            'created_by'    => 1,
            'created_at'    => now()
            ],
            ['id_type_name' => 'National ID', 
            'created_by'    => 1, 
            'created_at'    => now()
            ],
            ['id_type_name' => 'Company ID', 
            'created_by'    => 1, 
            'created_at'    => now()
            ],
            ['id_type_name' => 'Student ID', 
            'created_by'    => 1, 
            'created_at'    => now()
            ],
             ['id_type_name' => 'Voter\'s ID', 
             'created_by'    => 1, 
             'created_at'    => now()
             ]
        ];

        foreach ($idTypes as $type) {
            \App\Models\ValidIdType::create($type);
        }
    }
}
