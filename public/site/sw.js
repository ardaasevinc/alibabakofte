const CACHE_NAME = 'ali-baba-kofte-v1';
const urlsToCache = [
  '/',
  '/css/app.css', // Laravel projenizdeki CSS yolu
  '/js/app.js',   // Laravel projenizdeki JS yolu
  '/icons/icon-192x192.png'
];

// Service Worker Yüklenirken Dosyaları Önbelleğe Al
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

// İnternet Olmasa Bile Önbellekten Dosyaları Getir
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});