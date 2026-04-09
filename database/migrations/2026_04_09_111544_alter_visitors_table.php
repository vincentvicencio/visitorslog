<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->renameColumn('visitor_type', 'visitors_type_id');
            $table->renameColumn('visitor_id', 'visitors_ids_number');
            $table->renameColumn('id_type', 'id_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->renameColumn('visitors_type_id', 'visitor_type');
            $table->renameColumn('visitors_ids_number', 'visitor_id');
            $table->renameColumn('id_type_id', 'id_type');
        });
    }
};
