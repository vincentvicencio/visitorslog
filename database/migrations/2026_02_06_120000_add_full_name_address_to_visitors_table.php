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
            if (!Schema::hasColumn('visitors', 'full_name')) {
                $table->string('full_name')->nullable()->after('user_name');
            }

            if (!Schema::hasColumn('visitors', 'address')) {
                $table->string('address')->nullable()->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            if (Schema::hasColumn('visitors', 'full_name')) {
                $table->dropColumn('full_name');
            }

            if (Schema::hasColumn('visitors', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
