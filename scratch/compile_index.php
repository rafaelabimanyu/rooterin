<?php

$url = 'http://127.0.0.1:8000';
echo "Fetching live homepage from {$url}...\n";

$html = file_get_contents($url);
if (!$html) {
    die("Error: Could not retrieve HTML from local server. Make sure 'php artisan serve' is running.\n");
}

// Perform path normalization replacements
$replacements = [
    'http://127.0.0.1:8000/build/' => 'build/',
    'http://127.0.0.1:8000/images/' => 'images/',
    'http://127.0.0.1:8000/storage/' => 'images/',
    'http://127.0.0.1:8000/favicon.ico' => 'favicon.ico',
    'http://127.0.0.1:8000/layanan' => 'layanan.html',
    'http://127.0.0.1:8000/about' => 'about.html',
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
    '/galeri' => 'galeri.html',
    '/kontak' => 'kontak.html',
    '/wiki' => 'wiki.html',
    '/tips' => 'tips.html',
];

foreach ($replacements as $search => $replace) {
    $html = str_replace($search, $replace, $html);
}

// Make sure other absolute/root-relative links to home are resolved to index.html
$html = str_replace('href="/"', 'href="index.html"', $html);
$html = str_replace('href="index.html?lang=id"', 'href="?lang=id"', $html);
$html = str_replace('href="index.html?lang=en"', 'href="?lang=en"', $html);

// Ensure the local business schema points to rafaelabimanyu.github.io/rooterin/ or empty for relative resolution
$html = str_replace('"url": "index.html"', '"url": ""', $html);

// Ensure Open Graph Image path is relative/correct
$html = str_replace('content="index.html/images/logo.png"', 'content="images/logo.png"', $html);

$dest = __DIR__ . '/../docs/index.html';
if (file_put_contents($dest, $html)) {
    echo "Compiled page saved successfully to docs/index.html!\n";
} else {
    echo "Error saving compiled page.\n";
}
