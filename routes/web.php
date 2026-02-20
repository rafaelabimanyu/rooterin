<?php

use Illuminate\Support\Facades\Route;

Route::get('/area/{city}', [\App\Http\Controllers\LocalSeoController::class, 'cityLanding'])->name('local.city');
Route::get('/area/{city}/{service}', [\App\Http\Controllers\LocalSeoController::class, 'show'])->name('local.service');
Route::get('/ai-diagnostic', [\App\Http\Controllers\AiDiagnosticController::class, 'index'])->name('ai.diagnostic');
Route::post('/ai-diagnostic/store', [\App\Http\Controllers\AiDiagnosticController::class, 'store'])->name('ai.diagnostic.store');
Route::get('/ai-diagnostic/handshake', [\App\Http\Controllers\AiDiagnosticController::class, 'getHandshake'])->name('ai.diagnostic.handshake');
Route::get('/api/search/suggest', [\App\Http\Controllers\SearchController::class, 'suggest'])->name('api.search.suggest');
Route::get('/wiki', [\App\Http\Controllers\WikiController::class, 'index'])->name('wiki.index');
Route::get('/wiki/{slug}', [\App\Http\Controllers\WikiController::class, 'show'])->name('wiki.detail');

// NEURAL ASSET VAULT: Handshake Required
Route::get('/models/{file}', function($file) {
    if (!str_ends_with($file, '.json') && !str_ends_with($file, '.bin')) {
        abort(404);
    }
    
    $security = app(\App\Services\Security\SecurityAutomationService::class);
    if (!$security->verifyHandshake(request())) {
        $security->blockIp(request()->ip(), 'Illegal Model Access (No Handshake)');
        abort(403, 'Akses model ditolak. Koneksi tidak tersinkronisasi.');
    }
    
    $path = storage_path('app/models/' . $file);
    if (!file_exists($path)) abort(404);
    
    return response()->file($path, ['Content-Type' => 'application/octet-stream']);
})->name('neural.asset.serve');

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/tentang', function () {
    return view('tentang');
})->name('about');

Route::get('/layanan', [\App\Http\Controllers\ServiceLandingController::class, 'index'])->name('services');

Route::get('/galeri', [\App\Http\Controllers\GalleryLandingController::class, 'index'])->name('gallery');

Route::get('/tips', [\App\Http\Controllers\TipsController::class, 'index'])->name('tips');

Route::get('/tips/{slug}', [\App\Http\Controllers\TipsController::class, 'show'])->name('tips.detail');

Route::get('/kontak', function () {
    return view('kontak');
})->name('contact');

Route::get('/panduan-aksesibilitas', function () {
    return view('panduan-aksesibilitas');
})->name('accessibility-guide');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['audit'])->group(function() {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Content
    Route::get('/posts', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [\App\Http\Controllers\Admin\PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [\App\Http\Controllers\Admin\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}/edit', [\App\Http\Controllers\Admin\PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');
    
    Route::get('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [\App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{id}/edit', [\App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{id}', [\App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{id}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');
    
    Route::get('/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [\App\Http\Controllers\Admin\ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}/edit', [\App\Http\Controllers\Admin\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}', [\App\Http\Controllers\Admin\ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{id}', [\App\Http\Controllers\Admin\ProjectController::class, 'destroy'])->name('projects.destroy');
    
    // Config
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/{id}', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Messages
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
    // Media Library
    Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

    // SEO Management (Super Admin Only)
    Route::middleware(['super_admin'])->group(function() {
        Route::get('/seo', [\App\Http\Controllers\Admin\SeoController::class, 'index'])->name('seo.index');
        Route::post('/seo/settings', [\App\Http\Controllers\Admin\SeoController::class, 'updateSettings'])->name('seo.settings.update');
        Route::post('/seo/redirects', [\App\Http\Controllers\Admin\SeoController::class, 'storeRedirect'])->name('seo.redirects.store');
        Route::delete('/seo/redirects/{redirect}', [\App\Http\Controllers\Admin\SeoController::class, 'deleteRedirect'])->name('seo.redirects.destroy');
        Route::post('/seo/robots', [\App\Http\Controllers\Admin\SeoController::class, 'updateRobots'])->name('seo.robots.update');
        Route::post('/seo/ping', [\App\Http\Controllers\Admin\SeoController::class, 'ping'])->name('seo.ping');
        Route::get('/seo/ping', function() { return redirect()->route('admin.seo.index'); });
        Route::post('/seo/clear-cache', [\App\Http\Controllers\Admin\SeoController::class, 'clearCache'])->name('seo.clear-cache');
        Route::get('/seo/clear-cache', function() { return redirect()->route('admin.seo.index'); });

        // Authority Keywords
        Route::post('/seo/keywords', [\App\Http\Controllers\Admin\SeoController::class, 'storeKeyword'])->name('seo.keywords.store');
        Route::delete('/seo/keywords/{keyword}', [\App\Http\Controllers\Admin\SeoController::class, 'deleteKeyword'])->name('seo.keywords.destroy');

        // Local SEO Cities
        Route::post('/seo/cities', [\App\Http\Controllers\Admin\SeoController::class, 'storeCity'])->name('seo.cities.store');
        Route::put('/seo/cities/{city}', [\App\Http\Controllers\Admin\SeoController::class, 'updateCity'])->name('seo.cities.update');
        Route::delete('/seo/cities/{city}', [\App\Http\Controllers\Admin\SeoController::class, 'deleteCity'])->name('seo.cities.destroy');

        // Trust Architect (Reviews)
        Route::post('/seo/reviews', [\App\Http\Controllers\Admin\SeoController::class, 'storeReview'])->name('seo.reviews.store');
        Route::delete('/seo/reviews/{review}', [\App\Http\Controllers\Admin\SeoController::class, 'deleteReview'])->name('seo.reviews.destroy');

        // Indexing Rocket
        Route::post('/seo/rocket', [\App\Http\Controllers\Admin\SeoController::class, 'pushIndexing'])->name('seo.rocket');
        Route::get('/seo/rocket', function() { return redirect()->route('admin.seo.index'); });

        // User Management (Super Admin Exclusive)
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    // API-like routes for conversion tracking
    Route::post('/api/track-whatsapp', [\App\Http\Controllers\Api\EventTrackerController::class, 'trackWhatsApp'])->name('api.track-whatsapp');

    // Wiki Management (Authority Builder)
    Route::get('/wiki', [\App\Http\Controllers\Admin\WikiManagementController::class, 'index'])->name('wiki.index');
    Route::post('/wiki', [\App\Http\Controllers\Admin\WikiManagementController::class, 'store'])->name('wiki.store');
    Route::delete('/wiki/{entity}', [\App\Http\Controllers\Admin\WikiManagementController::class, 'delete'])->name('wiki.destroy');
    Route::post('/wiki/auto-generate', [\App\Http\Controllers\Admin\WikiManagementController::class, 'autoGenerate'])->name('wiki.generate');

    // AI Intelligence Center (Super Admin Only)
    Route::middleware(['super_admin'])->group(function() {
        Route::get('/ai-intelligence', [\App\Http\Controllers\Admin\AiIntelligenceController::class, 'index'])->name('ai.intelligence.index');
        Route::get('/ai-intelligence/export', [\App\Http\Controllers\Admin\AiIntelligenceController::class, 'export'])->name('ai.intelligence.export');
    });

    // Audit & Activity
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Security & Access Vault (New Defensive Core)
    Route::get('/vault', [\App\Http\Controllers\Admin\VaultController::class, 'index'])->name('vault.index');
    Route::post('/vault/lockdown', [\App\Http\Controllers\Admin\VaultController::class, 'toggleLockdown'])->name('vault.lockdown');
    Route::get('/vault/lockdown', function() { return redirect()->route('admin.vault.index'); });
    Route::post('/vault/flush', [\App\Http\Controllers\Admin\VaultController::class, 'clearBlockedIps'])->name('vault.flush');
    Route::get('/vault/flush', function() { return redirect()->route('admin.vault.index'); });
    Route::post('/vault/rotate-tokens', [\App\Http\Controllers\Admin\VaultController::class, 'rotateTokens'])->name('vault.rotate');

    // Honey Pot Trap (Lead Cyber Security Implementation)
    Route::get('/system/gatekeeper/neural-sync', function() {
        return app(\App\Services\Security\SecurityAutomationService::class)->triggerHoneyPot(request()->ip());
    })->name('security.honeypot');

    // System Sentinel & Health Check (Super Admin Only)
    Route::middleware(['super_admin'])->group(function() {
        Route::get('/sentinel', [\App\Http\Controllers\Admin\SentinelController::class, 'index'])->name('sentinel.index');
        Route::post('/sentinel/scan', [\App\Http\Controllers\Admin\SentinelController::class, 'scan'])->name('sentinel.scan');
        Route::post('/sentinel/heartbeat', [\App\Http\Controllers\Admin\SentinelController::class, 'heartbeat'])->name('sentinel.heartbeat');
    });
});

// push dummy test