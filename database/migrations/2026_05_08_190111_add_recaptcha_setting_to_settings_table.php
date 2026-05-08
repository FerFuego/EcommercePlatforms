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
        \DB::table('settings')->insert([
            'key' => 'recaptcha_enabled',
            'value' => '0',
            'group' => 'security',
            'label' => 'Habilitar Google reCAPTCHA v3',
            'type' => 'text', // Or boolean if you have it, but view uses text/textarea
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('settings')->where('key', 'recaptcha_enabled')->delete();
    }
};
