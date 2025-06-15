const CACHE_NAME = 'aescala-v{{ now()->format("YmdHis") }}';
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
  '/img/web-app-manifest-512x512.png'
];


self.addEventListener('install', event => {
  console.log('[SW] Instalando...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(urlsToCache);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  console.log('[SW] Activado');
  event.waitUntil(
    caches.keys().then(cacheNames =>
      Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            console.log('[SW] Borrando cache viejo:', cache);
            return caches.delete(cache);
          }
        })
      )
    )
  );
  self.clients.claim();
});

