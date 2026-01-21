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
        Schema::create('registered_visitor_ids', function (Blueprint $table) {
            $table->id();
            $table->integer('visitor_type');
            $table->string('id_number');
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->timestamp(column: 'deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registered_visitor_ids');
    }
};
