@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm p-8 md:p-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Política de Cookies</h1>
        <p class="text-gray-500 mb-8">Última actualización: {{ date('d/m/Y') }}</p>

        <div class="prose prose-orange max-w-none text-gray-700 space-y-6">
            <p>En <strong>Cocinarte</strong> utilizamos cookies y tecnologías similares únicamente para garantizar el correcto funcionamiento de nuestra plataforma y mejorar tu experiencia como usuario. A continuación, te explicamos qué son, cómo las usamos y cómo puedes gestionarlas.</p>

            <h2 class="text-xl font-semibold text-gray-900">1. ¿Qué son las cookies?</h2>
            <p>Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo (computadora, tablet o teléfono móvil) cuando visitas un sitio web. Estos archivos permiten que la plataforma "recuerde" tus acciones y preferencias durante un tiempo determinado.</p>

            <h2 class="text-xl font-semibold text-gray-900">2. Uso exclusivo para fines internos</h2>
            <p>En Cocinarte nos tomamos muy en serio tu privacidad. <strong>Actualmente, no compartimos, vendemos ni utilizamos cookies de seguimiento para fines publicitarios de terceros.</strong></p>
            <p>Nuestras cookies se utilizan estrictamente para uso interno, incluyendo:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Cookies Esenciales:</strong> Necesarias para iniciar sesión de forma segura, mantener tu sesión activa y procesar pedidos.</li>
                <li><strong>Preferencias del Usuario:</strong> Para recordar tus selecciones, como tu ubicación o la configuración de tu cuenta.</li>
                <li><strong>Rendimiento y Funcionamiento:</strong> Para entender cómo los usuarios navegan por la plataforma, lo que nos ayuda a identificar errores y mejorar el diseño.</li>
            </ul>

            <h2 class="text-xl font-semibold text-gray-900">3. Cookies de Terceros</h2>
            <p>Al utilizar servicios esenciales para el funcionamiento de la aplicación (como los mapas para ubicar a los cocineros y repartidores, o la pasarela de pagos), es posible que proveedores externos guarden cookies temporales en tu navegador. Sin embargo, estas se limitan estrictamente al servicio prestado.</p>

            <h2 class="text-xl font-semibold text-gray-900">4. ¿Cómo puedes gestionar tus cookies?</h2>
            <p>Tienes el control total sobre las cookies. Puedes configurar tu navegador para bloquearlas o eliminarlas en cualquier momento. Sin embargo, ten en cuenta que deshabilitar las cookies esenciales podría afectar negativamente tu experiencia, impidiéndote iniciar sesión o realizar pedidos.</p>
            <p>Puedes encontrar más información sobre cómo gestionar las cookies en las opciones de configuración de tu navegador web (Chrome, Safari, Firefox, Edge, etc.).</p>

            <h2 class="text-xl font-semibold text-gray-900">5. Contacto</h2>
            <p>Si tienes alguna duda sobre nuestra Política de Cookies, puedes ponerte en contacto con nosotros escribiendo a: <a href="mailto:info@cocinarte.app" class="text-orange-600 hover:underline">info@cocinarte.app</a>.</p>
        </div>
    </div>
</div>
@endsection
