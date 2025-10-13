// Cache name with version
const CACHE_NAME = 'purrfectpaw-cache-v1';

// Files to cache
const urlsToCache = [
  '/',
  '/index.html',
  '/manifest.json',
  '/offline.html',
  '/debug.html',
  '/assets/favicon.png',
  '/assets/icon.png',
  '/assets/splash-icon.png',
  '/assets/logo.png',
  '/assets/purrfectpaw_logo.png',
  '/assets/default-pet.png',
  '/assets/default_clinic.png'
];

// Install service worker and cache files
self.addEventListener('install', event => {
  console.log('Service Worker installing.');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch from cache first, then network
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached version if found
        if (response) {
          return response;
        }
        
        // Otherwise fetch from network
        return fetch(event.request)
          .then(response => {
            // Don't cache if not a valid response or not GET request
            if (!response || response.status !== 200 || response.type !== 'basic' || event.request.method !== 'GET') {
              return response;
            }

            // Clone the response
            let responseToCache = response.clone();

            // Add to cache for future
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return response;
          })
          .catch(() => {
            // Check if this is an API request
            if (event.request.url.includes('/api/')) {
              // For API requests that fail, return a custom JSON response
              return new Response(JSON.stringify({
                status: 'offline',
                message: 'You are currently offline. Please check your connection.',
                timestamp: new Date().toISOString()
              }), {
                headers: { 'Content-Type': 'application/json' },
                status: 503,
                statusText: 'Service Unavailable'
              });
            }
            
            // Fallback for image requests
            if (event.request.url.match(/\.(jpg|jpeg|png|gif|svg|webp)$/)) {
              return caches.match('/assets/default-pet.png');
            }
            
            // Fallback for HTML page requests (navigation)
            if (event.request.headers.get('accept').includes('text/html')) {
              return caches.match('/offline.html');
            }
            
            // Default fallback
            return caches.match('/offline.html');
          });
      })
  );
});

// Clean up old caches when new service worker activates
self.addEventListener('activate', event => {
  console.log('Service Worker activating.');
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});