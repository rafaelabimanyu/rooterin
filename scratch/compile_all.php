<?php

$routes = [
    '/' => 'index.html',
    '/tentang' => 'about.html',
    '/layanan' => 'layanan.html',
    '/galeri' => 'galeri.html',
    '/kontak' => 'kontak.html',
    '/tips' => 'tips.html',
    '/wiki' => 'wiki.html',
];

$replacements = [
    'http://127.0.0.1:8000/build/' => 'build/',
    'http://127.0.0.1:8000/images/' => 'images/',
    'http://127.0.0.1:8000/storage/' => 'images/',
    'http://127.0.0.1:8000/favicon.ico' => 'favicon.ico',
    'http://127.0.0.1:8000/layanan' => 'layanan.html',
    'http://127.0.0.1:8000/about' => 'about.html',
    'http://127.0.0.1:8000/tentang' => 'about.html',
    'http://127.0.0.1:8000/galeri' => 'galeri.html',
    'http://127.0.0.1:8000/kontak' => 'kontak.html',
    'http://127.0.0.1:8000/wiki' => 'wiki.html',
    'http://127.0.0.1:8000/tips' => 'tips.html',
    'http://127.0.0.1:8000/' => 'index.html',
    'http://127.0.0.1:8000' => 'index.html',
    
    '/build/' => 'build/',
    '/images/' => 'images/',
    '/storage/' => 'images/',
    '/favicon.ico' => 'favicon.ico',
    '/layanan' => 'layanan.html',
    '/about' => 'about.html',
    '/tentang' => 'about.html',
    '/galeri' => 'galeri.html',
    '/kontak' => 'kontak.html',
    '/wiki' => 'wiki.html',
    '/tips' => 'tips.html',
];

foreach ($routes as $route => $filename) {
    $url = "http://127.0.0.1:8000" . $route;
    echo "Compiling route: {$route} -> docs/{$filename}...\n";
    
    $html = file_get_contents($url);
    if (!$html) {
        echo "WARNING: Could not fetch route {$route}\n";
        continue;
    }
    
    // Replace URL paths with relative counterparts
    foreach ($replacements as $search => $replace) {
        $html = str_replace($search, $replace, $html);
    }
    
    // Normalize links
    $html = str_replace('href="/"', 'href="index.html"', $html);
    $html = str_replace('href="index.html?lang=id"', 'href="?lang=id"', $html);
    $html = str_replace('href="index.html?lang=en"', 'href="?lang=en"', $html);
    
    // Fix Open Graph / Schema values
    $html = str_replace('"url": "index.html"', '"url": ""', $html);
    $html = str_replace('content="index.html/images/logo.png"', 'content="images/logo.png"', $html);
    
    // Galeri-specific updates
    if ($filename === 'galeri.html') {
        // Swap unsplash hero images
        $html = str_replace(
            'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&fit=crop',
            'images/pages/hero1.webp',
            $html
        );
        $html = str_replace(
            'https://images.unsplash.com/photo-1542013936693-884638332954?w=600&fit=crop',
            'images/pages/hero2.webp',
            $html
        );
    }
    
    // About-specific updates
    if ($filename === 'about.html') {
        $html = str_replace(
            'https://images.unsplash.com/photo-1542013936693-884638332954?q=80&amp;w=1200&amp;auto=format&amp;fit=crop',
            'images/team/team.png',
            $html
        );
        $html = str_replace(
            'https://images.unsplash.com/photo-1542013936693-884638332954?q=80&w=1200&auto=format&fit=crop',
            'images/team/team.png',
            $html
        );
    }
    
    $dest = __DIR__ . '/../docs/' . $filename;
    file_put_contents($dest, $html);
}

echo "All pages compiled and saved successfully to docs/!\n";
