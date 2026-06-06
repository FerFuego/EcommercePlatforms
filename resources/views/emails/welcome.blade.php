<!DOCTYPE html>
<html>
<head>
    <title>¡Bienvenido a Cocinarte!</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
    <div style="background: linear-gradient(135deg, #f97316, #ec4899); padding: 30px; border-radius: 16px; text-align: center; margin-bottom: 20px;">
        <h1 style="color: white; margin: 0; font-size: 28px;">¡Bienvenido a Cocinarte! 🍽️</h1>
        <p style="color: rgba(255,255,255,0.9); margin-top: 8px; font-size: 16px;">Estamos felices de tenerte con nosotros</p>
    </div>

    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #374151;">Hola <strong>{{ $user->name }}</strong>,</p>

        <p style="font-size: 16px; color: #374151; line-height: 1.5;">
            Gracias por unirte a nuestra comunidad. En Cocinarte conectamos la auténtica comida casera de tu barrio directamente con tu mesa.
        </p>

        @if($user->role === 'customer')
            <h3 style="color: #f97316; margin-top: 24px;">🎯 ¿Qué puedes hacer ahora?</h3>
            <ol style="color: #374151; line-height: 2;">
                <li>Explorar los cocineros cerca de ti</li>
                <li>Descubrir platos caseros y hacer tu primer pedido</li>
                <li>Guardar tus comidas favoritas</li>
            </ol>
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('marketplace.catalog') }}" style="background: linear-gradient(135deg, #f97316, #ec4899); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">
                    Explorar Comida 🍕
                </a>
            </div>
        @elseif($user->role === 'cook')
            <h3 style="color: #ec4899; margin-top: 24px;">👨‍🍳 Próximos pasos para Cocineros:</h3>
            <ol style="color: #374151; line-height: 2;">
                <li>Completa tu perfil en el Panel de Cocinero</li>
                <li>Espera la validación de un administrador</li>
                <li>Sube tus mejores platos y comienza a vender</li>
            </ol>
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('cook.dashboard') }}" style="background: linear-gradient(135deg, #f97316, #ec4899); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">
                    Ir a Mi Panel 🍳
                </a>
            </div>
        @elseif($user->role === 'delivery_driver')
            <h3 style="color: #8b5cf6; margin-top: 24px;">🚴 Próximos pasos para Repartidores:</h3>
            <ol style="color: #374151; line-height: 2;">
                <li>Ingresa a tu Panel de Repartidor</li>
                <li>Completa tus datos personales y vehículo</li>
                <li>Espera la aprobación de un administrador para empezar a hacer entregas</li>
            </ol>
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('delivery-driver.dashboard') }}" style="background: linear-gradient(135deg, #8b5cf6, #d946ef); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">
                    Ir a Mi Panel 🛵
                </a>
            </div>
        @else
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('home') }}" style="background: linear-gradient(135deg, #f97316, #ec4899); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">
                    Visitar Cocinarte
                </a>
            </div>
        @endif
    </div>

    <p style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 20px;">
        Cocinarte — Comida casera de tu barrio 🍲
    </p>
</body>
</html>
