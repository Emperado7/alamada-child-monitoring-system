// ═══════════════════════════════════════════════════════════
//  Alamada CMS — Service Worker
//  Strategy: Cache-first for static assets, Network-first
//  for HTML pages, Offline fallback when network fails.
// ═══════════════════════════════════════════════════════════

const CACHE_NAME    = 'alamada-cms-v1';
const OFFLINE_URL   = '/offline.html';

// Static assets to pre-cache on install
const PRECACHE_URLS = [
    '/offline.html',
    // CDN assets cached at runtime (not listed here)
];

// ── Install: pre-cache offline page ──────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_URLS);
        })
    );
    self.skipWaiting();
});

// ── Activate: remove old caches ──────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(k => k !== CACHE_NAME)
                    .map(k => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// ── Fetch: network-first for HTML, cache-first for assets ────
self.addEventListener('fetch', event => {
    const req = event.request;

    // Only handle GET requests
    if (req.method !== 'GET') return;

    // Skip non-http(s) requests
    if (!req.url.startsWith('http')) return;

    // Skip admin API / login / logout routes from caching
    const url = new URL(req.url);
    const skipPaths = ['/login', '/logout', '/api/'];
    if (skipPaths.some(p => url.pathname.startsWith(p))) return;

    const isHTML = req.headers.get('Accept') &&
                   req.headers.get('Accept').includes('text/html');

    if (isHTML) {
        // Network-first for HTML pages
        event.respondWith(
            fetch(req)
                .then(response => {
                    // Cache a copy of successful HTML responses
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(req, clone));
                    }
                    return response;
                })
                .catch(() => {
                    // Try cache first, then offline fallback
                    return caches.match(req).then(cached => {
                        return cached || caches.match(OFFLINE_URL);
                    });
                })
        );
    } else {
        // Cache-first for static assets (CSS, JS, images, fonts)
        event.respondWith(
            caches.match(req).then(cached => {
                if (cached) return cached;

                return fetch(req).then(response => {
                    // Only cache valid responses
                    if (!response || response.status !== 200 || response.type === 'opaque') {
                        return response;
                    }
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(req, clone));
                    return response;
                }).catch(() => cached || new Response('', { status: 408 }));
            })
        );
    }
});
