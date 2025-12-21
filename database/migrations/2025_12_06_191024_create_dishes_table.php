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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cook_id')->constrained('cooks')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('photo_url')->nullable();
            $table->integer('available_stock')->default(0); // Stock disponible hoy
            $table->boolean('is_active')->default(true);
            $table->json('available_days')->nullable(); // [1,2,3,4,5] = Lun-Vie
            $table->integer('preparation_time_minutes')->default(30);
            $table->enum('delivery_method', ['pickup', 'delivery', 'both'])->default('both');
            $table->json('diet_tags')->nullable(); // ['vegetarian', 'vegan', 'gluten-free']
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
