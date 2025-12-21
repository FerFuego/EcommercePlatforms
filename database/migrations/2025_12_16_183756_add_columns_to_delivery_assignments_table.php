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
        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->decimal('pickup_lat', 10, 8)->nullable()->after('status');
            $table->decimal('pickup_lng', 10, 8)->nullable()->after('pickup_lat');
            $table->decimal('delivery_lat', 10, 8)->nullable()->after('pickup_lng');
            $table->decimal('delivery_lng', 10, 8)->nullable()->after('delivery_lat');
            $table->decimal('delivery_fee', 8, 2)->default(0)->after('delivery_lng');
            $table->timestamp('picked_up_at')->nullable()->after('location_tracking');
            $table->timestamp('delivered_at')->nullable()->after('picked_up_at');
            $table->text('rejection_reason')->nullable()->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_lat',
                'pickup_lng',
                'delivery_lat',
                'delivery_lng',
                'delivery_fee',
                'picked_up_at',
                'delivered_at',
                'rejection_reason'
            ]);
        });
    }
};
