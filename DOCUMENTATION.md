# Multi-Tenant E-commerce Platform Documentation

## Overview
This is a Laravel-based multi-tenant e-commerce platform where a Superadmin manages Clients, and Clients manage their own Stores.

## Features
- **Superadmin Panel**: Manage Clients and Stores.
- **Client Owner Panel**: Manage Store Settings, Products, and Orders.
- **Storefront**: Public-facing store with Product Listing, Cart, and Checkout.
- **Integrations**: MercadoPago Payment Gateway and Email Notifications.

## Installation

1. **Clone the repository**
2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```
3. **Configure Environment**
   - Copy `.env.example` to `.env`
   - Set database credentials (SQLite by default)
   - Set MercadoPago Access Token (`MP_ACCESS_TOKEN`)
   - Set Mail credentials
4. **Run Migrations**
   ```bash
   php artisan migrate
   ```
5. **Serve Application**
   ```bash
   php artisan serve
   ```
6. **Run Queue Worker**
   ```bash
   php artisan queue:work
   ```
7. **Run WebSockets Server**
   ```bash
   php artisan reverb:start
   ```
8. **Run OR Run Laravel Complete Services**
   ```bash
   composer dev
   ```
## Production Notes (Hostinger/Shared Hosting)

### Broadcasting & WebSockets
In production, we use **Pusher**. Due to limitations in shared hosting environments:
- **Immediate Broadcasting**: Events like `OrderStatusUpdated` implement `ShouldBroadcastNow` instead of `ShouldBroadcast`. This ensures messages are sent to Pusher immediately without requiring a persistent queue worker (`php artisan queue:work`), which is often unavailable or unstable on Hostinger.
- **Frontend Diagnostics**: `resources/js/echo.js` includes listeners for `connecting`, `connected`, and `error` states to assist in debugging connection issues in the browser console.

### Push Notifications (Firebase)
- **Authorized Domains**: Ensure the production domain is added to the "Authorized Domains" in the Firebase Console (Authentication > Settings > Authorized domains).
- **Service Worker**: The file `public/firebase-messaging-sw.js` must be reachable at the root of the domain.

## Usage

### Superadmin
- Register a user (or seed one).
- Manually set role to `superadmin` in database.
- Access `/superadmin/dashboard`.
- Create Clients.

### Client Owner
- Created by Superadmin.
- Login with credentials provided.
- Access `/client/dashboard`.
- Create a Store.
- Add Products.
- Configure Payment Settings (MercadoPago Access Token).

### Customer
- Visit `/store/{slug}`.
- Browse products, add to cart.
- Checkout using MercadoPago or Bank Transfer.

## Architecture
- **Models**: User, Client, Store, Product, Order, OrderItem.
- **Middleware**: `EnsureUserIsSuperadmin`, `EnsureUserIsClientOwner`.
- **Controllers**: `SuperAdminController`, `ClientDashboardController`, `StorefrontController`.

## Credits
Built with Laravel 11, TailwindCSS, and MercadoPago SDK.
