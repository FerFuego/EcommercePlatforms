<!DOCTYPE html>
<html>

<head>
    <title>{{ $feedback->type === 'error' ? 'Reporte de Error' : 'Nueva Sugerencia' }}</title>
</head>

<body>
    <h2>{{ $feedback->type === 'error' ? 'Reporte de Error' : 'Nueva Sugerencia' }} Recibido</h2>
    <p><strong>Cocinero:</strong> {{ $feedback->user->name }}</p>
    <p><strong>Email:</strong> {{ $feedback->user->email }}</p>
    <p><strong>Tipo:</strong> {{ ucfirst($feedback->type) }}</p>
    <p><strong>Fecha:</strong> {{ $feedback->created_at->format('d/m/Y H:i') }}</p>
    <hr>
    <p><strong>Mensaje:</strong></p>
    <p>{{ $feedback->message }}</p>
</body>

</html>