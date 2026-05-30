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

        return redirect()->route('admin.settings.index')->with('success', 'Configuración actualizada correctamente.');
    }
}
