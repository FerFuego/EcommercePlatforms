<!DOCTYPE html>
<html>
<head>
    <title>¡Tu cuenta de repartidor fue aprobada!</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
    <div style="background: linear-gradient(135deg, #3b82f6, #6366f1); padding: 30px; border-radius: 16px; text-align: center; margin-bottom: 20px;">
        <h1 style="color: white; margin: 0; font-size: 28px;">🎉 ¡Bienvenido!</h1>
        <p style="color: rgba(255,255,255,0.9); margin-top: 8px; font-size: 16px;">Tu cuenta de repartidor fue aprobada</p>
    </div>

    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #374151;">Hola <strong>{{ $driver->user->name }}</strong>,</p>

        <p style="font-size: 16px; color: #374151;">
            ¡Bienvenido/a al equipo de repartidores de Cocinarte! Tu solicitud fue revisada y aprobada. Ya puedes empezar a aceptar pedidos de delivery.
        </p>

        <h3 style="color: #4f46e5; margin-top: 24px;">📋 Próximos pasos:</h3>
        <ol style="color: #374151; line-height: 2;">
            <li>Ingresá a tu <strong>Panel de Repartidor</strong></li>
            <li>Verificá que tu información de vehículo esté actualizada</li>
            <li>Activá las notificaciones para recibir avisos de nuevos pedidos</li>
            <li>¡Empezá a aceptar entregas!</li>
        </ol>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ url('/delivery/dashboard') }}" style="background: linear-gradient(135deg, #3b82f6, #6366f1); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">
                Ir a Mi Panel 🛵
            </a>
        </div>
    </div>

    <p style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 20px;">
        Cocinarte — Comida casera de tu barrio 🍲
    </p>
</body>
</html>
