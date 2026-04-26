const CACHE_NAME = 'cocinarte-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/manifest.json',
    '/icon-192x192.png',
    '/icon-512x512.png',
    // You can add paths to offline pages or other assets here
];

// Instalar Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(ASSETS_TO_CACHE);
            })
    );
});

// Activar y limpiar cachés antiguos
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// Interceptar peticiones para funcionamiento Offline básico
self.addEventListener('fetch', event => {
    // Para peticiones GET, intentar responder con red, y si falla, buscar en caché.
    if (event.request.method === 'GET') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match(event.request)
                        .then(response => {
                            if (response) {
                                return response;
                            }
                            // Si no hay respuesta en caché, podríamos devolver una página offline genérica aquí
                            // return caches.match('/offline.html');
                        });
                })
        );
    }
});
