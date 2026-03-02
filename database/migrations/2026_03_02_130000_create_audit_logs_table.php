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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_number', 20)->nullable();
            $table->integer('record_id')->nullable();
            $table->string('module', 191);
            $table->string('sub_module', 191)->nullable();
            $table->string('action', 191);
            $table->text('previous_data')->nullable();
            $table->text('new_data')->nullable();
            $table->string('ip_address', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
