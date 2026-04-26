<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Refactorizar orders: La plataforma ya no interviene en el cobro de pedidos.
     * Solo cobra suscripciones. Los pedidos se coordinan directamente entre partes vía WhatsApp.
     *
     * Cambios:
     * - Cambiar default de status a 'awaiting_cook_acceptance'
     * - Hacer payment_method nullable
     * - Hacer payment_status nullable
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL: usar ALTER TABLE con ENUM
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
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
            ) DEFAULT 'awaiting_cook_acceptance'");

            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('mercadopago', 'cash', 'transfer') NULL DEFAULT NULL");

            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'approved', 'rejected') NULL DEFAULT NULL");
        }
        // SQLite: no soporta ALTER COLUMN, pero como usamos :memory: con migrate:fresh
        // las columnas se crean desde la migración original. Simplemente no hacemos nada
        // ya que el código PHP ahora no depende de estos defaults — los setea explícitamente.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
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
            ) DEFAULT 'pending_payment'");

            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('mercadopago', 'cash', 'transfer') NOT NULL DEFAULT 'mercadopago'");

            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }
};
