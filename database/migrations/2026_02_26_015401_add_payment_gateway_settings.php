<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'stripe_public_key',
                'value' => '',
                'group' => 'pagos',
                'label' => 'Stripe Public Key',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => '',
                'group' => 'pagos',
                'label' => 'Stripe Secret Key',
                'type' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mp_access_token',
                'value' => '',
                'group' => 'pagos',
                'label' => 'MercadoPago Access Token',
                'type' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mp_public_key',
                'value' => '',
                'group' => 'pagos',
                'label' => 'MercadoPago Public Key',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('settings')->insert($settings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'stripe_public_key',
            'stripe_secret_key',
            'mp_access_token',
            'mp_public_key',
        ])->delete();
    }
};
