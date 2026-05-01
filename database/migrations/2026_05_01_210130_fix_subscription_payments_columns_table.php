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
        Schema::table('subscription_payments', function (Blueprint $table) {
            // Renombrar columnas si existen con los nombres viejos
            if (Schema::hasColumn('subscription_payments', 'plan_id')) {
                $table->renameColumn('plan_id', 'subscription_plan_id');
            }
            
            if (Schema::hasColumn('subscription_payments', 'provider')) {
                $table->renameColumn('provider', 'payment_gateway');
            }
            
            if (Schema::hasColumn('subscription_payments', 'provider_payment_id')) {
                $table->renameColumn('provider_payment_id', 'payment_id');
            }

            // Añadir preapproval_id si no existe
            if (!Schema::hasColumn('subscription_payments', 'preapproval_id')) {
                $table->string('preapproval_id')->nullable()->after('payment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_payments', 'subscription_plan_id')) {
                $table->renameColumn('subscription_plan_id', 'plan_id');
            }
            if (Schema::hasColumn('subscription_payments', 'payment_gateway')) {
                $table->renameColumn('payment_gateway', 'provider');
            }
            if (Schema::hasColumn('subscription_payments', 'payment_id')) {
                $table->renameColumn('payment_id', 'provider_payment_id');
            }
        });
    }
};
