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
}
