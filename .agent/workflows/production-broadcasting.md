---
description: Mantener la configuración de Broadcasting en Producción (Hostinger)
---

Para asegurar que las actualizaciones en tiempo real (Pusher) sigan funcionando en producción sin un Queue Worker:

1. **Eventos y Notificaciones**: Siempre usa la interfaz `ShouldBroadcastNow` en los eventos y **NO** uses `ShouldQueue` en las clases de notificación (`OrderStatusNotification`, `NewOrderNotification`, etc.) para asegurar que se envíen de forma inmediata.
   ```php
   // En Eventos
   use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
   class MyEvent implements ShouldBroadcastNow { ... }

   // En Notificaciones (REMOVER ShouldQueue)
   class MyNotification extends Notification { ... }
   ```

2. **Compilación**: Si realizas cambios en `resources/js/echo.js` o `push-notifications.js`, debes ejecutar `npm run build` y subir la carpeta `public/build`.

3. **Variables de Entorno**: Verifica que las credenciales de Pusher en el `.env` de producción coincidan con las del dashboard de Pusher, especialmente el `PUSHER_APP_CLUSTER`.

4. **Firebase**: Si cambias de dominio, recuerda añadir el nuevo dominio en la consola de Firebase > Authentication > Settings > Authorized domains.
