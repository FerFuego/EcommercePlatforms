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
        if (!Schema::hasTable('cook_subscriptions')) {
            Schema::create('cook_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cook_id')->constrained()->onDelete('cascade');
                $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
                $table->string('provider')->nullable(); 
                $table->string('provider_subscription_id')->nullable(); 
                $table->string('provider_customer_id')->nullable(); 
                $table->string('status')->default('active'); 
                $table->timestamp('current_period_start')->nullable();
                $table->timestamp('current_period_end')->nullable();
                $table->boolean('cancel_at_period_end')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cook_subscriptions');
    }
};
