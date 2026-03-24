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
        Schema::create('emp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code')->nullable();
            $table->string('full_name')->nullable();
            $table->text('profile_pic')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('time')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('activity')->nullable();
            $table->string('created_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emp_logs');
    }
};
