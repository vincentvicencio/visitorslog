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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string(column: 'phone_number');
            $table->integer('visitor_type');
            $table->integer('visitor_id');
            $table->integer('location');
            $table->string('image_path');
            $table->timestamp('time_in');
            $table->timestamp('time_out');
            $table->tinyinteger('status');
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
        Schema::dropIfExists('visitors');
    }
};
