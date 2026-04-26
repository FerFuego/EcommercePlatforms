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
        Schema::table('cooks', function (Blueprint $table) {
            $table->foreignId('current_subscription_id')->nullable()->constrained('cook_subscriptions')->nullOnDelete();
            $table->decimal('monthly_sales_accumulated', 12, 2)->default(0);
            $table->integer('monthly_orders_accumulated')->default(0);
            $table->timestamp('sales_reset_at')->nullable();
            $table->boolean('is_selling_blocked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooks', function (Blueprint $table) {
            $table->dropForeign(['current_subscription_id']);
            $table->dropColumn([
                'current_subscription_id',
                'monthly_sales_accumulated',
                'monthly_orders_accumulated',
                'sales_reset_at',
                'is_selling_blocked'
            ]);
        });
    }
};
