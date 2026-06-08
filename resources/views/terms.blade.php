@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm p-8 md:p-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Condiciones del Servicio</h1>
        <p class="text-gray-500 mb-8">Última actualización: {{ date('d/m/Y') }}</p>

        <div class="prose prose-orange max-w-none text-gray-700 space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">1. Información de la Aplicación</h2>
            <p><strong>Cocinarte</strong> es una plataforma de Marketplace (disponible vía web y dispositivos móviles) diseñada para conectar a cocineros locales con clientes que desean adquirir comida casera en su barrio, y repartidores encargados de llevar los pedidos a su destino.</p>
            <p>El uso de la aplicación y la creación de una cuenta implica la aceptación íntegra de estas Condiciones del Servicio, así como de nuestras Políticas de Privacidad y Cookies.</p>

            <h2 class="text-xl font-semibold text-gray-900">2. Cuadro de Diálogo de Inicio de Sesión y Autenticación</h2>
            <p>Para utilizar las funciones principales de Cocinarte (realizar pedidos, vender comida o aceptar viajes de entrega), es obligatorio registrarse e iniciar sesión a través del <strong>cuadro de diálogo de inicio de sesión</strong> de la plataforma.</p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Proceso de Registro e Ingreso:</strong> Cuando interactúas con nuestro cuadro de inicio de sesión o te registras a través de terceros (por ejemplo, Facebook o Google), solicitamos acceso únicamente a tu información pública básica (nombre y correo electrónico) para crear y validar tu perfil de usuario.</li>
                <li><strong>Finalidad:</strong> Esta validación es indispensable para confirmar tu identidad, procesar correctamente los cobros/pagos, notificar el estado de los pedidos y garantizar la seguridad de la comunidad frente a fraudes o cuentas falsas.</li>
                <li><strong>Responsabilidad de la Cuenta:</strong> Eres responsable de mantener la confidencialidad de tus credenciales y de toda la actividad que ocurra bajo tu cuenta una vez que inicias sesión.</li>
            </ul>

            <h2 class="text-xl font-semibold text-gray-900">3. Uso Aceptable</h2>
            <p>Al utilizar la plataforma, te comprometes a actuar de buena fe. Los usuarios no deben:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Proporcionar información falsa, engañosa o utilizar cuentas de terceros sin autorización.</li>
                <li>Interferir con el correcto funcionamiento del cuadro de diálogo de inicio de sesión o intentar evadir las medidas de seguridad tecnológicas.</li>
                <li>Hacer pedidos falsos o abusar del sistema de cancelaciones.</li>
            </ul>

            <h2 class="text-xl font-semibold text-gray-900">4. Suspensión y Cancelación de la Cuenta</h2>
            <p>Cocinarte se reserva el derecho de suspender o eliminar temporal o permanentemente el acceso a la plataforma (deshabilitando el inicio de sesión) a cualquier usuario que viole estas Condiciones del Servicio, incluyendo fraudes, comportamiento indebido hacia los cocineros/repartidores o violaciones de seguridad.</p>

            <h2 class="text-xl font-semibold text-gray-900">5. Contacto</h2>
            <p>Si tienes alguna pregunta sobre el funcionamiento de nuestra aplicación, las reglas de inicio de sesión o cualquier punto de estas Condiciones, puedes escribirnos a: <a href="mailto:info@cocinarte.app" class="text-orange-600 hover:underline">info@cocinarte.app</a>.</p>
        </div>
    </div>
</div>
@endsection
