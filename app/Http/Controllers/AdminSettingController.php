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
        ];

        $data = $request->only($allowedKeys);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Configuración actualizada correctamente.');
    }
}
