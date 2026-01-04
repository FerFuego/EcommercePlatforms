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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cook_id')->constrained('cooks')->onDelete('cascade');

            // Estados del pedido según flujo definido
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
            ])->default('pending_payment');

            // Delivery
            $table->enum('delivery_type', ['pickup', 'delivery']);
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('delivery_lat', 10, 8)->nullable();
            $table->decimal('delivery_lng', 11, 8)->nullable();

            // Montos
            $table->decimal('subtotal', 10, 2); // Suma de ORDER_ITEMS
            $table->decimal('commission_amount', 10, 2)->default(0); // 10-15% para la plataforma
            $table->decimal('total_amount', 10, 2); // subtotal + delivery_fee

            // Pago
            $table->enum('payment_method', ['mercadopago', 'cash', 'transfer'])->default('mercadopago');
            $table->string('payment_id')->nullable(); // ID de transacción en MercadoPago
            $table->enum('payment_status', ['pending', 'approved', 'rejected'])->default('pending');

            // Extras
            $table->text('notes')->nullable(); // Notas del cliente
            $table->text('rejection_reason')->nullable();
            $table->timestamp('scheduled_time')->nullable(); // Hora programada para entrega/retiro
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
