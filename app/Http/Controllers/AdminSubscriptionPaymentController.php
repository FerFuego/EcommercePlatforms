<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class AdminSubscriptionPaymentController extends Controller
{
    public function index()
    {
        $payments = SubscriptionPayment::with(['cook.user', 'plan'])
            ->latest()
            ->paginate(15);

        $totalRevenue = SubscriptionPayment::where('status', 'approved')->sum('amount');
        $mpRevenue = SubscriptionPayment::where('status', 'approved')->where('provider', 'mercadopago')->sum('amount');
        $stripeRevenue = SubscriptionPayment::where('status', 'approved')->where('provider', 'stripe')->sum('amount');

        return view('admin.subscription-payments.index', compact('payments', 'totalRevenue', 'mpRevenue', 'stripeRevenue'));
    }
}
