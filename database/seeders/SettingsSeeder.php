<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Cocinarte',
                'group' => 'seo',
                'label' => 'Nombre del Sitio',
                'type' => 'text',
            ],
            [
                'key' => 'meta_title',
                'value' => 'Cocinarte - Comida Casera a Domicilio',
                'group' => 'seo',
                'label' => 'Meta Title (Título Global)',
                'type' => 'text',
            ],
            [
                'key' => 'meta_description',
                'value' => 'La mejor comida casera preparada por vecinos de tu zona. Delivery rápido y sabores auténticos.',
                'group' => 'seo',
                'label' => 'Meta Description',
                'type' => 'textarea',
            ],
            [
                'key' => 'commission_rate',
                'value' => '15',
                'group' => 'financial',
                'label' => 'Porcentaje de Comisión (%)',
                'type' => 'number',
            ],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
