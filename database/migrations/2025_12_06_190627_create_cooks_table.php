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
        Schema::create('cooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->unique();
            $table->text('bio')->nullable();
            $table->json('kitchen_photos')->nullable(); // Array de URLs de fotos de cocina
            $table->decimal('rating_avg', 3, 2)->default(0); // Promedio de calificaciones
            $table->integer('rating_count')->default(0); // Total de reviews
            $table->boolean('active')->default(true); // Si está activo para recibir pedidos
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->decimal('coverage_radius_km', 5, 2)->default(5); // Radio de cobertura en km
            $table->string('payout_method')->nullable(); // CBU, alias MP, etc.
            $table->json('payout_details')->nullable(); // Detalles de pago (encriptados)
            $table->string('dni_photo')->nullable(); // URL de foto de DNI
            $table->boolean('food_handler_declaration')->default(false); // Declaración jurada
            $table->boolean('is_approved')->default(false); // Aprobación por admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooks');
    }
};
