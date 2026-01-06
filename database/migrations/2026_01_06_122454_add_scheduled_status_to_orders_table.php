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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending_payment',
                'paid',
                'awaiting_cook_acceptance',
                'rejected_by_cook',
                'preparing',
                'ready_for_pickup',
                'assigned_to_delivery',
                'on_the_way',
                'delivered',
                'cancelled',
                'scheduled'
            ])->default('pending_payment')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending_payment',
                'paid',
                'awaiting_cook_acceptance',
                'rejected_by_cook',
                'preparing',
                'ready_for_pickup',
                'assigned_to_delivery',
                'on_the_way',
                'delivered',
                'cancelled'
            ])->default('pending_payment')->change();
        });
    }
};
