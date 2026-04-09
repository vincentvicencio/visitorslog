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
        Schema::table('registered_visitor_ids', function (Blueprint $table) {
            $table->renameColumn('visitor_type', 'visitor_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registered_visitor_ids', function (Blueprint $table) {
            $table->renameColumn('visitor_type_id', 'visitor_type');
        });
    }
};
