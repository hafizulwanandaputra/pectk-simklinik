const CACHE_NAME = 'pectk-farmasi-cache-v1';
const ASSETS_TO_CACHE = [
  './', 
  './favicon.png',
  './Screenshot.png',
  './assets/css/dashboard/dashboard.css',
  './assets/css/dashboard/dashboard.rtl.css',
  './assets/images/profile/logo_pec.png',
  './assets/images/profile/pec-dark.jpg',
  './assets/images/profile/pec.jpg',
  './assets/js/dashboard/dashboard.js',
  './assets_public/css/gradient.css',
  './assets_public/css/JawiDubai.css',
  './assets_public/css/main.css',
  './assets_public/fonts/inter-hwp/inter-hwp.css',
  './assets_public/fonts/inter-hwp/InterHWP-Black.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-BlackItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-Bold.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-BoldItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraBold.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraBoldItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraLight.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraLightItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-Italic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-Light.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-LightItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-Medium.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-MediumItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-Regular.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-SemiBold.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-SemiBoldItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-Thin.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-ThinItalic.ttf',
  './assets_public/fonts/inter-hwp/InterHWP-Black.woff',
  './assets_public/fonts/inter-hwp/InterHWP-BlackItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-Bold.woff',
  './assets_public/fonts/inter-hwp/InterHWP-BoldItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraBold.woff',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraBoldItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraLight.woff',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraLightItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-Italic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-Light.woff',
  './assets_public/fonts/inter-hwp/InterHWP-LightItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-Medium.woff',
  './assets_public/fonts/inter-hwp/InterHWP-MediumItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-Regular.woff',
  './assets_public/fonts/inter-hwp/InterHWP-SemiBold.woff',
  './assets_public/fonts/inter-hwp/InterHWP-SemiBoldItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-Thin.woff',
  './assets_public/fonts/inter-hwp/InterHWP-ThinItalic.woff',
  './assets_public/fonts/inter-hwp/InterHWP-Black.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-BlackItalic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-Bold.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-BoldItalic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraBold.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraBoldItalic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraLight.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-ExtraLightItalic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-Italic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-Light.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-LightItalic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-Medium.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-MediumItalic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-Regular.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-SemiBold.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-SemiBoldItalic.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-Thin.woff2',
  './assets_public/fonts/inter-hwp/InterHWP-ThinItalic.woff2',
  './assets_public/fonts/inter-hwp/LICENSE.txt',
  './assets_public/fonts/base-font.css',
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

// Activate Service Worker and clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('Deleting old cache:', cache);
            return caches.delete(cache);
          }
        })
      )
    )
  );
  self.clients.claim(); // Take control of all pages
});

// Fetch event handler to manage cache and network requests
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Use cache-first strategy for static assets
  if (ASSETS_TO_CACHE.includes(url.pathname) || url.origin === location.origin) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse; // Serve from cache
        }
        console.log('Fetching from network:', event.request.url);
        return fetch(event.request).then((networkResponse) => {
          return caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, networkResponse.clone()); // Update the cache
            return networkResponse;
          });
        });
      })
    );
    return;
  }

  // Network-first strategy for requests like /check-login
  event.respondWith(
    fetch(event.request, { redirect: 'follow' }) // Allow redirects
      .then((response) => {
        if (!response.ok) {
          console.error('Network request failed:', response.statusText);
        }
        return response;
      })
      .catch((error) => {
        console.error('Network error:', error);
        return caches.match(event.request).then((cachedResponse) => {
          return (
            cachedResponse || new Response('Network error occurred.', { status: 500 })
          );
        });
      })
  );
});