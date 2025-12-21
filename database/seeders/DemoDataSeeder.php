<?php

namespace Database\Seeders;

use App\Models\Cook;
use App\Models\DeliveryDriver;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Crear Admin (si no existe)
        $admin = User::firstOrCreate(
            ['email' => 'admin@cocinarte.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '+54 351 000 0000',
            ]
        );

        // Crear Cocineros
        $cook1 = User::create([
            'name' => 'María González',
            'email' => 'maria@cocina.com',
            'password' => bcrypt('password'),
            'role' => 'cook',
            'phone' => '+54 351 111 1111',
            'address' => 'Av. Colón 500, Córdoba',
        ]);

        $cookProfile1 = Cook::create([
            'user_id' => $cook1->id,
            'bio' => 'Especialista en comida casera argentina. Más de 20 años de experiencia.',
            'location_lat' => -31.4201,
            'location_lng' => -64.1888,
            'is_approved' => true,
            'active' => true,
        ]);

        $cook2 = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@chef.com',
            'password' => bcrypt('password'),
            'role' => 'cook',
            'phone' => '+54 351 222 2222',
            'address' => 'Av. Vélez Sarsfield 200, Córdoba',
        ]);

        $cookProfile2 = Cook::create([
            'user_id' => $cook2->id,
            'bio' => 'Comida italiana casera. Pastas frescas y salsas tradicionales.',
            'location_lat' => -31.4167,
            'location_lng' => -64.1833,
            'is_approved' => true,
            'active' => true,
        ]);

        $cook3 = User::create([
            'name' => 'Ana Martínez',
            'email' => 'ana@cocina.com',
            'password' => bcrypt('password'),
            'role' => 'cook',
            'phone' => '+54 351 333 3333',
            'address' => 'Av. Rafael Núñez 1000, Córdoba',
        ]);

        $cookProfile3 = Cook::create([
            'user_id' => $cook3->id,
            'bio' => 'Repostería artesanal y tortas personalizadas.',
            'location_lat' => -31.4100,
            'location_lng' => -64.1950,
            'is_approved' => true,
            'active' => true,
        ]);

        // Cocinero pendiente de aprobación
        $cook4 = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos@cocina.com',
            'password' => bcrypt('password'),
            'role' => 'cook',
            'phone' => '+54 351 444 4444',
            'address' => 'Av. Hipólito Yrigoyen 300, Córdoba',
        ]);

        Cook::create([
            'user_id' => $cook4->id,
            'bio' => 'Comida vegana y vegetariana.',
            'location_lat' => -31.4250,
            'location_lng' => -64.1800,
            'is_approved' => false,
            'active' => false,
        ]);

        // Crear Platos
        $dishes = [
            // María González
            ['cook_id' => $cookProfile1->id, 'name' => 'Milanesas con Puré', 'description' => 'Milanesas de carne con puré de papas casero', 'price' => 2500, 'available_stock' => 10],
            ['cook_id' => $cookProfile1->id, 'name' => 'Empanadas de Carne', 'description' => 'Docena de empanadas caseras jugosas', 'price' => 3000, 'available_stock' => 20],
            ['cook_id' => $cookProfile1->id, 'name' => 'Guiso de Lentejas', 'description' => 'Guiso casero con chorizo y verduras', 'price' => 2000, 'available_stock' => 8],
            ['cook_id' => $cookProfile1->id, 'name' => 'Tarta de Verduras', 'description' => 'Tarta casera de acelga y ricota', 'price' => 1800, 'available_stock' => 6],

            // Juan Pérez
            ['cook_id' => $cookProfile2->id, 'name' => 'Ravioles Caseros', 'description' => 'Ravioles de ricota con salsa bolognesa', 'price' => 2800, 'available_stock' => 15],
            ['cook_id' => $cookProfile2->id, 'name' => 'Ñoquis de Papa', 'description' => 'Ñoquis caseros con salsa a elección', 'price' => 2500, 'available_stock' => 12],
            ['cook_id' => $cookProfile2->id, 'name' => 'Lasagna', 'description' => 'Lasagna de carne con bechamel', 'price' => 3200, 'available_stock' => 8],
            ['cook_id' => $cookProfile2->id, 'name' => 'Tiramisu', 'description' => 'Postre italiano tradicional', 'price' => 1500, 'available_stock' => 10],

            // Ana Martínez
            ['cook_id' => $cookProfile3->id, 'name' => 'Torta de Chocolate', 'description' => 'Torta húmeda de chocolate con ganache', 'price' => 4000, 'available_stock' => 5],
            ['cook_id' => $cookProfile3->id, 'name' => 'Lemon Pie', 'description' => 'Tarta de limón con merengue italiano', 'price' => 3500, 'available_stock' => 6],
            ['cook_id' => $cookProfile3->id, 'name' => 'Alfajores Artesanales', 'description' => 'Docena de alfajores de maicena', 'price' => 2000, 'available_stock' => 20],
            ['cook_id' => $cookProfile3->id, 'name' => 'Cheesecake', 'description' => 'Cheesecake de frutos rojos', 'price' => 4500, 'available_stock' => 4],
        ];

        foreach ($dishes as $dishData) {
            Dish::create(array_merge($dishData, ['is_active' => true]));
        }

        // Crear Clientes
        $customer1 = User::create([
            'name' => 'Laura Fernández',
            'email' => 'laura@cliente.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '+54 351 555 5555',
            'address' => 'Av. Hipólito Yrigoyen 100, Córdoba',
        ]);

        $customer2 = User::create([
            'name' => 'Pedro Gómez',
            'email' => 'pedro@cliente.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '+54 351 666 6666',
            'address' => 'Av. Colón 800, Córdoba',
        ]);

        $customer3 = User::create([
            'name' => 'Sofía López',
            'email' => 'sofia@cliente.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '+54 351 777 7777',
            'address' => 'Av. Rafael Núñez 500, Córdoba',
        ]);

        // Crear Pedidos Entregados (para estadísticas)
        $allDishes = Dish::all();

        // Pedido 1 - Entregado
        $order1 = Order::create([
            'customer_id' => $customer1->id,
            'cook_id' => $cookProfile1->id,
            'status' => Order::STATUS_DELIVERED,
            'total_amount' => 5500,
            'commission_amount' => 550,
            'delivery_type' => 'pickup',
            'delivery_address' => $customer1->address,
            'delivery_lat' => -31.4201,
            'delivery_lng' => -64.1888,
            'payment_method' => 'cash',
            'created_at' => now()->subDays(5),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'dish_id' => $allDishes->where('name', 'Milanesas con Puré')->first()->id,
            'quantity' => 1,
            'price' => 2500,
            'total_price' => 2500,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'dish_id' => $allDishes->where('name', 'Empanadas de Carne')->first()->id,
            'quantity' => 1,
            'price' => 3000,
            'total_price' => 3000,
        ]);

        // Pedido 2 - Entregado
        $order2 = Order::create([
            'customer_id' => $customer2->id,
            'cook_id' => $cookProfile2->id,
            'status' => Order::STATUS_DELIVERED,
            'total_amount' => 5600,
            'commission_amount' => 560,
            'delivery_type' => 'delivery',
            'delivery_address' => $customer2->address,
            'delivery_lat' => -31.4201,
            'delivery_lng' => -64.1888,
            'payment_method' => 'mercadopago',
            'delivery_fee' => 500,
            'created_at' => now()->subDays(3),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'dish_id' => $allDishes->where('name', 'Ravioles Caseros')->first()->id,
            'quantity' => 2,
            'price' => 2800,
            'total_price' => 5600,
        ]);

        // Pedido 3 - Entregado
        $order3 = Order::create([
            'customer_id' => $customer3->id,
            'cook_id' => $cookProfile3->id,
            'status' => Order::STATUS_DELIVERED,
            'total_amount' => 7500,
            'commission_amount' => 750,
            'delivery_type' => 'pickup',
            'delivery_address' => $customer3->address,
            'delivery_lat' => -31.4100,
            'delivery_lng' => -64.1950,
            'payment_method' => 'cash',
            'created_at' => now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'dish_id' => $allDishes->where('name', 'Torta de Chocolate')->first()->id,
            'quantity' => 1,
            'price' => 4000,
            'total_price' => 4000,
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'dish_id' => $allDishes->where('name', 'Lemon Pie')->first()->id,
            'quantity' => 1,
            'price' => 3500,
            'total_price' => 3500,
        ]);

        // Pedidos Pendientes
        $order4 = Order::create([
            'customer_id' => $customer1->id,
            'cook_id' => $cookProfile1->id,
            'status' => Order::STATUS_PREPARING,
            'total_amount' => 2000,
            'commission_amount' => 200,
            'delivery_type' => 'delivery',
            'delivery_address' => $customer1->address,
            'delivery_lat' => -31.4201,
            'delivery_lng' => -64.1888,
            'payment_method' => 'mercadopago',
            'delivery_fee' => 500,
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'dish_id' => $allDishes->where('name', 'Guiso de Lentejas')->first()->id,
            'quantity' => 1,
            'price' => 2000,
            'total_price' => 2000,
        ]);

        $order5 = Order::create([
            'customer_id' => $customer2->id,
            'cook_id' => $cookProfile2->id,
            'status' => Order::STATUS_AWAITING_COOK,
            'total_amount' => 3200,
            'commission_amount' => 320,
            'delivery_type' => 'pickup',
            'delivery_address' => $customer2->address,
            'delivery_lat' => -31.4167,
            'delivery_lng' => -64.1833,
            'payment_method' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order5->id,
            'dish_id' => $allDishes->where('name', 'Lasagna')->first()->id,
            'quantity' => 1,
            'price' => 3200,
            'total_price' => 3200,
        ]);

        // Crear Repartidor aprobado
        $driver1 = User::create([
            'name' => 'Roberto Delivery',
            'email' => 'roberto@delivery.com',
            'password' => bcrypt('password'),
            'role' => 'delivery_driver',
            'phone' => '+54 351 888 8888',
            'address' => 'Av. Colón 300, Córdoba',
        ]);

        DeliveryDriver::create([
            'user_id' => $driver1->id,
            'dni_number' => '12345678',
            'vehicle_type' => 'motorcycle',
            'vehicle_plate' => 'ABC123',
            'location_lat' => -31.4201,
            'location_lng' => -64.1888,
            'coverage_radius_km' => 5,
            'is_approved' => true,
            'is_available' => true,
            'bank_name' => 'Banco Nación',
            'account_number' => '1234567890',
            'account_type' => 'savings',
        ]);

        $this->command->info('✅ Datos de demostración creados exitosamente!');
        $this->command->info('📊 Resumen:');
        $this->command->info('  - 1 Admin');
        $this->command->info('  - 4 Cocineros (3 aprobados, 1 pendiente)');
        $this->command->info('  - 12 Platos');
        $this->command->info('  - 3 Clientes');
        $this->command->info('  - 5 Pedidos (3 entregados, 2 pendientes)');
        $this->command->info('  - 1 Repartidor aprobado');
        $this->command->info('');
        $this->command->info('🔑 Credenciales:');
        $this->command->info('  Admin: admin@cocinarte.com / password');
        $this->command->info('  Cocineros: maria@cocina.com, juan@chef.com, ana@cocina.com / password');
        $this->command->info('  Clientes: laura@cliente.com, pedro@cliente.com, sofia@cliente.com / password');
        $this->command->info('  Repartidor: roberto@delivery.com / password');
    }
}
