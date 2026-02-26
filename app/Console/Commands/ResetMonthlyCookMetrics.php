<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cook;
use Illuminate\Support\Facades\Log;

class ResetMonthlyCookMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cooks:reset-monthly-metrics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the monthly sales and orders accumulated for all cooks and unlock them if applicable.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Starting monthly cook metrics reset.');

        $cooksToUpdate = Cook::query()->update([
            'monthly_sales_accumulated' => 0,
            'monthly_orders_accumulated' => 0,
            'sales_reset_at' => now(),
            'is_selling_blocked' => false // Unblock them at the start of the month
        ]);

        Log::info("Successfully reset monthly metrics for {$cooksToUpdate} cooks.");
        $this->info("Successfully reset monthly metrics for {$cooksToUpdate} cooks.");
    }
}
