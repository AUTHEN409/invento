const CACHE_NAME = 'invento-v3';

self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => caches.delete(cacheName))
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request).catch(() => {
            const accept = event.request.headers.get('accept') || '';
            if (accept.includes('text/html')) {
                return new Response(
                    '<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Offline</title><style>body{font-family:sans-serif;padding:2rem;text-align:center;color:#333;}</style></head><body><h1>You are offline</h1><p>Please check your internet connection.</p><button onclick="window.location.reload()" style="padding:10px 20px;background:#0d9488;color:#fff;border:none;border-radius:5px;cursor:pointer;">Retry</button></body></html>',
                    { status: 503, headers: { 'Content-Type': 'text/html' } }
                );
            }
            if (accept.includes('application/json')) {
                return new Response(
                    JSON.stringify({ error: 'Offline', message: 'No network connection.' }),
                    { status: 503, headers: { 'Content-Type': 'application/json' } }
                );
            }
            return new Response('', { status: 503 });
        })
    );
});
