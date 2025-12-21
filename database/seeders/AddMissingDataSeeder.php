<?php

namespace Database\Seeders;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddMissingDataSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener cocineros existentes
        $cooks = Cook::where('is_approved', true)->get();

        if ($cooks->count() < 3) {
            $this->command->error('Se necesitan al menos 3 cocineros aprobados. Ejecuta primero DemoDataSeeder.');
            return;
        }

        // Crear Platos para cada cocinero
        $cook1 = $cooks[0];
        $cook2 = $cooks[1];
        $cook3 = $cooks[2];

        $this->command->info('Creando platos...');

        // Platos para cocinero 1
        Dish::create(['cook_id' => $cook1->id, 'name' => 'Milanesas con Puré', 'description' => 'Milanesas de carne con puré de papas casero', 'price' => 2500, 'available_stock' => 10, 'is_active' => true]);
        Dish::create(['cook_id' => $cook1->id, 'name' => 'Empanadas de Carne', 'description' => 'Docena de empanadas caseras jugosas', 'price' => 3000, 'available_stock' => 20, 'is_active' => true]);
        Dish::create(['cook_id' => $cook1->id, 'name' => 'Guiso de Lentejas', 'description' => 'Guiso casero con chorizo y verduras', 'price' => 2000, 'available_stock' => 8, 'is_active' => true]);
        Dish::create(['cook_id' => $cook1->id, 'name' => 'Tarta de Verduras', 'description' => 'Tarta casera de acelga y ricota', 'price' => 1800, 'available_stock' => 6, 'is_active' => true]);

        // Platos para cocinero 2
        Dish::create(['cook_id' => $cook2->id, 'name' => 'Ravioles Caseros', 'description' => 'Ravioles de ricota con salsa bolognesa', 'price' => 2800, 'available_stock' => 15, 'is_active' => true]);
        Dish::create(['cook_id' => $cook2->id, 'name' => 'Ñoquis de Papa', 'description' => 'Ñoquis caseros con salsa a elección', 'price' => 2500, 'available_stock' => 12, 'is_active' => true]);
        Dish::create(['cook_id' => $cook2->id, 'name' => 'Lasagna', 'description' => 'Lasagna de carne con bechamel', 'price' => 3200, 'available_stock' => 8, 'is_active' => true]);
        Dish::create(['cook_id' => $cook2->id, 'name' => 'Tiramisu', 'description' => 'Postre italiano tradicional', 'price' => 1500, 'available_stock' => 10, 'is_active' => true]);

        // Platos para cocinero 3
        Dish::create(['cook_id' => $cook3->id, 'name' => 'Torta de Chocolate', 'description' => 'Torta húmeda de chocolate con ganache', 'price' => 4000, 'available_stock' => 5, 'is_active' => true]);
        Dish::create(['cook_id' => $cook3->id, 'name' => 'Lemon Pie', 'description' => 'Tarta de limón con merengue italiano', 'price' => 3500, 'available_stock' => 6, 'is_active' => true]);
        Dish::create(['cook_id' => $cook3->id, 'name' => 'Alfajores Artesanales', 'description' => 'Docena de alfajores de maicena', 'price' => 2000, 'available_stock' => 20, 'is_active' => true]);
        Dish::create(['cook_id' => $cook3->id, 'name' => 'Cheesecake', 'description' => 'Cheesecake de frutos rojos', 'price' => 4500, 'available_stock' => 4, 'is_active' => true]);

        $this->command->info('✅ ' . Dish::count() . ' platos creados');

        // Crear Clientes
        $this->command->info('Creando clientes...');

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

        $this->command->info('✅ 3 clientes creados');

        // Crear Pedidos
        $this->command->info('Creando pedidos...');

        $allDishes = Dish::all();

        // Pedido 1 - Entregado
        $order1 = Order::create([
            'customer_id' => $customer1->id,
            'cook_id' => $cook1->id,
            'status' => 'delivered',
            'subtotal' => 5500,
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
            'cook_id' => $cook2->id,
            'status' => 'delivered',
            'subtotal' => 5600,
            'total_amount' => 6100,
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
            'cook_id' => $cook3->id,
            'status' => 'delivered',
            'subtotal' => 7500,
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
            'cook_id' => $cook1->id,
            'status' => 'preparing',
            'subtotal' => 2000,
            'total_amount' => 2500,
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
            'cook_id' => $cook2->id,
            'status' => 'awaiting_cook_acceptance',
            'subtotal' => 3200,
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

        $this->command->info('✅ 5 pedidos creados (3 entregados, 2 pendientes)');

        $this->command->info('');
        $this->command->info('🎉 ¡Datos de demostración completados!');
        $this->command->info('📊 Ahora el panel de admin mostrará estadísticas.');
    }
}
