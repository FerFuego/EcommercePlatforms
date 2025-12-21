# 🧪 Guía de Testing - Cocinarte Marketplace

## 📊 Datos de Prueba Creados

### Usuarios Admin
- **Email:** `admin@cocinarte.com`
- **Password:** `password`
- **Acceso:** Panel de administración

### Cocineros (5 perfiles aprobados)
| **Nombre** | **Email** | **Especialidad** | **Ubicación** |
|------------|----------|------------------|---------------|
| Doña Rosa Cocina Casera | `rosa@cocinarte.com` | Comida tradicional | -32.1745, -63.2963 |
| Mario Pastas Frescas | `mario@cocinarte.com` | Pastas italianas | -32.1756, -63.2945 |
| Veggie Delight | `veggie@cocinarte.com` | Cocina vegana | -32.1723, -63.2988 |
| Parrilla Don Carlos | `carlos@cocinarte.com` | Asados | -32.1768, -63.2931 |
| Comida Fit by Lucía | `lucia@cocinarte.com` | Comida fitness | -32.1732, -63.2975 |

**Password para todos:** `password`

### Clientes (8 usuarios)
- juan.perez@example.com
- maria.gonzalez@example.com
- carlos.rodriguez@example.com
- ana.martinez@example.com
- luis.fernandez@example.com
- laura.sanchez@example.com
- diego.lopez@example.com
- sofia.garcia@example.com

**Password para todos:** `password`

### Platos Creados (20 platos)
- **Doña Rosa:** Guiso de Lentejas, Estofado de Carne, Ñoquis Caseros, Pastel de Papa
- **Mario:** Ravioles, Lasagna, Sorrentinos, Tallarines Carbonara
- **Veggie:** Bowl Vegano, Hamburguesa de Lentejas, Curry de Garbanzos, Ensalada Buddha
- **Don Carlos:** Asado, Choripán, Empanadas, Vacío a la Parrilla
- **Lucía Fit:** Pechuga Grillada, Salmón con Quinoa, Ensalada Proteica, Bowl Fitness

### Pedidos (15 pedidos variados)
- Estados: `delivered`, `preparing`, `awaiting_cook_acceptance`, `ready_for_pickup`
- Métodos de pago: MercadoPago, Efectivo, Transferencia
- Tipos: Delivery y Retiro

---

## 🧪 Flujos de Prueba

### 1. Exploración Pública (Sin Login)
1. Ir a `http://localhost:8000`
2. Ver landing page
3. Click en "Explorar Cocineros"
4. Ver mapa con 5 marcadores de cocineros
5. Usar filtros: radio, dieta, precio
6. Click en un cocinero → Ver perfil completo

### 2. Registro y Login como Cliente
1. Ir a `/register`
2. Crear cuenta con rol "Customer"
3. Login con credenciales
4. Explorar marketplace

### 3. Flujo Completo de Compra (Cliente)
**Usuario de prueba:** `juan.perez@example.com / password`

1. **Explorar**
   - Ir a `/marketplace/catalog`
   - Ver cocineros en el mapa
   - Click en "Doña Rosa"

2. **Agregar al Carrito**
   - Ver perfil de Rosa
   - Agregar "Guiso de Lentejas" (cantidad 2)
   - Click en carrito (badge debe mostrar 1  item)

3. **Checkout**
   - Ir a `/cart`
   - Click "Proceder al Pago"
   - Seleccionar "Retiro" o "Delivery"
   - Elegir método de pago
   - Confirmar pedido

4. **Success & Tracking**
   - Ver página de confirmación
   - Ir a "Mis Pedidos"
   - Ver detalle del pedido

5. **Calificar**
   - Si el pedido está "delivered", click en "Calificar"
   - Dar estrellas y comentario

### 4. Flujo del Cocinero
**Usuario de prueba:** `rosa@cocinarte.com / password`

1. **Dashboard**
   - Login como Rosa
   - Ver estadísticas: Total, Pendientes, Hoy, Ingresos
   - Ver actividad reciente

2. **Gestionar Platos**
   - Ir a "Mis Platos"
   - Ver grid de platos con fotos
   - Modificar stock con +/-
   - Toggle active/inactive
   - Click "Nuevo Plato"

3. **Crear Plato Nuevo**
   - Completar formulario
   - Subir foto (opcional)
   - Seleccionar días disponibles
   - Agregar diet tags
   - Guardar

4. **Gestionar Pedidos**
   - Ir a "Ver Pedidos"
   - Filtrar por estado: Pendientes, En Preparación, Completados
   - Ver pedidos "Esperando Confirmación"
   - Click "Aceptar" o "Rechazar"
   - Actualizar estado a "Listo"

### 5. Panel de Admin
**Usuario:** `admin@cocinarte.com / password`

1. Login como admin
2. Ir a `/admin/dashboard`
3. Ver métricas globales
4. Gestionar cocineros pendientes
5. Ver todos los pedidos
6. Ver estadísticas

---

## 🗺️ Testing de Geolocalización

### Coordenadas de Bell Ville
**Centro:** `-32.1745, -63.2963`

### Test del Mapa
1. Ir a `/marketplace/catalog`
2. Click en "Usar Mi Ubicación" (navegador pedirá permiso)
3. O ingresar manualmente: lat `-32.1745`, lng `-63.2963`
4. Ajustar radio: 1-50 km
5. Ver markers aparecer/desaparecer según radio

### Cocineros por Distancia (desde centro)
- Veggie Delight: ~2.8 km
- Doña Rosa: ~0.3 km
- Mario: ~1.6 km
- Don Carlos: ~3.1 km
- Lucía Fit: ~1.4 km

---

## 🎨 Funcionalidades Visuales a Testear

### Diseño
✅ Gradientes vibrantes (orange → pink → purple)
✅ Animaciones hover en cards
✅ Transitions suaves
✅ Shadows dinámicas
✅ Responsive design

### Componentes Interactivos
- **Mapa:** Zoom, pan, click en markers
- **Filtros:** Sliders, dropdowns, checkboxes
- **Carrito:** Badge contador actualizado
- **Stock:** +/- AJAX sin reload
- **Toggle Active:** Switch instantáneo
- **Review Modal:** Star rating interactivo
- **Flash Messages:** Success/error con gradientes

---

## 📱 Testing Responsive

### Breakpoints a Probar
- **Mobile:** 320px - 640px
- **Tablet:** 641px - 1024px
- **Desktop:** 1025px+

### Vistas Críticas
1. Landing page
2. Catálogo con mapa
3. Perfil de cocinero
4. Carrito
5. Checkout
6. Dashboard

---

## ⚠️ Casos de Error a Testear

### Validaciones
1. Crear plato sin foto
2. Stock negativo
3. Checkout sin items en carrito
4. Review sin rating
5. Cantidad mayor al stock

### Estados Inválidos
1. Aceptar pedido ya aceptado
2. Calificar pedido no delivered
3. Acceder a dashboard cook sin ser cook

---

## 🚀 Comandos Útiles

```bash
# Resetear base de datos y re-seed
php artisan migrate:fresh --seed

# Ver rutas
php artisan route:list

# Cache clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Server
php artisan serve
```

---

## 📊 Métricas Pre-cargadas

- **Usuarios:** 14 (1 admin + 8 clientes + 5 cooks)
- **Cocineros Activos:** 5
- **Platos Disponibles:** 20
- **Pedidos:** 15
- **Reviews:** ~7-8 (pedidos delivered con review)

---

## ✅ Checklist de Testing

### Frontend
- [ ] Landing page carga correctamente
- [ ] Mapa muestra markers
- [ ] Filtros funcionan
- [ ] Carrito agrega items
- [ ] Checkout procesa pedido
- [ ] Success page muestra resumen
- [ ] Mis pedidos lista correctamente
- [ ] Review modal funciona

### Cocinero
- [ ] Dashboard muestra stats
- [ ] Lista de platos funciona
- [ ] Crear/editar plato
- [ ] Stock +/- AJAX
- [ ] Toggle active
- [ ] Pedidos con filtros
- [ ] Accept/reject funciona

### Admin
- [ ] Dashboard de admin
- [ ] Ver cocineros
- [ ] Ver pedidos
- [ ] Estadísticas

### Geolocalización
- [ ] Nearby search funciona
- [ ] Distancias correctas
- [ ] Markers clickeables
- [ ] Filtro por radio

---

**🎉 ¡Aplicación Lista para Testing!**
