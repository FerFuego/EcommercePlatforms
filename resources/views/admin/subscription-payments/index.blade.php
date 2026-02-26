@extends('layouts.admin')

@section('title', 'Historial de Pagos de Suscripciones')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Recaudación por Suscripciones</h1>
                <p class="text-muted">Control de ingresos y pagos recurrentes de cocineros.</p>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Recaudación Total</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    MercadoPago</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($mpRevenue, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Stripe</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stripeRevenue, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fab fa-stripe fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Listado de Acreditaciones</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cocinero</th>
                                <th>Plan</th>
                                <th>Monto</th>
                                <th>Proveedor</th>
                                <th>Estado</th>
                                <th>ID Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : $payment->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <strong>{{ $payment->cook->user->name }}</strong><br>
                                        <small class="text-muted">{{ $payment->cook->user->email }}</small>
                                    </td>
                                    <td>{{ $payment->plan->name }}</td>
                                    <td>
                                        <strong>${{ number_format($payment->amount, 2) }}</strong>
                                        <small>{{ $payment->currency }}</small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $payment->provider === 'stripe' ? 'badge-info' : 'badge-primary' }}">
                                            {{ ucfirst($payment->provider) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td><code class="small">{{ $payment->provider_payment_id }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No se registran pagos aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection