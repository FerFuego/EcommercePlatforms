🚀 Auditoría Profesional de SEO & SEM (Área Pública)
Proyecto: Cocinarte — Marketplace de Comida Casera
Alcance: Exclusivamente superficies públicas indexables (Landing/Home, Catálogo, Perfil de Cocinero, Ficha de Plato y Páginas Legales).

1. Resumen Ejecutivo & Diagnóstico
El marketplace cuenta con una base visual y de experiencia de usuario (UI/UX) moderna, atractiva y con arquitectura PWA. Sin embargo, desde la perspectiva de SEO (Orgánico) y SEM (Paid Media / Performance), el proyecto se encuentra en un estado embrionario:

┌─────────────────────────────────────────────────────────────┐
│ PUNTUACIÓN DE MADUREZ DIGITAL (Parte Pública)               │
├───────────────────────────────┬─────────────────────────────┤
│ SEO Técnico & Rastreo         │ 🔴 35 / 100 (Crítico)       │
│ SEO On-Page & Contenidos      │ 🟡 45 / 100 (Básico)        │
│ Local SEO & Rich Snippets     │ 🔴 20 / 100 (Sin Schema)    │
│ SEM, Pixel & Medición E-comm  │ 🔴 10 / 100 (Inoperativo)   │
│ Rendimiento / Core Web Vitals │ 🟢 78 / 100 (Aceptable)     │
└───────────────────────────────┴─────────────────────────────┘
Principales Hallazgos Críticos:
Sin Sitemap ni Robots.txt optimizado: No existe sitemap.xml dinámico para platos y cocineros, y el archivo robots.txt no bloquea rutas dinámicas/carrito ni declara sitemaps.
Meta Tags duplicadas: La misma <meta name="description"> global se repite en todo el sitio (catálogo, platos y perfiles), lo que provoca canibalización y penalización por contenido duplicado en Googlebot.
Ausencia de Etiquetas Canónicas (rel="canonical"): En el catálogo (/marketplace/catalog), los filtros de radio, coordenadas, dieta y precio generan miles de combinaciones de URLs que pueden agotar el presupuesto de rastreo (crawl budget) y generar contenido duplicado.
Cero Datos Estructurados (Schema.org / JSON-LD): Los motores de búsqueda no interpretan platos como Product/MenuItem, ni a los cocineros como LocalBusiness/Restaurant, perdiendo la posibilidad de mostrar estrellas de calificación, precios e imágenes en los resultados de búsqueda.
Cero Open Graph / Twitter Cards: Al compartir un enlace de un cocinero o un plato por WhatsApp o redes sociales, no se genera vista previa atractiva (imagen, precio, bio).
SEM a ciegas: No hay Google Tag Manager, Google Analytics 4 (GA4), Meta Pixel ni seguimiento de conversiones (AddToCart, InitiateCheckout, Purchase), lo cual imposibilita realizar campañas rentables de Google Ads (PMax / Search) o Meta Ads (Instagram/Facebook).

2. Auditoría Detallada por Componente Público
A. Home / Landing (/)
Estado Actual:
Buen uso de títulos visuales y llamados a la acción duales (Cocinero / Cliente).
<h1> presente ("Comida Casera Hecha con Amor").
Imágenes estáticas del hero en formato .png en vez de .webp comprimido sin dimensiones fijas (width/height), generando potencial Cumulative Layout Shift (CLS).
Oportunidades:
Falta de palabras clave transaccionales y de geolocalización en el encabezado principal (ej. "Comida Casera y Viandas a Domicilio").
Sin marcado WebSite ni Organization con SearchAction (barra de búsqueda de Google vinculada al sitio).
Ausencia de sección de preguntas frecuentes (FAQ) con acordeones y marcado FAQPage para ganar posiciones cero (snippets destacados) en Google.
B. Catálogo Público (/marketplace/catalog)
Estado Actual:
Title genérico: <title>Explorar Cocineros</title>.
La carga de cocineros depende principalmente de geolocalización por JS. Googlebot (que rastrea desde IPs internacionales y sin activar geolocalización de navegador) ve un listado vacío o limitado.
Múltiples filtros por URL (?lat=...&lng=...&radius=...&sort=...) sin canonical.
Oportunidades:
Implementar Fallback SEO: Si no hay parámetros de ubicación, pre-renderizar en el servidor los cocineros y platos mejor valorados y más populares de la plataforma.
Canonicalización estricta hacia /marketplace/catalog o páginas de categoría estáticas (ej. /comida-vegetariana, /viandas-semanales).
Título optimizado: "Catálogo de Cocineros Caseros y Menú del Día | Cocinarte".
C. Perfil Público del Cocinero (/marketplace/cook/{id})
Estado Actual:
URL no amigable para SEO: utiliza IDs numéricos en lugar de slugs (/marketplace/cook/3 en vez de /cocineros/maria-pastas-caseras).
El <title> es solo el nombre de la persona (ej. "María Gómez").
Sin meta descripción dinámica (se pierde el extracto de su biografía y especialidades).
Oportunidades:
Estructurar el título para intención de búsqueda local: "{Nombre} — Cocinero Casero | Menú y Pedidos en Cocinarte".
Inyectar JSON-LD de tipo LocalBusiness / Restaurant con:
name, description, image, telephone (opcional/público).
aggregateRating: rating promedio y número de reviews.
servesCuisine: tipos de dietas o cocina.
Meta tags Open Graph (og:image, og:title, og:description) para viralización en WhatsApp y grupos vecinales.
D. Detalle de Plato (/marketplace/dish/{id})
Estado Actual:
URL numérica: /marketplace/dish/{id}.
Título: {Nombre Plato} — Cocinarte.
Meta descripción no configurada específicamente para el plato.
Oportunidades:
Slug en URL: /platos/{slug-del-plato} o /marketplace/dish/{id}-{slug}.
Inyectar JSON-LD de tipo Product o MenuItem:
name: Nombre del plato.
description: Ingredientes o detalles del plato.
offers: Precio, disponibilidad (InStock / OutOfStock), moneda (ARS).
image: Foto del plato en alta resolución.
Esta estructura permite aparecer en Google Shopping y en los resultados enriquecidos de comida/recetas.
E. Infraestructura Técnica (Head Global, Assets y Rendimiento)
CSS Bloqueante: En layouts/app.blade.php se cargan leaflet.css y flatpickr.min.css globalmente en todas las páginas, incluso donde no hay mapas ni selectores de fecha, demorando el First Contentful Paint (FCP).
Robots.txt: Actualmente es permisivo (Disallow:  sin restricciones). Debe proteger rutas como /cart/*, /orders/*, /push-tokens y parámetros innecesarios para ahorrar crawl budget.


3. Plan de Acción para Mejoras (Priorizado)

🟢 FASE 1: Quick Wins (Alto Impacto, Bajo Esfuerzo) — ✅ COMPLETADA
- [x] **Meta Tags Dinámicas y Open Graph en Layout (`resources/views/layouts/app.blade.php`):**
  - Implementado `@yield('meta_description')`, `@yield('og_image')`, `@yield('canonical')`, `@yield('og_title')`.
  - Integrado Open Graph completo (`og:type`, `og:site_name`, `og:url`, `og:title`, `og:description`, `og:image`) y Twitter Cards para previews automáticas y profesionales en WhatsApp, Instagram, Twitter y Facebook.
  - Implementado `<link rel="canonical" href="@yield('canonical', url()->current())">` para prevenir duplicación por parámetros de consulta.
- [x] **Optimización de Títulos y Metas por Vista Pública:**
  - **Home (`landing.blade.php`):** Título SEO orientado a intención de compra, meta descripción transaccional y canonical hacia `/`.
  - **Catálogo (`catalog.blade.php`):** Título descriptivo, meta descripción para búsqueda local/dietas y canonical estricto hacia `/marketplace/catalog` que neutraliza filtros URL duplicados.
  - **Perfil de Cocinero (`cook-profile.blade.php`):** Título dinámico con especialidad, meta descripción basada en bio del cocinero, Open Graph con foto de perfil/cocina y tipo `profile`.
  - **Detalle de Plato (`dish-detail.blade.php`):** Título con nombre, cocinero y precio formateado, meta descripción dinámica con ingredientes, Open Graph con foto del plato y tipo `product`.
  - **Páginas Legales (`privacy`, `terms`, `cookies`):** Títulos, descripciones y canonicals dedicados.
- [x] **Optimización de `public/robots.txt`:**
  - Configurado bloqueo para rutas privadas, APIs y checkout (`/cart/`, `/orders/`, `/checkout`, `/profile/`, `/push-tokens/`, `/dashboard`, `/admin/`, `/cook/`, etc.).
  - Declarado `Sitemap: https://cocinarte.com.ar/sitemap.xml`.
- [x] **Generador Dinámico de `sitemap.xml`:**
  - Creado `SitemapController` y vista XML en `/sitemap.xml` con estándares de sitemaps.org.
  - Incluye: Home (1.0), Catálogo (0.9), cocineros aprobados/activos (0.8), platos activos (0.7) y legales (0.3).




🟡 FASE 2: SEO Técnico y Rich Snippets — ✅ COMPLETADA
- [x] **Implementación de Schema.org (JSON-LD):**
  - **Home (`landing.blade.php`):** Estructura `Organization` y `WebSite` con `potentialAction` (`SearchAction`) apuntando al buscador del catálogo.
  - **Catálogo (`catalog.blade.php`):** Estructura `BreadcrumbList` (`Inicio > Catálogo`).
  - **Cocinero (`cook-profile.blade.php`):** Estructura `Restaurant` con `name`, `image`, `description`, `priceRange`, `servesCuisine`, `geo` (coordenadas geográficas) y `aggregateRating` (basado en el rating real y reviews de la BD), más `BreadcrumbList`.
  - **Plato (`dish-detail.blade.php`):** Estructura `Product` y `MenuItem` con `name`, `image`, `description`, y `offers` (`price`, `priceCurrency: ARS`, disponibilidad `InStock`/`OutOfStock`, vendedor), más `BreadcrumbList`.
- [x] **Carga Condicional de Assets (CSS y JS):**
  - Eliminados `leaflet.css` y `flatpickr.min.css` del `<head>` global en `layouts/app.blade.php` (retrasaban el FCP innecesariamente).
  - Eliminados `leaflet.js` y `flatpickr.js` del pie global.
  - Mapeados condicionalmente vía `@push('styles')` y `@push('scripts')` exclusivamente donde se utilizan (`orders/checkout.blade.php` para Flatpickr, y vistas de repartidor para Leaflet).
- [x] **Optimización de Imágenes y Prevención de CLS:**
  - Logo en Navbar optimizado con atributos explícitos `width="160" height="64"`, `alt` dinámico con el nombre del sitio y `fetchpriority="high"`.
  - Imágenes del Hero de la Landing optimizadas con `width="240" height="192"`, `loading="lazy"` para imágenes inferiores y textos `alt` contextuales de comida casera.
- [x] **Tests Automatizados de Cobertura:**
  - Creado `tests/Feature/SeoMetadataTest.php` validando canonicals, Open Graph, Sitemap XML y Schemas JSON-LD en todas las vistas públicas. Test suite 100% verde (223/223 tests pasando).



🔵 FASE 3: SEM & Estrategia de Conversión Paga — Semana 2
Arquitectura de Medición y Tracking (Google & Meta):

Integrar Google Tag Manager (GTM) o Google tag (gtag.js) y Meta Pixel.
Disparar eventos estándar de E-commerce:
view_item_list en el Catálogo.
view_item en el Detalle del Plato.
add_to_cart cuando el usuario pulsa "Agregar al Carrito".
begin_checkout en la pantalla de Checkout.
purchase (con valor de orden, ID y moneda) en orders/success.blade.php.
Estructura de Campañas SEM Recomendadas:

Google Search (Alta Intención):
Grupo 1: Comida casera a domicilio [Tu Ciudad]
Grupo 2: Viandas caseras semanales / mensuales
Grupo 3: Comida sin TACC / Vegana artesanal
Meta Ads (Instagram / Facebook - Awareness & Retargeting):
Campaña de prospección con fotos atractivas de platos locales a un radio de 3 a 5 km.
Campaña de Retargeting para usuarios que vieron platos o agregaron al carrito pero no completaron la compra en los últimos 7 días.
Landing Pages Específicas para Pauta:

Crear URLs estáticas orientadas a intención de compra (ej. /viandas-saludables, /pastas-artesanales) que redirijan tráfico de pauta con filtros preconfigurados, aumentando el Quality Score de los anuncios y bajando el Costo por Clic (CPC).