@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <a href="{{ route('cook.subscription.index') }}"
                    class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-primary mb-3 transition-colors group">
                    <svg class="h-4 w-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver a Suscripción
                </a>
                <h1 class="text-5xl font-black tracking-tight">
                    <span class="bg-gradient-to-r from-orange-600 via-orange-600 to-purple-600 bg-clip-text text-transparent">
                        Historial de Pagos
                    </span>
                </h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Control total de tus inversiones en la plataforma.</p>
            </div>

            <!-- Mini Summary Cards -->
            <div class="flex flex-wrap gap-4">
                <div class="bg-white dark:bg-gray-800 px-6 py-4 rounded-2xl shadow-lg border-l-4 border-purple-600 flex flex-col justify-center min-w-[200px]">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Invertido</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white">${{ number_format($totalInvested, 2) }}</span>
                </div>
                <div class="bg-white dark:bg-gray-800 px-6 py-4 rounded-2xl shadow-lg border-l-4 border-emerald-500 flex flex-col justify-center min-w-[200px]">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Plan Actual</span>
                    <span class="text-2xl font-black text-emerald-600 uppercase">{{ $cook->currentSubscription->plan->name ?? 'Sin Plan' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-900/50 flex items-center justify-between">
                <h2 class="text-xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tight">Movimientos Recientes</h2>
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-1.5 rounded-full border border-gray-100 dark:border-gray-800">
                    <span class="text-xs font-bold text-gray-400">{{ $payments->total() }} registros encontrados</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/30 dark:bg-gray-900/40 text-gray-400">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest">Acreditado el</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest">Plan de Servicio</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest">Monto Neto</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-center">Plataforma</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-center">Estado</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-right">Referencia ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-900/50">
                        @forelse($payments as $payment)
                            <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-700 dark:text-gray-300">{{ $payment->paid_at ? $payment->paid_at->format('d M, Y') : $payment->created_at->format('d M, Y') }}</span>
                                        <span class="text-[10px] font-bold text-gray-400">{{ $payment->paid_at ? $payment->paid_at->format('H:i') : $payment->created_at->format('H:i') }} hs</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg text-xs font-black uppercase tracking-tighter">
                                        {{ $payment->plan->name }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-xl font-black text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $payment->currency }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex flex-col items-center">
                                        @if($payment->provider === 'stripe')
                                            <svg viewBox="0 0 40 40" class="h-6 mb-1"><path fill="#635bff" d="M34.7 20.3c0-4.6-2.3-7.2-6.5-7.2-4.1 0-6.9 2.7-6.9 7.2 0 5.4 3.7 7.2 8.3 7.2 1.5 0 3-.2 4.1-.7l-.4-2.8c-1 .4-2 .5-3.1.5-2.6 0-5.1-.7-5.1-4.2h10.4c-.1-1.3-.8-4.5-.8-4.5zm-5.6-2.6c0-.1 0-.1.1-.1.9 0 1.5.7 1.7 1.7h-3.4c.1-1.1.7-1.6 1.6-1.6zm-17 1.3c0-2 1.3-3.1 3.5-3.1 1.2 0 2 .1 2.9.5l-.3-2.7c-.7-.3-1.6-.5-3-.5-4.2 0-6.8 2.3-6.8 6.5 0 5.8 3.7 7.2 8.3 7.2 1 0 2-.1 2.8-.4l-.4-2.8c-.8.3-1.4.4-2.3.4-2.7 0-4.7-.9-4.7-4.1v-.8c-.1-.2-.1-.2-.1-.2z"/></svg>
                                        @else
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/bc/Mercado_Pago_logo.svg" alt="MP" class="h-4 mb-2">
                                        @endif
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $payment->provider }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 border border-emerald-100 dark:border-emerald-800/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <code class="text-[10px] font-black text-gray-300 dark:text-gray-600 bg-gray-50/50 dark:bg-gray-900 px-2 py-1 rounded border border-gray-100 dark:border-gray-800 group-hover:text-gray-500 transition-colors">
                                        {{ $payment->provider_payment_id ?? 'N/A' }}
                                    </code>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-full mb-4">
                                            <svg class="h-12 w-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Sin movimientos registrados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-50 dark:border-gray-900">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

        <div class="mt-12 bg-gradient-to-r from-purple-900 to-indigo-900 rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 bg-white/5 w-40 h-40 rounded-full blur-3xl transform group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center">
                    <div class="bg-white/10 p-4 rounded-2xl mr-6 backdrop-blur-md border border-white/10">
                        <svg class="h-8 w-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-white uppercase tracking-tight">Gestión de Suscripción</h4>
                        <p class="text-purple-200/70 text-sm mt-1">Tus cobros recurrentes son procesados de forma segura por las pasarelas oficiales.</p>
                    </div>
                </div>
                <div class="flex flex-col items-center md:items-end text-center md:text-right">
                    <p class="text-xs font-bold text-white/50 uppercase tracking-[0.2em] mb-2 font-mono">Seguridad Nivel Bancario</p>
                    <div class="flex gap-3">
                        <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-[10px] font-black text-white/80 uppercase tracking-widest">SSL Secure</span>
                        <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-[10px] font-black text-white/80 uppercase tracking-widest">PCI DSS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection