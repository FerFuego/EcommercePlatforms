<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cook;
use App\Models\DeliveryDriver;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Servir de forma segura la foto de DNI del cocinero o chofer solo a administradores.
     * Soporta archivos en el nuevo almacenamiento privado ('local') y archivos legados ('uploads').
     */
    public function showDni(string $type, int $id)
    {
        $dniPath = null;

        if ($type === 'driver') {
            $driver = DeliveryDriver::findOrFail($id);
            $dniPath = $driver->dni_photo;
        } elseif ($type === 'cook') {
            $cook = Cook::findOrFail($id);
            $dniPath = $cook->dni_photo;
        } else {
            abort(404);
        }

        if (!$dniPath) {
            abort(404, 'DNI no encontrado');
        }

        // 1. Verificar si existe en el almacenamiento privado local
        if (Storage::disk('local')->exists($dniPath)) {
            $fullPath = Storage::disk('local')->path($dniPath);
            return response()->file($fullPath, [
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            ]);
        }

        // 2. Compatibilidad con archivos legados en disco uploads
        if (Storage::disk('uploads')->exists($dniPath)) {
            $fullPath = Storage::disk('uploads')->path($dniPath);
            return response()->file($fullPath, [
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            ]);
        }

        abort(404, 'Archivo físico no encontrado');
    }
}
