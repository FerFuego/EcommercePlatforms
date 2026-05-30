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
            'key'        => 'chatbot_enabled',
            'value'      => '1',
            'group'      => 'security',
            'label'      => 'Habilitar Chatbot (Asistente Virtual)',
            'type'       => 'text',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('settings')->where('key', 'chatbot_enabled')->delete();
    }
};
