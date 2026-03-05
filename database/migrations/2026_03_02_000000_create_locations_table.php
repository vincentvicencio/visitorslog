<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->integer('location_id');
            $table->string('name');
        });

        DB::table('locations')->insert([
            ['location_id' => 1, 'name' => 'Facilities Centre - Front Door'],
            ['location_id' => 1, 'name' => 'Facilities Centre - Back Door'],
            ['location_id' => 2, 'name' => 'Summit'],
            ['location_id' => 3, 'name' => 'Centris - Front Door'],
            ['location_id' => 3, 'name' => 'Centris - Back Door'],
            ['location_id' => 4, 'name' => 'Mezzanine'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
