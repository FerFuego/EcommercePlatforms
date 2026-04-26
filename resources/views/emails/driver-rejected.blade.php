<!DOCTYPE html>
<html>
<head>
    <title>Actualización sobre tu solicitud</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
    <div style="background: linear-gradient(135deg, #6b7280, #374151); padding: 30px; border-radius: 16px; text-align: center; margin-bottom: 20px;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Actualización de tu Solicitud</h1>
        <p style="color: rgba(255,255,255,0.8); margin-top: 8px; font-size: 14px;">Cocinarte</p>
    </div>

    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #374151;">Hola <strong>{{ $driver->user->name }}</strong>,</p>

        <p style="font-size: 16px; color: #374151;">
            Lamentablemente, luego de revisar tu solicitud como repartidor en Cocinarte, hemos decidido no aprobarla en esta oportunidad.
        </p>

        <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin: 20px 0;">
            <p style="font-size: 14px; color: #991b1b; margin: 0;">
                <strong>Motivo:</strong> {{ $rejectionReason }}
            </p>
        </div>

        <p style="font-size: 14px; color: #6b7280;">
            Si crees que esto fue un error o deseas enviar una nueva solicitud con más información, no dudes en contactarnos.
        </p>

        <p style="font-size: 14px; color: #6b7280;">
            ¡Gracias por tu interés en Cocinarte! 🙏
        </p>
    </div>

    <p style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 20px;">
        Cocinarte — Comida casera de tu barrio 🍲
    </p>
</body>
</html>
