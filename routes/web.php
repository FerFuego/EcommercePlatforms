<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CookDashboardController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Marketplace de Cocinarte
|--------------------------------------------------------------------------
*/

// Landing page pública
Route::get('/', [MarketplaceController::class, 'index'])->name('home');

// Marketplace (público)
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/catalog', [MarketplaceController::class, 'catalog'])->name('catalog');
    Route::get('/cook/{cookId}', [MarketplaceController::class, 'cookProfile'])->name('cook.profile');
    Route::get('/dish/{dishId}', [MarketplaceController::class, 'dishDetail'])->name('dish.detail');

    // API para mapa
    Route::get('/api/nearby-cooks', [MarketplaceController::class, 'nearbyCooksApi'])->name('api.nearby');
});

// Reviews públicas
Route::get('/cook/{cookId}/reviews', [ReviewController::class, 'cookReviews'])->name('reviews.cook');

// Authentication routes
require __DIR__ . '/auth.php';

// Rutas autenticadas
Route::middleware(['auth'])->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard redirection
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isCook()) {
            return redirect()->route('cook.dashboard');
        } elseif ($user->isDeliveryDriver()) {
            return redirect()->route('delivery-driver.dashboard');
        } else {
            return redirect()->route('orders.my');
        }
    })->name('dashboard');

    // Carrito y órdenes (clientes)
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [OrderController::class, 'cart'])->name('index');
        Route::post('/add/{dishId}', [OrderController::class, 'addToCart'])->name('add');
        Route::delete('/remove/{index}', [OrderController::class, 'removeFromCart'])->name('remove');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('process');
        Route::get('/success/{orderId}', [OrderController::class, 'success'])->name('success');
        Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my');
        Route::get('/{orderId}', [OrderController::class, 'show'])->name('show');

        // Reviews
        Route::post('/{orderId}/review', [ReviewController::class, 'store'])->name('review.store');

        // Reorder
        Route::post('/{orderId}/reorder', [OrderController::class, 'reorder'])->name('reorder');
    });
});

// Rutas de Cocinero
Route::middleware(['auth', 'cook'])->prefix('cook')->name('cook.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CookDashboardController::class, 'index'])->name('dashboard');

    // Perfil de cocinero
    Route::get('/profile/create', [CookDashboardController::class, 'createProfile'])->name('profile.create');
    Route::post('/profile', [CookDashboardController::class, 'storeProfile'])->name('profile.store');
    Route::get('/profile/edit', [CookDashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [CookDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile/photo', [CookDashboardController::class, 'deleteKitchenPhoto'])->name('profile.photo.delete');
    Route::post('/profile/toggle-active', [CookDashboardController::class, 'toggleActive'])->name('profile.toggle-active');

    // Gestión de platos
    Route::resource('dishes', DishController::class);
    Route::post('/dishes/{id}/toggle-active', [DishController::class, 'toggleActive'])->name('dishes.toggle');
    Route::post('/dishes/{id}/update-stock', [DishController::class, 'updateStock'])->name('dishes.stock');

    // Órdenes del cocinero
    Route::get('/orders', [OrderController::class, 'cookOrders'])->name('orders.index');
    Route::post('/orders/{orderId}/accept', [OrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{orderId}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    Route::post('/orders/{orderId}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});

// Rutas de Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Gestión de cocineros
    Route::get('/cooks/pending', [AdminController::class, 'pendingCooks'])->name('cooks.pending');
    Route::get('/cooks', [AdminController::class, 'allCooks'])->name('cooks.index');
    Route::get('/cooks/{cookId}', [AdminController::class, 'showCook'])->name('cooks.show');
    Route::post('/cooks/{cookId}/approve', [AdminController::class, 'approveCook'])->name('cooks.approve');
    Route::post('/cooks/{cookId}/reject', [AdminController::class, 'rejectCook'])->name('cooks.reject');

    // Órdenes
    Route::get('/orders', [AdminController::class, 'allOrders'])->name('orders.index');

    // Estadísticas
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');

    // Gestión de repartidores
    Route::get('/drivers/pending', [AdminController::class, 'pendingDrivers'])->name('drivers.pending');
    Route::get('/drivers', [AdminController::class, 'allDrivers'])->name('drivers.index');
    Route::get('/drivers/{driverId}', [AdminController::class, 'showDriver'])->name('drivers.show');
    Route::post('/drivers/{driverId}/approve', [AdminController::class, 'approveDriver'])->name('drivers.approve');
    Route::post('/drivers/{driverId}/reject', [AdminController::class, 'rejectDriver'])->name('drivers.reject');

    // Gestión de Usuarios
    Route::get('/users', [AdminController::class, 'allUsers'])->name('users.index');
    Route::post('/users/{userId}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::delete('/users/{userId}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Configuración
    Route::get('/settings', [App\Http\Controllers\AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\AdminSettingController::class, 'update'])->name('settings.update');
});

// Rutas de Delivery Driver
Route::middleware(['auth', 'delivery_driver'])->prefix('delivery-driver')->name('delivery-driver.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DeliveryDriverController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/profile/create', [App\Http\Controllers\DeliveryDriverController::class, 'createProfile'])->name('profile.create');
    Route::post('/profile', [App\Http\Controllers\DeliveryDriverController::class, 'storeProfile'])->name('profile.store');
    Route::get('/profile/edit', [App\Http\Controllers\DeliveryDriverController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\DeliveryDriverController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/toggle-availability', [App\Http\Controllers\DeliveryDriverController::class, 'toggleAvailability'])->name('profile.toggle');

    // Pedidos
    Route::get('/orders/available', [App\Http\Controllers\DeliveryOrderController::class, 'available'])->name('orders.available');
    Route::get('/orders', [App\Http\Controllers\DeliveryOrderController::class, 'myDeliveries'])->name('orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\DeliveryOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/accept', [App\Http\Controllers\DeliveryOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{id}/reject', [App\Http\Controllers\DeliveryOrderController::class, 'reject'])->name('orders.reject');
    Route::post('/orders/{id}/update-status', [App\Http\Controllers\DeliveryOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Ganancias
    Route::get('/earnings', [App\Http\Controllers\DeliveryDriverController::class, 'earnings'])->name('earnings');
});
