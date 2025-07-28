const CACHE_NAME = 'stream-platform-v1';
const CRITICAL_CACHE = 'critical-v1';
const STATIC_CACHE = 'static-v1';

// Critical assets that should be cached immediately
const CRITICAL_ASSETS = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json'
];

// Static assets to cache
const STATIC_ASSETS = [
    '/images/logo.png',
    '/images/og-default.jpg',
    '/images/favicon.ico'
];

// Install event - cache critical resources
self.addEventListener('install', event => {
    event.waitUntil(
        Promise.all([
            caches.open(CRITICAL_CACHE).then(cache => {
                return cache.addAll(CRITICAL_ASSETS);
            }),
            caches.open(STATIC_CACHE).then(cache => {
                return cache.addAll(STATIC_ASSETS);
            })
        ])
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME && 
                        cacheName !== CRITICAL_CACHE && 
                        cacheName !== STATIC_CACHE) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - implement caching strategies
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip cross-origin requests unless they're for known CDNs
    if (url.origin !== self.location.origin && !isTrustedCDN(url.origin)) {
        return;
    }

    event.respondWith(handleRequest(request));
});

async function handleRequest(request) {
    const url = new URL(request.url);
    
    // Critical assets - Cache First
    if (isCriticalAsset(request.url)) {
        return cacheFirst(request, CRITICAL_CACHE);
    }
    
    // Static assets (images, fonts, etc.) - Cache First with long TTL
    if (isStaticAsset(request.url)) {
        return cacheFirst(request, STATIC_CACHE);
    }
    
    // API calls - Network First with cache fallback
    if (url.pathname.startsWith('/api/')) {
        return networkFirst(request);
    }
    
    // HTML pages - Stale While Revalidate
    if (request.destination === 'document') {
        return staleWhileRevalidate(request);
    }
    
    // Default strategy - Network First
    return networkFirst(request);
}

// Cache First strategy - good for static assets
async function cacheFirst(request, cacheName = CACHE_NAME) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        // Return offline fallback if available
        return getOfflineFallback(request);
    }
}

// Network First strategy - good for dynamic content
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        return getOfflineFallback(request);
    }
}

// Stale While Revalidate - good for HTML pages
async function staleWhileRevalidate(request) {
    const cachedResponse = await caches.match(request);
    
    const networkPromise = fetch(request).then(response => {
        if (response.ok) {
            const cache = caches.open(CACHE_NAME);
            cache.then(c => c.put(request, response.clone()));
        }
        return response;
    }).catch(() => null);
    
    return cachedResponse || networkPromise || getOfflineFallback(request);
}

// Helper functions
function isCriticalAsset(url) {
    return CRITICAL_ASSETS.some(asset => url.includes(asset)) ||
           url.includes('/css/') ||
           url.includes('/js/app');
}

function isStaticAsset(url) {
    return url.includes('/images/') ||
           url.includes('/fonts/') ||
           url.includes('.png') ||
           url.includes('.jpg') ||
           url.includes('.jpeg') ||
           url.includes('.gif') ||
           url.includes('.webp') ||
           url.includes('.svg') ||
           url.includes('.woff') ||
           url.includes('.woff2');
}

function isTrustedCDN(origin) {
    const trustedCDNs = [
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        'https://cdnjs.cloudflare.com',
        'https://unpkg.com',
        'https://image.tmdb.org'
    ];
    return trustedCDNs.includes(origin);
}

async function getOfflineFallback(request) {
    if (request.destination === 'document') {
        return caches.match('/offline.html') || 
               new Response('Offline - Please check your connection', {
                   status: 503,
                   statusText: 'Service Unavailable'
               });
    }
    
    if (request.destination === 'image') {
        return caches.match('/images/offline-image.svg') ||
               new Response('', { status: 503 });
    }
    
    return new Response('', { status: 503 });
}

// Background sync for offline actions
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    // Handle queued actions when back online
    try {
        const requests = await getQueuedRequests();
        for (const request of requests) {
            await fetch(request);
        }
        await clearQueuedRequests();
    } catch (error) {
        console.log('Background sync failed:', error);
    }
}

// Cache management
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(clearCaches());
    }
});

async function clearCaches() {
    const cacheNames = await caches.keys();
    return Promise.all(cacheNames.map(name => caches.delete(name)));
}

// Performance optimizations
const MAX_CACHE_SIZE = 50; // Maximum number of items in cache
const CACHE_EXPIRY = 24 * 60 * 60 * 1000; // 24 hours

// Limit cache size
async function limitCacheSize(cacheName, maxItems) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    
    if (keys.length > maxItems) {
        const itemsToDelete = keys.slice(0, keys.length - maxItems);
        await Promise.all(itemsToDelete.map(key => cache.delete(key)));
    }
}

// Clean expired items
async function cleanExpiredItems() {
    const cacheNames = await caches.keys();
    
    for (const cacheName of cacheNames) {
        const cache = await caches.open(cacheName);
        const keys = await cache.keys();
        
        for (const key of keys) {
            const response = await cache.match(key);
            const dateHeader = response.headers.get('date');
            
            if (dateHeader) {
                const cacheDate = new Date(dateHeader);
                const now = new Date();
                
                if (now - cacheDate > CACHE_EXPIRY) {
                    await cache.delete(key);
                }
            }
        }
        
        await limitCacheSize(cacheName, MAX_CACHE_SIZE);
    }
}

// Run cleanup periodically
setInterval(cleanExpiredItems, 60 * 60 * 1000); // Every hour