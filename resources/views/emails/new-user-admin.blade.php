<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Registro en Cocinarte</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
    <div style="background: linear-gradient(135deg, #4f46e5, #9333ea); padding: 20px; border-radius: 12px 12px 0 0; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Nuevo Usuario Registrado 📢</h1>
    </div>

    <div style="background: white; padding: 30px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #374151;">Hola Administrador,</p>

        <p style="font-size: 16px; color: #374151;">Un nuevo usuario se ha registrado en la plataforma. Aquí tienes los detalles:</p>

        <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <p style="margin: 5px 0;"><strong>Nombre:</strong> {{ $user->name }}</p>
            <p style="margin: 5px 0;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 5px 0;"><strong>Rol:</strong> 
                @if($user->role === 'customer')
                    <span style="color: #ea580c; font-weight: bold;">Cliente</span>
                @elseif($user->role === 'cook')
                    <span style="color: #db2777; font-weight: bold;">Cocinero</span>
                @elseif($user->role === 'delivery_driver')
                    <span style="color: #7c3aed; font-weight: bold;">Repartidor</span>
                @else
                    <span style="color: #4b5563; font-weight: bold;">{{ ucfirst($user->role) }}</span>
                @endif
            </p>
            <p style="margin: 5px 0;"><strong>Fecha de registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('admin.users.index') }}" style="background: #4f46e5; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">
                Ir al Panel de Administración
            </a>
        </div>
    </div>
</body>
</html>
