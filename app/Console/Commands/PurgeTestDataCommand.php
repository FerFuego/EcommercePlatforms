<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PurgeTestDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-test-data {--force : Forzar la ejecución sin pedir confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina todos los datos de prueba (pedidos, platos, usuarios no administradores) preservando configuraciones y administradores.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('⚠️  ¡ATENCIÓN! Esta acción eliminará PERMANENTEMENTE todos los datos de prueba de la plataforma:');
        $this->line('- Todos los pedidos, ítems y registros de delivery');
        $this->line('- Todos los platos, opciones y reseñas');
        $this->line('- Todos los cocineros, repartidores y usuarios (excepto administradores)');
        $this->line('- Historial de pagos de suscripción');
        $this->line('-> Cuentas de administradores y planes de suscripción seran PRESERVADOS.');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás seguro de que deseas proceder con la limpieza total de datos de prueba?', false)) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $this->info('Iniciando proceso de limpieza de datos de prueba...');

        try {
            DB::beginTransaction();
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

            $tablesToPurge = [
                'order_logs',
                'order_status_logs',
                'delivery_assignments',
                'order_item_options',
                'order_items',
                'orders',
                'reviews',
                'user_favorites',
                'dish_options',
                'dish_option_groups',
                'dishes',
                'broadcast_recipients',
                'cook_broadcasts',
                'subscription_payments',
                'cook_subscriptions',
                'user_push_tokens',
                'feedback',
                'cooks',
                'delivery_drivers',
            ];

            $counts = [];
            foreach ($tablesToPurge as $table) {
                if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    $counts[$table] = DB::table($table)->delete();
                } else {
                    $counts[$table] = 0;
                }
            }

            // Eliminar usuarios no administradores
            $nonAdminUsersCount = DB::table('users')
                ->where(function ($query) {
                    $query->where('role', '!=', 'admin')
                        ->orWhereNull('role');
                })
                ->delete();

            $adminUsersCount = DB::table('users')->where('role', 'admin')->count();

            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            DB::commit();

            $ordersCount = $counts['orders'] ?? 0;
            $dishesCount = $counts['dishes'] ?? 0;

            Log::info("PurgeTestDataCommand: Limpieza de datos completada. Usuarios no admin eliminados: {$nonAdminUsersCount}, Pedidos: {$ordersCount}, Platos: {$dishesCount}. Admins preservados: {$adminUsersCount}.");

            $this->newLine();
            $this->info('✅ ¡Limpieza de datos de prueba completada exitosamente!');
            $this->table(
                ['Concepto', 'Registros Eliminados'],
                [
                    ['Pedidos', $counts['orders'] ?? 0],
                    ['Ítems de Pedidos', $counts['order_items'] ?? 0],
                    ['Platos de Comida', $counts['dishes'] ?? 0],
                    ['Reseñas y Valoraciones', $counts['reviews'] ?? 0],
                    ['Perfiles de Cocineros', $counts['cooks'] ?? 0],
                    ['Perfiles de Repartidores', $counts['delivery_drivers'] ?? 0],
                    ['Pagos e Historial de Suscripciones', $counts['subscription_payments'] ?? 0],
                    ['Usuarios de Prueba (No-Admin)', $nonAdminUsersCount],
                ]
            );
            $this->info("🛡️ Cuentas de Administrador preservadas: {$adminUsersCount}");

            return 0;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error durante la limpieza de datos de prueba: " . $e->getMessage());
            $this->error("❌ Error al realizar la limpieza: " . $e->getMessage());
            return 1;
        }
    }
}
