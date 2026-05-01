<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class AdminSubscriptionPaymentController extends Controller
{
    protected $subscriptionService;
    protected $mpService;

    public function __construct(SubscriptionService $subscriptionService, \App\Services\MercadoPagoService $mpService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->mpService = $mpService;
    }

    public function index()
    {
        $payments = SubscriptionPayment::with(['cook.user', 'plan'])
            ->latest()
            ->paginate(15);

        $totalRevenue = SubscriptionPayment::where('status', 'approved')->sum('amount');
        $mpRevenue = SubscriptionPayment::where('status', 'approved')->where('payment_gateway', 'mercadopago')->sum('amount');
        $stripeRevenue = SubscriptionPayment::where('status', 'approved')->where('payment_gateway', 'stripe')->sum('amount');

        return view('admin.subscription-payments.index', compact('payments', 'totalRevenue', 'mpRevenue', 'stripeRevenue'));
    }

    public function syncFromMercadoPago()
    {
        try {
            // Buscamos pagos recientes (últimas 24 horas o top 50)
            $response = $this->mpService->searchPayments([
                'sort' => 'date_created',
                'criteria' => 'desc',
                'range' => 'date_created',
                'begin_date' => now()->subDays(7)->format('Y-m-d\TH:i:s.000-04:00'), // Última semana
                'end_date' => now()->format('Y-m-d\TH:i:s.000-04:00'),
            ]);

            if (!$response || !isset($response->results)) {
                return back()->with('error', 'No se pudieron recuperar pagos de Mercado Pago.');
            }

            $syncedCount = 0;
            foreach ($response->results as $mpPayment) {
                if ($mpPayment->status === 'approved') {
                    // Intentamos procesar el pago como si fuera un webhook
                    $success = $this->subscriptionService->handleAuthorizedPayment((string) $mpPayment->id);
                    if ($success) $syncedCount++;
                }
            }

            return back()->with('success', "Sincronización completada. Se procesaron {$syncedCount} pagos nuevos.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al sincronizar pagos: ' . $e->getMessage());
        }
    }
}
