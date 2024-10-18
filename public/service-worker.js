const CACHE_NAME = 'pectk-farmasi-cache-v1';
const ASSETS_TO_CACHE = [
  './', 
  './index.php',
  './favicon.png',
  './Screenshot.png'
];

// Install Service Worker dan caching aset
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Caching assets');
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// Mengambil aset dari cache
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});
