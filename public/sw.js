const CACHE_NAME = 'poprua-v1';

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (e) => e.waitUntil(clients.claim()));

self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (event.request.method === 'GET' && response.status === 200) {
                    const url = new URL(event.request.url);
                    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|woff2?)$/)) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    }
                }
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});
