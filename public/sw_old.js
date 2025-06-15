const CACHE_NAME = 'aescala-v1';
const urlsToCache = [
  '/',
  '/favicon.ico',
  '/img/apple-touch-icon.png',
  '/img/banner.jpg',
  '/img/favicon-96x96.png',
  '/img/favicon.svg',
  '/img/logo.png',
  '/img/site.webmanifest',
  '/img/web-app-manifest-192x192.png',
  '/img/web-app-manifest-512x512.png',
  '/css/app.css',
  '/js/app.js',
];


self.addEventListener('install', function(event) {
  console.log('Service Worker: Instalando...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(async cache => {
      for (const url of urlsToCache) {
        try {
          await cache.add(url);
        } catch (e) {
          console.warn('No se pudo cachear:', url, e);
        }
      }
    })
  );
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request).then(function(response) {
      return response || fetch(event.request);
    })
  );
});
