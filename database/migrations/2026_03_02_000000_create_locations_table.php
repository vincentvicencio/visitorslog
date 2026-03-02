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
            $table->string('name');
        });

        DB::table('locations')->insert([
            ['name' => 'Facilities Centre - Front Door'],
            ['name' => 'Facilities Centre - Back Door'],
            ['name' => 'Summit'],
            ['name' => 'Centris - Front Door'],
            ['name' => 'Centris - Back Door'],
            ['name' => 'Mezzanine'],
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
