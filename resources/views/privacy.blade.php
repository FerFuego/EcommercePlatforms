@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm p-8 md:p-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Políticas de Privacidad</h1>
        <p class="text-gray-500 mb-8">Última actualización: {{ date('d/m/Y') }}</p>

        <div class="prose prose-orange max-w-none text-gray-700 space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">1. Información que Recopilamos</h2>
            <p>En Cocinarte, recopilamos información personal que nos proporcionas directamente al registrarte en nuestra plataforma, ya sea como cliente, cocinero o repartidor. Esta información incluye, pero no se limita a:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Nombre completo y datos de contacto (correo electrónico, número de teléfono).</li>
                <li>Dirección física o ubicación (para la entrega de pedidos).</li>
                <li>Información de pago y transacciones.</li>
                <li>Comunicaciones, reportes o feedback enviados a través de nuestro sistema.</li>
            </ul>

            <h2 class="text-xl font-semibold text-gray-900">2. Uso de la Información</h2>
            <p>Utilizamos la información recopilada para los siguientes propósitos:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Procesar y gestionar pedidos entre clientes, cocineros y repartidores.</li>
                <li>Mejorar nuestra plataforma, personalizando tu experiencia.</li>
                <li>Enviar notificaciones importantes sobre tu cuenta, estado de pedidos o cambios en nuestros servicios.</li>
                <li>Prevenir fraudes y garantizar la seguridad de todos los usuarios de la comunidad.</li>
            </ul>

            <h2 class="text-xl font-semibold text-gray-900">3. Compartir Información</h2>
            <p>En Cocinarte, nos tomamos muy en serio tu privacidad. Solo compartimos la información estrictamente necesaria con otros usuarios para poder llevar a cabo el servicio. Por ejemplo:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Un <strong>Cocinero</strong> recibirá el nombre del cliente y detalles del pedido.</li>
                <li>Un <strong>Repartidor</strong> recibirá la dirección de entrega y el contacto del cliente para garantizar que la comida llegue correctamente.</li>
            </ul>
            <p>No vendemos, alquilamos ni comercializamos tu información personal a terceros bajo ninguna circunstancia.</p>

            <h2 class="text-xl font-semibold text-gray-900">4. Seguridad de los Datos</h2>
            <p>Implementamos medidas de seguridad técnicas y organizativas para proteger tu información contra el acceso no autorizado, alteración, divulgación o destrucción. Las contraseñas están encriptadas y protegemos las transferencias de datos.</p>

            <h2 class="text-xl font-semibold text-gray-900">5. Derechos del Usuario</h2>
            <p>Tienes el derecho de acceder, corregir o eliminar tu información personal en cualquier momento. Puedes gestionar estos datos desde el panel de tu cuenta o utilizar la opción "Eliminar Cuenta" en tu perfil si deseas darte de baja de manera definitiva.</p>

            <h2 class="text-xl font-semibold text-gray-900">6. Cambios en estas Políticas</h2>
            <p>Cocinarte se reserva el derecho de modificar esta Política de Privacidad en cualquier momento. Cualquier cambio será notificado a nuestros usuarios activos a través del correo electrónico registrado o mediante un aviso destacado en nuestra plataforma.</p>

            <h2 class="text-xl font-semibold text-gray-900">7. Contacto</h2>
            <p>Si tienes preguntas o inquietudes sobre nuestra Política de Privacidad o el manejo de tus datos, no dudes en contactarnos en: <a href="mailto:info@cocinarte.app" class="text-orange-600 hover:underline">info@cocinarte.app</a>.</p>
        </div>
    </div>
</div>
@endsection
