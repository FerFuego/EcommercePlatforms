<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class VerifyMercadoPagoSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.mercadopago.webhook_secret');
        $signature = $request->header('x-signature');
        $requestId = $request->header('x-request-id');

        // Si no hay firma o secreto, rechazamos por seguridad
        if (!$signature || !$secret) {
            Log::warning('MercadoPago Webhook Rejected: Missing signature or secret.');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // La firma viene en formato: ts=...,v1=...
            $parts = [];
            foreach (explode(',', $signature) as $part) {
                $item = explode('=', $part);
                if (count($item) == 2) {
                    $parts[$item[0]] = $item[1];
                }
            }

            $ts = $parts['ts'] ?? '';
            $v1 = $parts['v1'] ?? '';

            // El ID del recurso puede estar en diferentes lugares según el tipo de evento
            $resourceId = $request->query('data_id') ?: $request->input('data.id');
            
            if (!$resourceId) {
                // Para algunos eventos de preapproval, el ID está en el root
                $resourceId = $request->input('id');
            }

            // Construir el manifiesto para la verificación
            $manifest = "id:{$resourceId};request-id:{$requestId};ts:{$ts};";

            // Generar HMAC-SHA256
            $computedSignature = hash_hmac('sha256', $manifest, $secret);

            if (!hash_equals($v1, $computedSignature)) {
                Log::error('MercadoPago Webhook Rejected: Invalid signature match.', [
                    'received' => $v1,
                    'computed' => $computedSignature,
                    'manifest' => $manifest
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            return $next($request);

        } catch (\Exception $e) {
            Log::error('MercadoPago Signature Verification Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Verification failed'], 401);
        }
    }
}
