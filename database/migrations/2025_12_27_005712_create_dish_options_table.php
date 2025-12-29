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
        Schema::create('dish_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('dish_option_groups')->onDelete('cascade');
            $table->string('name'); // e.g., "Carne Salada"
            $table->decimal('additional_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_options');
    }
};
