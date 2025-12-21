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
        Schema::create('delivery_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Personal Information
            $table->string('dni_number')->unique();
            $table->string('dni_photo'); // Storage path
            $table->string('profile_photo')->nullable();

            // Vehicle Information
            $table->enum('vehicle_type', ['bicycle', 'motorcycle', 'car']);
            $table->string('vehicle_plate')->nullable();
            $table->string('vehicle_photo')->nullable();

            // Coverage Area
            $table->decimal('location_lat', 10, 8);
            $table->decimal('location_lng', 11, 8);
            $table->integer('coverage_radius_km')->default(5);

            // Payment Information
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_type')->nullable(); // checking, savings
            $table->string('cbu_cvu')->nullable(); // Argentina specific

            // Status & Ratings
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_available')->default(false); // Online/Offline
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);

            // Statistics
            $table->integer('total_deliveries')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_drivers');
    }
};
