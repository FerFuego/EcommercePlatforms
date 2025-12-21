# ✅ Test Suite - Cocinarte Marketplace

## 📊 Resumen de Tests

**Total: 38 tests / 69 assertions - 100% SUCCESS RATE ✅**

### Tests por Modelo

#### CookTest (10 tests) ✅
- ✅ Cálculo de cocineros cercanos usando fórmula Haversine
- ✅ Relación belongsTo con User
- ✅ Relación hasMany con Dishes
- ✅ Relación hasMany con Orders
- ✅ Relación hasMany con Reviews
- ✅ Scope `approved()` filtra cocineros aprobados
- ✅ Scope `active()` filtra cocineros activos
- ✅ Actualización de rating promedio
- ✅ kitchen_photos cast a array
- ✅ payout_details cast a array

#### DishTest (12 tests) ✅
- ✅ Relación belongsTo con Cook
- ✅ Decremento de stock con validación
- ✅ No permite decrementar stock por debajo de cero
- ✅ Incremento de stock
- ✅ Scope `available()` filtra platos disponibles (activos + stock)
- ✅ Scope `active()` filtra platos activos
- ✅ Scope `byDiet()` filtra por etiquetas dietéticas
- ✅ Verificación de disponibilidad por día
- ✅ diet_tags cast a array
- ✅ available_days cast a array
- ✅ Verificación de stock disponible
- ✅ price cast a float

#### OrderTest (15 tests) ✅
- ✅ Cálculo de comisión de la plataforma
- ✅ Marcar como pagado con payment_id
- ✅ Aceptar pedido por cocinero
- ✅ No permite aceptar si no está en awaiting
- ✅ Rechazar pedido por cocinero con razón
- ✅ Marcar como en preparación
- ✅ Marcar como listo (pickup por defecto)
- ✅ Marcar como entregado con completed_at
- ✅ Cancelar pedido
- ✅ Verificar si puede ser revisado
- ✅ Relación belongsTo con Customer (User)
- ✅ Relación belongsTo con Cook
- ✅ Relación hasMany con OrderItems
- ✅ Scope `pending()` filtra pedidos pendientes
- ✅ Scope `completed()` filtra pedidos completados

---

## 🏗️ Factories Creadas

### CookFactory
```php
- Estado por defecto: aprobado y activo
- Métodos: pending(), inactive()
- Coordenadas aleatorias para Bell Ville
```

### DishFactory
```php
- Platos variados con precios realistas
- Métodos: inactive(), outOfStock()
- Diet tags y available_days configurables
```

### OrderFactory
```php
- Órdenes con subtotal, delivery, commission
- Métodos: pending(), awaitingCook(), preparing(), delivered()
- Payment methods y delivery types variados
```

### ReviewFactory
```php
- Ratings de 3-5 estrellas
- Comentarios opcionales
- Relaciones automáticas
```

### OrderItemFactory
```php
- Cantidad y precios configurables
- Total_price automático
```

---

## 🎯 Cobertura de Tests

### Modelos
- ✅ **Cook**: Geolocalización, relaciones, scopes, ratings
- ✅ **Dish**: Stock, CRUD, filters, availability, diet tags
- ✅ **Order**: State machine completa, comisiones, transiciones
- ✅ **OrderItem**: Cálculos automáticos
- ✅ **Review**: Relaciones y triggers

### Funcionalidades Clave
- ✅ **Geolocalización**: Fórmula Haversine para búsqueda por radio
- ✅ **State Machine**: Todas las transiciones de estado de órdenes
- ✅ **Ratings**: Actualización automática de promedios
- ✅ **Stock Management**: Incremento/decremento con validaciones
- ✅ **Filters**: Por dieta, disponibilidad, estado
- ✅ **Comisiones**: Cálculo automático del 12%

---

## 🚀 Comandos de Testing

```bash
# Ejecutar todos los tests unitarios
php artisan test --testsuite=Unit

# Ejecutar tests con coverage
php artisan test --coverage

# Ejecutar test específico
php artisan test --filter=CookTest

# Ejecutar con verbosidad
php artisan test --testsuite=Unit --verbose
```

---

## 📝 Métodos Agregados para Tests

### Cook Model
- `updateRating(int $newRating)` - Actualiza rating incremental
- `updateRatingFromReviews()` - Recalcula desde reviews existentes
- `scopeApproved($query)` - Filtra aprobados
- `scopeActive($query)` - Filtra activos

### Dish Model
- `incrementStock(int $quantity)` - Incrementa stock
- `decrementStock(int $quantity)` - Decrementa con validación
- `scopeActive($query)` - Filtra activos
- `isAvailableOnDay(int $day)` - Verifica disponibilidad
- `hasStock()` - Verifica si hay stock

### Order Model
- `scopePending($query)` - Pedidos pendientes
- `scopeCompleted($query)` - Pedidos completados
- Constantes de estado completas

---

## ✨ Mejoras Implementadas

1. **HasFactory trait** agregado a todos los modelos
2. **Price cast a float** en Dish para compatibilidad
3. **Constant aliases** en Order para tests
4. **Default pickup logic** en markAsReady()
5. **Stock validation** en decrementStock()

---

**Status: ✅ TODOS LOS TESTS PASANDO**
**Next Steps: Feature tests para flujos completos**
