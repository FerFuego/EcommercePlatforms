<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Asegurar que existan las configuraciones base de seguridad
        if (!Setting::where('key', 'recaptcha_enabled')->exists()) {
            Setting::create([
                'key'   => 'recaptcha_enabled',
                'value' => '0',
                'group' => 'security',
                'label' => 'Habilitar Google reCAPTCHA v3',
                'type'  => 'text',
            ]);
        }

        if (!Setting::where('key', 'chatbot_enabled')->exists()) {
            Setting::create([
                'key'   => 'chatbot_enabled',
                'value' => '1',
                'group' => 'security',
                'label' => 'Habilitar Chatbot (Asistente Virtual)',
                'type'  => 'text',
            ]);
        }

        // Group settings by their 'group' column for organized display
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Whitelist of allowed setting keys for security
        $allowedKeys = [
            'site_name',
            'meta_title',
            'meta_description',
            'commission_rate',
            'stripe_publishable_key',
            'stripe_secret_key',
            'mp_access_token',
            'mp_public_key',
            'recaptcha_enabled',
            'chatbot_enabled',
        ];

        $data = $request->only($allowedKeys);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'Configuraciones actualizadas exitosamente.');
    }

    /**
     * Purge all test data (orders, dishes, non-admin users).
     */
    public function purgeTestData(Request $request)
    {
        $request->validate([
            'confirm_text' => 'required|string|in:BORRAR,borrar,Borrar',
        ], [
            'confirm_text.in' => 'Debe escribir la palabra BORRAR para confirmar la eliminación de datos.',
        ]);

        try {
            \Illuminate\Support\Facades\Artisan::call('app:purge-test-data', ['--force' => true]);
            return redirect()->route('admin.settings.index')->with('success', '¡Datos de prueba eliminados exitosamente para el lanzamiento!');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error purging test data from admin panel: " . $e->getMessage());
            return redirect()->route('admin.settings.index')->with('error', 'Error al borrar datos de prueba: ' . $e->getMessage());
        }
    }
}
