<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Cocinarte',
            'email' => 'admin@cocinarte.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+54 3537 123456',
            'address' => 'Av. San Martín 123, Bell Ville, Córdoba',
        ]);

        echo "✅ Admin creado: admin@viandas.com / password\n";

        // 2. Create Customers
        $customers = [];
        $customerNames = [
            'Juan Pérez',
            'María González',
            'Carlos Rodríguez',
            'Ana Martínez',
            'Luis Fernández',
            'Laura Sánchez',
            'Diego López',
            'Sofía García',
        ];

        foreach ($customerNames as $index => $name) {
            $customers[] = User::create([
                'name' => $name,
                'email' => strtolower(
                    str_replace(
                        ' ',
                        '.',
                        strtr($name, [
                            'á' => 'a',
                            'é' => 'e',
                            'í' => 'i',
                            'ó' => 'o',
                            'ú' => 'u',
                            'Á' => 'a',
                            'É' => 'e',
                            'Í' => 'i',
                            'Ó' => 'o',
                            'Ú' => 'u',
                            'ñ' => 'n',
                            'Ñ' => 'n'
                        ])
                    )
                ) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+54 3537 ' . (400000 + $index),
                'address' => 'Calle ' . ($index + 1) . ', Bell Ville, Córdoba',
            ]);
        }

        echo "✅ " . count($customers) . " clientes creados\n";

        // 3. Create Cooks with realistic Bell Ville locations
        $cooksData = [
            [
                'name' => 'Doña Rosa Cocina Casera',
                'email' => 'rosa@viandas.com',
                'bio' => 'Más de 30 años cocinando comida tradicional argentina. Especialidad en guisos, estofados y pastas caseras. Mi pasión es compartir el sabor de la cocina de la abuela con cada plato que preparo.',
                'lat' => -32.6259,
                'lng' => -62.6887,
                'radius' => 5,
            ],
            [
                'name' => 'Mario Pastas Frescas',
                'email' => 'mario@cocinarte.com',
                'bio' => 'Chef italiano con 15 años de experiencia. Todas mis pastas son hechas a mano con ingredientes frescos. La tradición italiana en cada bocado.',
                'lat' => -32.6280,
                'lng' => -62.6850,
                'radius' => 8,
            ],
            [
                'name' => 'Veggie Delight - Cocina Vegana',
                'email' => 'veggie@cocinarte.com',
                'bio' => 'Comida vegana deliciosa y nutritiva. Uso ingredientes orgánicos locales. Opciones sin gluten disponibles. ¡La comida saludable también puede ser deliciosa!',
                'lat' => -32.6230,
                'lng' => -62.6900,
                'radius' => 10,
            ],
            [
                'name' => 'Parrilla Don Carlos',
                'email' => 'carlos@cocinarte.com',
                'bio' => 'Parrillero con 20 años de experiencia. Asados, choripanes y empanadas caseras. La mejor carne de la zona cocinada a la parrilla con leña.',
                'lat' => -32.6260,
                'lng' => -62.6820,
                'radius' => 6,
            ],
            [
                'name' => 'Comida Fit by Lucía',
                'email' => 'lucia@cocinarte.com',
                'bio' => 'Nutricionista y cocinera especializada en comida fit. Opciones bajas en calorías, altas en proteínas. Menús personalizados para deportistas.',
                'lat' => -32.6290,
                'lng' => -62.6920,
                'radius' => 12,
            ],
        ];

        // 3.1 Create Cooks in Villa María (10 cooks)
        $villaMariaCooks = [
            ['name' => 'La Cocina de Juana', 'specialty' => 'Empanadas', 'lat' => -32.4075, 'lng' => -63.2403],
            ['name' => 'Sabores de Villa', 'specialty' => 'Milanesas', 'lat' => -32.4100, 'lng' => -63.2450],
            ['name' => 'El Rincón del Chef', 'specialty' => 'Pastas', 'lat' => -32.4050, 'lng' => -63.2350],
            ['name' => 'Comida Casera VM', 'specialty' => 'Guisos', 'lat' => -32.4120, 'lng' => -63.2420],
            ['name' => 'Delicias del Centro', 'specialty' => 'Tartas', 'lat' => -32.4080, 'lng' => -63.2380],
            ['name' => 'Sabor y Arte', 'specialty' => 'Pizzas', 'lat' => -32.4150, 'lng' => -63.2500],
            ['name' => 'La Olla de Cobre', 'specialty' => 'Locro', 'lat' => -32.4020, 'lng' => -63.2300],
            ['name' => 'Menú Diario VM', 'specialty' => 'Minutas', 'lat' => -32.4110, 'lng' => -63.2480],
            ['name' => 'Chef Express', 'specialty' => 'Sandwiches', 'lat' => -32.4090, 'lng' => -63.2410],
            ['name' => 'Cocina Saludable', 'specialty' => 'Vegetariano', 'lat' => -32.4130, 'lng' => -63.2460],
        ];

        foreach ($villaMariaCooks as $index => $cook) {
            $cooksData[] = [
                'name' => $cook['name'],
                'email' => 'vm_cook_' . ($index + 1) . '@cocinarte.com',
                'bio' => 'Cocinero apasionado en Villa María. Especialidad en ' . $cook['specialty'] . '.',
                'lat' => $cook['lat'],
                'lng' => $cook['lng'],
                'radius' => 5,
            ];
        }

        // 3.2 Create Cooks in Marcos Juárez (10 cooks)
        $marcosJuarezCooks = [
            ['name' => 'El Buen Sabor MJ', 'specialty' => 'Asado', 'lat' => -32.6960, 'lng' => -62.1070],
            ['name' => 'Rotisería La Nona', 'specialty' => 'Pollo', 'lat' => -32.6980, 'lng' => -62.1050],
            ['name' => 'Cocina de Campo', 'specialty' => 'Fiambres', 'lat' => -32.6940, 'lng' => -62.1090],
            ['name' => 'Sabor Casero', 'specialty' => 'Pastel de Papa', 'lat' => -32.6970, 'lng' => -62.1060],
            ['name' => 'La Esquina del Sabor', 'specialty' => 'Hamburguesas', 'lat' => -32.6950, 'lng' => -62.1080],
            ['name' => 'Dulce y Salado', 'specialty' => 'Postres', 'lat' => -32.6990, 'lng' => -62.1040],
            ['name' => 'El Fuego', 'specialty' => 'Parrilla', 'lat' => -32.6930, 'lng' => -62.1100],
            ['name' => 'Comida al Paso', 'specialty' => 'Empanadas', 'lat' => -32.6965, 'lng' => -62.1075],
            ['name' => 'Chef MJ', 'specialty' => 'Gourmet', 'lat' => -32.6985, 'lng' => -62.1055],
            ['name' => 'Vida Sana', 'specialty' => 'Ensaladas', 'lat' => -32.6945, 'lng' => -62.1095],
        ];

        foreach ($marcosJuarezCooks as $index => $cook) {
            $cooksData[] = [
                'name' => $cook['name'],
                'email' => 'mj_cook_' . ($index + 1) . '@cocinarte.com',
                'bio' => 'Cocinero experto en Marcos Juárez. Ofrezco lo mejor en ' . $cook['specialty'] . '.',
                'lat' => $cook['lat'],
                'lng' => $cook['lng'],
                'radius' => 5,
            ];
        }

        $cooks = [];
        foreach ($cooksData as $cookData) {
            $user = User::create([
                'name' => $cookData['name'],
                'email' => $cookData['email'],
                'password' => Hash::make('password'),
                'role' => 'cook',
                'phone' => '+54 3537 ' . rand(400000, 499999),
                'address' => 'Bell Ville, Córdoba',
            ]);

            $cook = Cook::create([
                'user_id' => $user->id,
                'bio' => $cookData['bio'],
                'dni_photo' => 'cooks/dni/sample.jpg',
                'kitchen_photos' => ['cooks/kitchens/sample1.jpg', 'cooks/kitchens/sample2.jpg', 'cooks/kitchens/sample3.jpg'],
                'location_lat' => $cookData['lat'],
                'location_lng' => $cookData['lng'],
                'coverage_radius_km' => $cookData['radius'],
                'payout_method' => 'cbu',
                'payout_details' => json_encode(['cbu' => '0000003100' . rand(1000000000, 9999999999)]),
                'is_approved' => true,
                'active' => true,
                'rating_avg' => 0,
                'rating_count' => 0,
            ]);

            $cooks[] = $cook;
        }

        echo "✅ " . count($cooks) . " cocineros creados\n";

        // 4. Create Dishes
        $dishesData = [
            // Doña Rosa
            [
                'cook_index' => 0,
                'dishes' => [
                    ['Guiso de Lentejas', 'Guiso tradicional con lentejas, chorizo, panceta y verduras. Porción abundante.', 1200, ['vegetarian-option']],
                    ['Estofado de Carne', 'Carne estofada con papas, zanahorias y batatas. Cocción lenta de 3 horas.', 1500, []],
                    ['Ñoquis Caseros', 'Ñoquis de papa hechos a mano con salsa a elección (tuco, bolognesa, crema).', 1300, ['vegetarian']],
                    ['Pastel de Papa', 'Pastel de papa y carne al horno. Clásico argentino.', 1400, []],
                ],
            ],
            // Mario
            [
                'cook_index' => 1,
                'dishes' => [
                    ['Ravioles de Ricota', 'Ravioles caseros rellenos de ricota y espinaca. Salsa a elección.', 1600, ['vegetarian']],
                    ['Lasagna Bolognesa', 'Lasagna con 5 capas de pasta fresca, salsa bolognesa y bechamel.', 1800, []],
                    ['Sorrentinos de Jamón y Queso', 'Sorrentinos rellenos con jamón y queso. Masa extra fina.', 1700, []],
                    ['Tallarines Carbonara', 'Tallarines con salsa carbonara cremosa, panceta y queso parmesano.', 1500, []],
                ],
            ],
            // Veggie Delight
            [
                'cook_index' => 2,
                'dishes' => [
                    ['Bowl Vegano de Quinoa', 'Quinoa con garbanzos, palta, tomate, rúcula y aderezo tahini.', 1400, ['vegan', 'gluten-free']],
                    ['Hamburguesa de Lentejas', 'Hamburguesa vegana con pan integral, lechuga, tomate y hummus.', 1300, ['vegan']],
                    ['Curry de Garbanzos', 'Curry cremoso de garbanzos con leche de coco, arroz basmati y naan.', 1500, ['vegan', 'gluten-free']],
                    ['Ensalada Buddha Bowl', 'Bowl con edamame, brócoli, zanahoria, aguacate y salsa de maní.', 1200, ['vegan', 'gluten-free']],
                ],
            ],
            // Don Carlos
            [
                'cook_index' => 3,
                'dishes' => [
                    ['Asado con Guarnición', 'Asado de tira a la parrilla con ensalada y papas al horno.', 2000, []],
                    ['Choripán Completo', 'Choripán con chimichurri, salsa criolla y pan casero.', 800, []],
                    ['Empanadas de Carne (docena)', 'Empanadas de carne cortada a cuchillo con huevo y aceitunas.', 1800, []],
                    ['Vacío a la Parrilla', 'Vacío jugoso a la parrilla con chimichurri. Porción de 300g.', 2200, []],
                ],
            ],
            // Lucía Fit
            [
                'cook_index' => 4,
                'dishes' => [
                    ['Pechuga Grillada con Vegetales', 'Pechuga de pollo grillada con brócoli, zanahoria y batata. 350 cal.', 1400, ['gluten-free', 'low-carb']],
                    ['Salmón con Quinoa', 'Filete de salmón a la plancha con quinoa y espárragos. 400 cal.', 1800, ['gluten-free']],
                    ['Ensalada Proteica', 'Mix de hojas verdes, atún, huevo, palta y semillas. 300 cal.', 1200, ['gluten-free', 'low-carb']],
                    ['Bowl Fitness', 'Arroz integral, pollo, verduras salteadas y salsa de soja light. 450 cal.', 1500, ['gluten-free']],
                ],
            ],
        ];

        // Generate dishes for new cooks (indices 5 to 24)
        for ($i = 5; $i < 25; $i++) {
            $dishesData[] = [
                'cook_index' => $i,
                'dishes' => [
                    ['Plato Especial 1', 'Delicioso plato casero preparado con ingredientes frescos.', rand(1000, 2000), []],
                    ['Plato Especial 2', 'Nuestra especialidad de la casa, no te lo pierdas.', rand(1200, 2200), []],
                    ['Plato Especial 3', 'Opción económica y sabrosa para el almuerzo.', rand(800, 1500), []],
                ],
            ];
        }

        $allDishes = [];
        foreach ($dishesData as $cookDishes) {
            $cook = $cooks[$cookDishes['cook_index']];

            foreach ($cookDishes['dishes'] as $dishData) {
                $dish = Dish::create([
                    'cook_id' => $cook->id,
                    'name' => $dishData[0],
                    'description' => $dishData[1],
                    'price' => $dishData[2],
                    'photo_url' => null, // Podrías agregar URLs de imágenes aquí
                    'available_stock' => rand(5, 20),
                    'is_active' => true,
                    'diet_tags' => $dishData[3],
                    'available_days' => [1, 2, 3, 4, 5, 6], // Lunes a Sábado
                    'preparation_time_minutes' => rand(20, 45),
                    'delivery_method' => 'both',
                ]);

                $allDishes[] = $dish;
            }
        }

        echo "✅ " . count($allDishes) . " platos creados\n";

        // 5. Create Orders
        $orderCount = 0;
        foreach ($customers as $customer) {
            // Cada cliente hace 1-3 pedidos
            $numOrders = rand(1, 3);

            for ($i = 0; $i < $numOrders; $i++) {
                $randomCook = $cooks[array_rand($cooks)];
                $cookDishes = $allDishes; // Filtrar por cook si quieres

                // Seleccionar 1-3 platos random
                $numItems = rand(1, 3);
                $selectedDishes = array_rand($allDishes, min($numItems, count($allDishes)));
                if (!is_array($selectedDishes)) {
                    $selectedDishes = [$selectedDishes];
                }

                $subtotal = 0;
                $itemsData = [];

                foreach ($selectedDishes as $dishIndex) {
                    $dish = $allDishes[$dishIndex];
                    $quantity = rand(1, 2);
                    $price = $dish->price;
                    $total = $price * $quantity;

                    $itemsData[] = [
                        'dish' => $dish,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $total,
                    ];

                    $subtotal += $total;
                }

                $deliveryFee = rand(0, 1) ? 500 : 0;
                $totalAmount = $subtotal + $deliveryFee;

                $statuses = ['delivered', 'preparing', 'awaiting_cook_acceptance', 'ready_for_pickup'];
                $status = $statuses[array_rand($statuses)];

                $order = Order::create([
                    'customer_id' => $customer->id,
                    'cook_id' => $randomCook->id,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'total_amount' => $totalAmount,
                    'commission_amount' => round($subtotal * 0.12, 2),
                    'delivery_type' => $deliveryFee > 0 ? 'delivery' : 'pickup',
                    'delivery_address' => $deliveryFee > 0 ? $customer->address : null,
                    'payment_method' => ['mercadopago', 'cash', 'transfer'][array_rand(['mercadopago', 'cash', 'transfer'])],
                    'payment_id' => $status !== 'awaiting_cook_acceptance' ? 'PAY_' . strtoupper(bin2hex(random_bytes(8))) : null,
                    'notes' => rand(0, 1) ? 'Sin cebolla por favor' : null,
                ]);

                // Create order items
                foreach ($itemsData as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'dish_id' => $itemData['dish']->id,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['price'],
                        'total_price' => $itemData['total'],
                    ]);
                }

                $orderCount++;

                // Create review for delivered orders
                if ($status === 'delivered' && rand(0, 1)) {
                    $comments = [
                        '¡Excelente! Muy rica la comida y buena porción.',
                        'Delicioso, llegó caliente y bien presentado.',
                        'Muy buena atención y comida casera de verdad.',
                        'Riquísimo, volveré a pedir seguro.',
                        'Buena comida, aunque la porción podría ser un poco más grande.',
                    ];

                    Review::create([
                        'order_id' => $order->id,
                        'customer_id' => $customer->id,
                        'cook_id' => $randomCook->id,
                        'rating' => rand(4, 5),
                        'comment' => $comments[array_rand($comments)],
                    ]);
                }
            }
        }

        echo "✅ {$orderCount} pedidos iniciales creados\n";

        // 6. Generate 100 random reviews (requires creating delivered orders first)
        echo "🚀 Generando 100 reviews aleatorias...\n";

        $reviewComments = [
            '¡Excelente comida! Sabor casero de verdad.',
            'Muy buena atención y la comida llegó caliente.',
            'Las porciones son abundantes. Recomiendo.',
            'Todo riquísimo, sin duda volveré a pedir.',
            'Me encantó, muy buena relación precio-calidad.',
            'Llegó un poco tarde pero la comida estaba exquisita.',
            'El mejor plato que he probado en mucho tiempo.',
            'Muy amable el cocinero y excelente presentación.',
            'Sabor auténtico, me recordó a la comida de mi abuela.',
            'Buena opción para el almuerzo, rápido y rico.',
            'La comida estaba un poco fría, pero rica.',
            'Excelente servicio, muy recomendables.',
            'Increíble sabor, se nota que usan ingredientes frescos.',
            'Muy buena opción vegetariana.',
            'La carne estaba en su punto justo. Perfecto.',
        ];

        for ($i = 0; $i < 100; $i++) {
            $cook = $cooks[array_rand($cooks)];
            $customer = $customers[array_rand($customers)];

            // Create a delivered order (historical)
            $order = Order::create([
                'customer_id' => $customer->id,
                'cook_id' => $cook->id,
                'status' => 'delivered',
                'subtotal' => 1500,
                'delivery_fee' => 0,
                'total_amount' => 1500,
                'commission_amount' => 180,
                'delivery_type' => 'pickup',
                'payment_method' => 'cash',
                'payment_status' => 'approved',
                'completed_at' => now()->subDays(rand(1, 60)),
                'created_at' => now()->subDays(rand(1, 60)),
            ]);

            // Create Order Item (required for consistency)
            // Pick a random dish from this cook
            $dish = $cook->dishes->isNotEmpty() ? $cook->dishes->random() : null;

            if ($dish) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'dish_id' => $dish->id,
                    'quantity' => 1,
                    'unit_price' => $dish->price,
                    'total_price' => $dish->price,
                ]);
            }

            // Create Review
            Review::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'cook_id' => $cook->id,
                'rating' => rand(3, 5), // Mostly positive
                'comment' => $reviewComments[array_rand($reviewComments)],
                'created_at' => $order->completed_at->addHours(2),
            ]);
        }

        echo "✅ 100 reviews adicionales creadas\n";

        echo "\n🎉 ¡Seeder completado!\n\n";
        echo "📧 Credenciales de prueba:\n";
        echo "   Admin: admin@cocinarte.com / password\n";
        echo "   Cocinero 1: rosa@cocinarte.com / password\n";
        echo "   Cocinero 2: mario@cocinarte.com / password\n";
        echo "   Cliente: juan.perez@example.com / password\n";
        echo "\n";
    }
}
