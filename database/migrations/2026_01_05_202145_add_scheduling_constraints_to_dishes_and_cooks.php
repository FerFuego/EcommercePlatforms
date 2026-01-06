<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->boolean('is_schedulable')->default(true)->after('is_active');
        });

        Schema::table('cooks', function (Blueprint $table) {
            $table->integer('max_scheduled_portions_per_day')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->dropColumn('is_schedulable');
        });

        Schema::table('cooks', function (Blueprint $table) {
            $table->dropColumn('max_scheduled_portions_per_day');
        });
    }
};
