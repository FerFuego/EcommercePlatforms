# Marketplace de Cocinarte

## Fase 1: Análisis y Planificación
 Analizar estructura del proyecto actual
 Identificar componentes a eliminar
 Identificar componentes a reutilizar
 Diseñar nueva arquitectura de base de datos
 Crear plan de implementación detallado

## Fase 2: Limpieza del Proyecto
 Eliminar modelos no necesarios (Client, Store, Category, Banner, Product viejo)
 Eliminar migraciones relacionadas con e-commerce
 Eliminar controladores de superadmin y client_owner
 Eliminar vistas del sistema anterior
 Limpiar rutas no necesarias

## Fase 3: Nueva Arquitectura - Base de Datos
 Modificar migración de users para agregar campos (role, phone, address)
 Crear migración de tabla cooks (extensión 1:1 de users)
 Crear migración de tabla dishes (platos/viandas)
 Crear migración de tabla orders (pedidos con estados)
 Crear migración de tabla order_items (items dentro de pedidos)
 Crear migración de tabla reviews (reseñas)
  Crear migración de tabla order_logs (logs de eventos)
  Crear migración de tabla favorite_cooks (pivot para favoritos) [NEW]
  Agregar campos de programación a dishes y cooks [NEW]
  Agregar campo notes a orders [NEW]
 Crear migración de tabla delivery_assignments (opcional post-MVP)

## Fase 4: Modelos y Relaciones
 Modificar modelo User con relaciones y métodos helper
 Crear modelo Cook con geolocalización
 Crear modelo Dish
 Crear modelo Order con estados y transiciones
 Crear modelo OrderItem
 Crear modelo Review con observers
 Crear modelo DeliveryAssignment (opcional)
 Definir todas las relaciones Eloquent

## Fase 5: Autenticación y Roles
 Actualizar sistema de roles en User (customer, cook, delivery, admin)
 Crear middleware para cada rol (CookMiddleware, AdminMiddleware)
 Actualizar registro para permitir selección de rol

## Fase 6: Controladores Backend
 Crear CookController (registro, perfil, platos)
 Crear DishController (CRUD de platos)
 Crear CustomerController (perfil, pedidos)
 Crear OrderController (flujo completo de pedidos)
 Crear ReviewController (calificaciones)
 Integrar geolocalización (Google Maps / Leaflet)

## Fase 7: Vistas Frontend
 Landing page para la plataforma
 Formulario de registro para cocineros
 Panel del cocinero (gestión de platos)
 Catálogo con mapa de cocineros cercanos
 Vista de perfil de cocinero
 Carrito y checkout
 Seguimiento de pedidos
 Sistema de reviews

## Fase 8: Geolocalización
 Implementar servicio de geolocalización
 Calcular distancias entre cocinero y cliente
 Filtrar cocineros por radio de cobertura
 Mostrar mapa con puntos de cocineros

## Fase 9: Integración de Pagos
 Configurar MercadoPago (ya existe, adaptar)
 Implementar sistema de comisiones (10-15%)
 Sistema de acreditación a cocineros

## Fase 10: Sistema de Estados de Pedidos
 Implementar máquina de estados
 Notificaciones por email
 Notificaciones por WhatsApp (opcional)
 Panel de seguimiento en tiempo real

## Fase 11: Testing y Validación
 Probar flujo completo de registro de cocinero
 Probar publicación y búsqueda de platos
 Probar proceso de pedido completo
 Probar geolocalización
 Probar sistema de pagos
 Probar sistema de reviews

## Fase 12: Documentación
 Actualizar README.md
 Crear manual de uso para cocineros
 Crear manual de uso para clientes
 Documentar configuración de geolocalización



# Manual Verification

## Flujo de Registro de Cocinero
- Navegar a /register/cook
- Completar formulario con datos de prueba
- Subir fotos (DNI + 3 fotos de cocina)
- Verificar que el registro quede pending approval
- Como admin, aprobar el cocinero
- Verificar que puede acceder a /cook/dashboard

## Flujo de Publicación de Platos
- Login como cocinero
- Ir a /cook/dishes/create
- Crear 3 platos con fotos
- Verificar que aparezcan en el marketplace

## Flujo de Búsqueda por Geolocalización
- Abrir /marketplace
- Permitir geolocalización del navegador
- Verificar que el mapa muestre marcadores de cocineros cercanos
- Aplicar filtros (precio, dieta, distancia)
- Hacer clic en un cocinero y ver su perfil

## Flujo Completo de Pedido
- Seleccionar un plato
- Ir a checkout
- Elegir método de entrega (retiro/delivery)
- Pagar con MercadoPago (usar credenciales de test)
- Verificar que el cocinero recibe notificación
- Como cocinero, aceptar el pedido
- Cambiar estado a "en preparación"
- Marcar como "listo para retiro" o "en camino"
- Como cliente, verificar seguimiento en /orders
- Completar entrega
- Dejar review

## Verificación de Comisiones
- Verificar que al crear un pedido se calcule la comisión correctamente
- Verificar que en el panel admin se visualicen las comisiones

# Diagrama de Flujos

## 🔧 1. Flujo completo del usuario (cliente)
[Inicio]
   ↓
Usuario abre app/web
   ↓
¿Está registrado?
   ├─ No → Registro/Login
   └─ Sí → Home
   ↓
Home (lista de cocineros cercanos)
   ↓
Selecciona un cocinero
   ↓
Ve menú disponible
   ↓
Selecciona plato
   ↓
¿Retiro o Delivery?
   ├─ Retiro → Mostrar dirección y horarios
   └─ Delivery → Calcular costo + tiempo estimado
   ↓
Checkout
   ↓
Pago (MercadoPago)
   ↓
¿Pago OK?
    ├─ NO → Error / Reintentar
    └─ SÍ → Pedido generado
    ↓
¿Tipo de pedido?
    ├─ Inmediato → Flujo normal
    └─ Programado → Validar horas cocinero + stock porción diaria
    ↓
    Notificación al cocinero
   ↓
Cocinero acepta / rechaza
   ├─ Rechaza → Notificar cliente / devolución
   └─ Acepta → En preparación
   ↓
Si es Delivery → Asignar repartidor (APP externa o freelance)
   ↓
Pedido en camino
   ↓
Entrega completada
   ↓
Reseña del cliente
   ↓
[Fin]


## 👨‍🍳 2. Flujo del cocinero (alta, platos, pedidos)
[Inicio]
   ↓
Cocinero se registra
   ↓
Sube fotos de cocina + DNI + datos bancarios
   ↓
Admin aprueba / rechaza
   ├─ Rechaza → Pedir corrección
   └─ Aprueba → Cocinero activo
   ↓
Cocinero crea menú
   ↓
Publica platos (foto + precio + stock)
   ↓
Cliente hace pedido
   ↓
Cocinero recibe notificación
   ↓
¿Acepta el pedido?
   ├─ NO → informar al cliente
   └─ SÍ → Cambiar estado a "En preparación"
   ↓
¿Modo de entrega?
   ├─ Retiro → Mostrar horarios
   └─ Delivery → Coordinar con repartidor externo
   ↓
Pedido completado
   ↓
Ingresos se acreditan en la cuenta del cocinero (menos comisión)
   ↓
[Fin]


## 🛵 3. Flujo del repartidor (solo si lo agregan)
Si tercerizan con mensajerías o motociclistas freelance, este flujo queda simple.

Pedido listo → Notificación al repartidor
   ↓
Aceptar carrera
   ↓
Retira vianda en cocina
   ↓
Entrega al cliente
   ↓
Sube confirmación (foto/OK)
   ↓
Listo


## 🧱 4. Flujo interno del sistema (backoffice)
[Inicio]
   ↓
Admin recibe nuevas solicitudes de cocineros
   ↓
Validación rápida
   ↓
Aprobación
   ↓
Monitoreo de pedidos en curso
   ↓
Gestión de reclamos
   ↓
Revisión de estadísticas:
   - Ventas por cocinero
   - Platos más pedidos
   - Zonas con más demanda
   - Calificaciones
   ↓
Pagos a cocineros
   ↓
[Fin]


## 🗺️ 5. Flujo completo del pedido (vista global)
Cliente elige plato
   ↓
→ Pedido creado
   ↓
→ Pago aprobado
   ↓
→ Cocinero notificado
   ↓
Cocinero:
      ¿Acepta?
        ↓
      Sí
        ↓
   Cambia a "Preparación"
        ↓
   Sistema notifica al cliente
        ↓
   ¿Delivery?
        ├─ No (Retiro)
        └─ Sí → Asignar repartidor
                ↓
             Repartidor retira
                ↓
             En camino
                ↓
             Entregado
                     ↓
                  Cliente califica
## 📦 6. Flujo técnico del backend (APIs y estados)

Esto te sirve para empezar a armar la base de datos y la API:

Estados del pedido
pending_payment

paid

awaiting_cook_acceptance

rejected_by_cook

preparing

ready_for_pickup

assigned_to_delivery

on_the_way

delivered

cancelled

scheduled [NEW]



# Diagrama de Tablas

           ┌──────────────────────┐
           │        USERS         │
           │──────────────────────│
           │ id PK                │
           │ name                 │
           │ email                │
           │ password_hash        │
           │ role                 │
           │ phone                │
            │ address              │
            └─────────┬────────────┘
                      │ 1–N
                      │
            ┌─────────▼───────────┐
            │   FAVORITE_COOKS    │
            │─────────────────────│
            │ user_id FK          │
            │ cook_id FK          │
            └─────────────────────┘
                     │ N–1
                     │
           ┌─────────▼───────────┐
           │        COOKS        │
           │─────────────────────│
           │ id PK (FK → users)  │
           │ bio                 │
           │ kitchen_photos (json│
           │ rating_avg          │
           │ rating_count        │
           │ location_lat        │
            │ location_lng        │
            │ max_scheduled_portions_per_day │ [NEW]
            └─────────┬───────────┘
                     │ 1–N
                     │
           ┌─────────▼───────────┐
           │       DISHES         │
           │──────────────────────│
           │ id PK                │
           │ cook_id FK           │
           │ name                 │
           │ description          │
           │ price                │
           │ photo_url            │
            │ available_stock      │
            │ is_schedulable      │ [NEW]
            └─────────┬────────────┘
                     │ 1–N
                     │
           ┌─────────▼───────────┐
           │       ORDER_ITEMS    │
           │──────────────────────│
           │ id PK                │
           │ order_id FK          │
           │ dish_id FK           │
           │ quantity             │
           │ unit_price           │
           └─────────┬────────────┘
                     │ N–1
                     │
           ┌─────────▼───────────┐
           │        ORDERS        │
           │──────────────────────│
           │ id PK                │
           │ customer_id FK       │
           │ cook_id FK           │
           │ status               │
           │ delivery_type        │
           │ delivery_address     │
            │ total_amount         │
            │ payment_id           │
            │ scheduled_time       │ [NEW]
            │ notes (text)         │ [NEW]
            └─────────┬────────────┘
                     │ 1–1
                     │
           ┌─────────▼───────────┐
           │       REVIEWS        │
           │──────────────────────│
           │ id PK                │
           │ order_id FK          │
           │ customer_id FK       │
           │ cook_id FK           │
           │ rating               │
           │ comment              │
           └──────────────────────┘

            ┌──────────────────────┐
            │      ORDER_LOGS      │
            │──────────────────────│
            │ id PK                │
            │ order_id FK          │
            │ user_id FK           │
            │ status               │
            │ event                │
            │ description          │
            │ metadata (json)      │
            └──────────────────────┘

            OPTIONAL DELIVERY MODULE

           ┌────────────────────────┐
           │   DELIVERY_ASSIGNMENTS │
           │────────────────────────│
           │ id PK                  │
           │ order_id FK            │
           │ delivery_user_id FK    │
           │ status                 │
           │ location_tracking json │
           └────────────────────────┘


📊 Nuevos Controladores Implementados
1. MarketplaceController
- index() - Landing page
- catalog() - Catálogo con filtros (lat, lng, radius, diet, price)
- cookProfile($cookId) - Perfil del cocinero
- dishDetail($dishId) - Detalle del plato
- nearbyCooks() - API JSON para mapa
2. CookDashboardController
- index() - Dashboard con estadísticas
- createProfile() / storeProfile() - Alta de cocinero
- editProfile() / updateProfile() - Edición
- deleteKitchenPhoto() - Gestión de fotos
3. DishController (Resource)
- index() - Lista de platos
- create() / store() - Crear plato con foto
- edit() / update() - Editar plato
- destroy() - Eliminar plato
- toggleActive() - Activar/desactivar (AJAX)
- updateStock() - Actualizar stock (AJAX)
4. OrderController ⭐
- cart() - Ver carrito
- addToCart() - Agregar al carrito con validaciones
- removeFromCart() - Remover item
- checkout() - Vista de checkout
- processCheckout() - Procesar pedido + decrementar stock
- success() - Página de confirmación
- myOrders() - Pedidos del cliente
- cookOrders() - Pedidos del cocinero
- show() - Detalle del pedido
- accept() / reject() - Aceptar/rechazar (cocinero)
- updateStatus() - Cambiar estado
5. ReviewController
- store() - Crear review
- cookReviews() - Ver reviews de un cocinero
6. FavoriteController ⭐ [NEW]
- index() - Lista de cocineros favoritos del cliente
- toggle() - Alternar favorito (AJAX)
7. AdminController
- index() - Dashboard con métricas
- pendingCooks() - Cocineros pendientes
- showCook() - Ver solicitud
- approveCook() / rejectCook() - Aprobar/rechazar
- allCooks() / allOrders() - Listados
- statistics() - Estadísticas avanzadas
🎨 Vistas Frontend Creadas
1. layouts/app.blade.php
Navbar sticky con gradiente border
Logo animado 🍲
Cart badge con contador
User dropdown menu
Flash messages con gradientes
Footer oscuro con gradientes
6. Toasts & Notifications [NEW]
- Sistema de Toasts flotantes para feedback inmediato (Carrito, Favoritos).
- Animaciones de entrada/salida fluida.
7. Navigation Enhancements [NEW]
- Enlaces rápidos a "Favoritos" en Navbar y Dropdown.
- Botones de "Volver al Explorador" en Vistas de Pedidos y Favoritos.
2. marketplace/landing.blade.php
Hero section con grid de platos animados
Stats (150+ cocineros, 2,500+ pedidos)
Sección "Cómo Funciona" (3 pasos)
Sección para cocineros con beneficios
CTA final con gradiente full-width
Animaciones con blur effects
3. marketplace/catalog.blade.php ⭐
Mapa Leaflet interactivo (500px)
Filtros en sidebar sticky:
Geolocation button
Radio slider (1-50km)
Filtro de dieta
Precio máximo
Cards de cocineros con:
Fotos de cocina
Rating con estrellas
Distancia badge
Preview de platos
JavaScript para markers dinámicos
4. cook/dashboard.blade.php
4 Stats cards con gradientes:
Total Orders (blue → indigo)
Pending (orange → pink)
Today (purple → pink)
Revenue (green → emerald)
Quick Actions sidebar
Profile status toggle
Recent activity feed
5. orders/cart.blade.php
Lista de items con fotos
Remove button por item
Summary sticky con:
Subtotal
Item count
Total destacado
Botón de checkout con gradiente
📊 Paleta de Colores Implementada
/* Gradientes Principales */
Orange → Pink → Purple  /* Branding, CTAs, borders */
Blue → Indigo           /* Info, secundario */
Green → Emerald         /* Success, revenue */
Yellow → Orange         /* Warning, pending */
Purple → Pink           /* Premium, cocinero */
/* Backgrounds */
from-orange-50 via-pink-50 to-purple-50  /* Body gradient */
🛣️ Estructura de Rutas Completa
/                                   → Landing page
/marketplace/catalog                → Catálogo con mapa
/marketplace/cook/{id}              → Perfil de cocinero
/marketplace/dish/{id}              → Detalle de plato
/marketplace/api/nearby-cooks       → API para mapa
/cart                               → Carrito
/orders/checkout                    → Checkout
/orders/my-orders                   → Mis pedidos
/orders/{id}                        → Detalle del pedido
/cook/dashboard                     → Dashboard cocinero
/cook/profile/create                → Alta de cocinero
/cook/dishes                        → Gestión de platos
/cook/orders                        → Pedidos del cocinero
/admin/dashboard                    → Dashboard admin
/admin/cooks/pending                → Aprobar cocineros
/admin/orders                       → Ver todos los pedidos
/admin/statistics                   → Estadísticas
Middleware aplicado:

cook
 → Rutas de cocinero
admin → Rutas de administrador
🔧 Comandos Útiles Ejecutados
# Limpieza de base de datos y ejecución de migraciones
php artisan migrate:fresh
# Crear modelos
php artisan make:model Cook
php artisan make:model Dish
php artisan make:model Order
php artisan make:model OrderItem
php artisan make:model Review
php artisan make:model DeliveryAssignment
📝 Notas Técnicas
Geolocalización
Método: Fórmula de Haversine para cálculo de distancias
Herramienta de mapas: Leaflet + OpenStreetMap (gratis)
Campos: location_lat, location_lng con precisión de 8 decimales (~1.1mm)
Modelo de Negocio
Comisión: 10-15% por transacción
Delivery: Tercerizado para MVP (no gestionamos repartidores propios inicialmente)
Estados de Pedidos Implementados
pending_payment - Esperando pago
paid - Pagado
awaiting_cook_acceptance - Esperando que el cocinero acepte
rejected_by_cook - Rechazado por el cocinero
preparing - En preparación
ready_for_pickup - Listo para retiro
assigned_to_delivery - Asignado a delivery
on_the_way - En camino
delivered - Entregado
cancelled - Cancelado
scheduled - Programado (aceptado por cocinero para fecha futura)
⚠️ Advertencias del Linter (No críticas)
Hay un warning de tipo estático en Order::calculateCommission() relacionado con la conversión de float a decimal. Esto es un falso positivo - Laravel maneja automáticamente la conversión a través del sistema de casts definido en el modelo. El código funciona correctamente en runtime.

🚀 Estado del Servidor
El servidor está corriendo:

php artisan serve
# Corriendo en http://localhost:8000
✨ Logros Clave
Base de datos completamente normalizada siguiendo el diagrama ER proporcionado
Modelos con lógica de negocio robusta incluyendo:
Máquina de estados para pedidos
Sistema de geolocalización
Actualización automática de ratings
Manejo de inventario
Relaciones Eloquent perfectamente definidas entre todos los modelos
Preparado para escalar con arquitectura limpia y separación de responsabilidades
Fecha: 2025-12-06
Progreso: 6 de 12 fases completadas (50%)
Próximo hito: Crear vistas frontend con Blade templates y Leaflet.js para mapas

BACKEND 100% FUNCIONAL ✅
Solo faltan las vistas frontend para tener una aplicación completamente usable.



# FEATURES DE SUSCRIPCIONES

Plan FREE

- Publicar platos
- Recibir pedidos
- Reviews básicas

Plan PREMIUM

- Panel de ventas
- Posicionamiento destacado + badge “Cocinero Premium”
- Notificaciones a clientes
- Posibilidad de subir ofertas como 2x1

---
# SISTEMA DE FAVORITOS [NEW]
- **Mecanismo**: Toggle AJAX con icono de corazón.
- **Feedback**: Toast notification ("¡Agregado!") + Cambio de color (Rojo/Lleno).
- **Consistencia**: El estado se sincroniza en catálogo y perfil del cocinero.

# SISTEMA DE PEDIDOS PROGRAMADOS [NEW]
- **Validaciones**:
    - Horarios de atención: Se valida que la hora este entre `opening_time` y `closing_time`.
    - Aptitud del plato: El plato debe tener `is_schedulable = true`.
    - Capacidad diaria: Valida que no se supere `max_scheduled_portions_per_day`.
- **Flujo**:
    - Una vez aceptado por el cocinero, el pedido pasa a estado `scheduled`.
    - Notifica al cliente mediante `OrderStatusUpdated` event.
# SISTEMA DE NOTIFICACIONES PUSH (FCM) [NEW]
- **Motor**: Firebase Cloud Messaging (FCM).
- **Backend**: Integración con `kreait/laravel-firebase` y canal personalizado `WebPushChannel`.
- **Automatización**:
    - **Cocinero**: Alerta de "Nuevo Pedido" instantánea al pagar el comensal.
    - **Cliente**: Actualizaciones de estado en tiempo real (Preparando, Listo, En Camino, etc.).
- **Frontend**:
    - **Service Worker**: Gestión en segundo plano (background) para móviles y PC.
    - **In-App Toast**: Sistema de avisos visuales internos para 100% de visibilidad incluso con notificaciones de sistema bloqueadas.
- **Mobile Support**: Preparado para PWA en Android e iOS (vía "Añadir a pantalla de inicio").

# SISTEMA DE SUSCRIPCIONES
Guía de implementación del sistema de suscripción de Cook
El MVP del sistema de suscripción de Cook se ha implementado correctamente en el Marketplace de Cocinarte. Este documento resume la arquitectura, las nuevas funciones y los cambios realizados en la aplicación.

1. Base de datos y arquitectura
Se implementó un modelo de suscripción híbrido, compuesto por una implementación local personalizada ( SubscriptionPlan y CookSubscription) diseñada para funcionar con Laravel Cashier.

Nuevas tablas:
subscription_plans: Almacena detalles de los planes disponibles (nombre, precio, moneda, período de facturación, límites de ventas/pedidos, porcentaje de comisión y características codificadas en JSON).
cook_subscriptions: Asocia a los cocineros con planes, estado de seguimiento y períodos de facturación.
Actualizaciones de la tabla Cooks: Se añadieron las columnas de seguimiento: current_subscription_id, monthly_sales_accumulated, monthly_orders_accumulated, sales_reset_at y is_selling_blocked.

2. Funciones de administración
Los administradores ahora pueden gestionar completamente el ecosistema de suscripciones.

Gestión de planes (AdminSubscriptionPlanController): CRUD completo para planes de suscripción mediante las rutas admin.subscription-plans.*.
Configuración de funciones premium: Los administradores pueden definir funciones premium mediante JSON en el formulario de creación de planes (por ejemplo, "Cocinero Premium" y "Destacado en Búsquedas").
Interfaz de usuario: Se añadió la sección "Planes de Suscripción" a la barra lateral de administración (layouts/admin.blade.php ).


3. Experiencia de cocina y facturación: Los cocineros pueden ver su consumo actual y administrar su plan.

Integración del panel: Se añadió el enlace "Mi suscripción" a la sección de acciones rápidas del panel de control de cocina.
Gestión de suscripciones (CookSubscriptionController ): Los cocineros pueden ver su plan activo, controlar el consumo mensual actual en relación con los límites y explorar las actualizaciones disponibles.
Flujo de pago: Se implementó un proceso de pago simulado ( checkout.blade.php ), preparando el sistema para los webhooks de Stripe/MercadoPago. 4. 

Lógica de negocio y middleware
El motor de reglas principal que aplica las restricciones de suscripción:

Seguimiento de métricas: El OrderController incrementa las métricas de ventas y recuento de pedidos del cocinero al completarse el pedido mediante el método incrementMetricsAndCheckLimits del modelo Cook.

Aplicación de límites (EnsureCookCanSell Middleware): Este middleware encapsula las rutas cart.add y orders.checkout. Si un cocinero excede los límites de su plan, se bloquean los pedidos posteriores a ese cocinero y el usuario recibe un mensaje de error.

Reinicio mensual: Se creó un comando artesanal ResetMonthlyCookMetrics
(cooks:reset-monthly-metrics) y se programó para ejecutarse automáticamente el 1 de cada mes para restablecer las ventas, los pedidos y desbloquear cocineros.

5. Funciones premium (Frontend)
Las funciones definidas en los planes de suscripción afectan directamente la visibilidad del marketplace.

Insignia Premium: Los cocineros con la función premium_badge muestran una insignia dorada visual "Premium" en sus tarjetas de perfil en el catálogo público (marketplace/catalog.blade.php).
Lista de Prioridad: Se actualizó la lógica de consulta MarketplaceController@catalog para usar una combinación izquierda (LEFT JOIN) en la columna de características JSON, priorizando a los cocineros con priority_listing = true en la parte superior de los resultados de búsqueda.

6. Pruebas y Migración
Semillado de Datos: Se creó un SubscriptionPlanSeeder para completar la base de datos con un plan "Básico (GRATIS)" y uno "Premium".
Migración de Datos: La migración de assign_default_plan_to_cooks inscribió automáticamente a todos los cocineros existentes en el plan "Básico (GRATIS)" con límites iniciales (20 pedidos, 50.000 ARS).
Pruebas automatizadas: Se realizaron pruebas exhaustivas de funciones en SubscriptionFlowTest para validar los incrementos de métricas, el bloqueo de límites, la creación de planes de administración y el comando de reinicio mensual. Todas las pruebas se superaron correctamente.

7. Phase 12: Integración Real de Pagos (Stripe & MercadoPago)
Se ha implementado el flujo completo de pago para las suscripciones de los cocineros:

Selección de Pasarela: El cocinero ahora puede elegir entre Stripe (Tarjetas Internacionales) y MercadoPago (Pagos Locales) si el administrador ha configurado las llaves.
Redirección a Checkout Real:
Stripe: Utiliza Laravel Cashier para crear sesiones de Checkout seguras vinculadas al stripe_price_id del plan.
MercadoPago: Utiliza el SDK v3 para crear Preferencias de pago vinculadas al mp_plan_id o el precio del plan.
Confirmación Automática (Webhooks): Se ha creado el PaymentWebhookController para recibir notificaciones de MercadoPago y activar la suscripción automáticamente en la base de datos.
Seguridad: Se han excluido las rutas de webhooks de la protección CSRF en bootstrap/app.php.

8. Phase 13: Historial de Pagos y Acreditaciones
Para dar transparencia al proceso de cobro recurrente, se implementó un sistema de seguimiento de transacciones:

Historial del Cocinero
Los cocineros tienen un nuevo botón "Historial de Pagos" en su panel de suscripción.
Pueden ver la fecha, el plan adquirido, el monto pagado (en ARS/USD), el método de pago y el ID de referencia de la plataforma.
Panel de Control Administrativo
Nueva sección: Pagos y Recaudación.
Dashboard de Ingresos: Visualización del total recaudado y desglose por plataforma (Stripe vs MercadoPago).
Control Detallado: Listado global de todas las acreditaciones por usuario en tiempo real.
NOTE

Cada vez que una plataforma (Stripe o MercadoPago) confirma un pago a través de un webhook, el sistema registra automáticamente una entrada en la tabla subscription_payments, asegurando que el historial esté siempre actualizado sin intervención manual.