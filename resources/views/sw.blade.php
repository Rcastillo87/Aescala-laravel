const CACHE_NAME = 'aescala-{{ \Illuminate\Support\Carbon::now()->format("YmdHis") }}';

@php
    $isLocal = app()->environment('local');
@endphp

const urlsToCache = [
    '/',
    '/favicon.ico',
    '{{ asset('img/apple-touch-icon.png') }}',
    '{{ asset('img/banner.jpg') }}',
    '{{ asset('img/favicon-96x96.png') }}',
    '{{ asset('img/favicon.svg') }}',
    '{{ asset('img/logo.png') }}',
    '{{ asset('manifest.json') }}',
    @unless($isLocal)
        '{{ Vite::asset("resources/css/app.css") }}',
        '{{ Vite::asset("resources/js/app.js") }}'
    @endunless
];

self.addEventListener('install', event => {
    console.log('SW instalado');
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.all(
                urlsToCache.map(url =>
                    fetch(url)
                        .then(resp => {
                            if (resp.ok) {
                                return cache.put(url, resp.clone());
                            } else {
                                console.warn('No cacheó (status != 200):', url, resp.status);
                            }
                        })
                        .catch(err => {
                            console.warn('No cacheó (error en fetch):', url, err);
                        })
                )
            );
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(name => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        })
    );
    console.log('SW activado y viejo cache limpiado');
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});
