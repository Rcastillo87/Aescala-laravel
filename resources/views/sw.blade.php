const CACHE_NAME = 'aescala-v1';

const urlsToCache = [
    '/',
    '/favicon.ico',
    '{{ asset('img/apple-touch-icon.png') }}',
    '{{ asset('img/banner.jpg') }}',
    '{{ asset('img/favicon-96x96.png') }}',
    '{{ asset('img/favicon.svg') }}',
    '{{ asset('img/logo.png') }}',
    '{{ asset('manifest.json') }}',
    '{{ Vite::asset("resources/css/app.css") }}'
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.map(k => k !== CACHE_NAME && caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    if (event.request.destination === 'script') {
        event.respondWith(
            fetch(event.request)
                .then(res => {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then(c => c.put(event.request, clone));
                    return res;
                })
                .catch(() => caches.match(event.request))
        );
        return;
    }
    event.respondWith(
        caches.match(event.request).then(res => res || fetch(event.request))
    );
});