<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class MercadoPagoTestController extends Controller
{
    protected $mpService;

    public function __construct(MercadoPagoService $mpService)
    {
        $this->mpService = $mpService;
    }

    public function testConnection(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se proporcionó un Access Token.'
            ]);
        }

        $result = $this->mpService->testToken($token);

        if ($result['status'] === 'success') {
            $envName = $result['environment'] === 'sandbox' ? 'Desarrollo (Sandbox)' : 'Producción';
            return response()->json([
                'status' => 'success',
                'message' => "Conexión exitosa. Las credenciales son de **{$envName}**.",
                'environment' => $result['environment']
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Error de conexión: ' . ($result['message'] ?? 'Desconocido')
        ]);
    }

    public function showLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) {
            return "No se encontró el archivo de logs.";
        }

        $lines = file($logPath);
        $relevantLines = array_filter($lines, function($line) {
            return str_contains($line, 'MercadoPago') || str_contains($line, 'ERROR');
        });

        $lastErrors = array_slice($relevantLines, -50);
        
        echo "<h1>Últimos errores de Mercado Pago</h1>";
        echo "<pre style='background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 8px; font-family: monospace;'>";
        echo implode("", array_reverse($lastErrors));
        echo "</pre>";
    }
}
