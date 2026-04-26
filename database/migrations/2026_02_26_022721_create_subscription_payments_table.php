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
        if (!Schema::hasTable('subscription_payments')) {
            Schema::create('subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cook_id')->constrained()->cascadeOnDelete();
                $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
                $table->string('payment_gateway')->default('mercadopago');
                $table->string('payment_id')->nullable(); // MP payment ID
                $table->string('preapproval_id')->nullable(); // MP preapproval ID
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('ARS');
                $table->string('status')->default('pending'); // pending, authorized, collected, failed
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
