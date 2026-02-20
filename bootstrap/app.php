<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityShield::class);
        $middleware->append(\App\Http\Middleware\PreRenderMiddleware::class);
        $middleware->append(\App\Http\Middleware\TrackVisitors::class);
        $middleware->append(\App\Http\Middleware\SeoRedirectMiddleware::class);
        
        $middleware->validateCsrfTokens(except: [
            'admin/api/track-whatsapp',
            'api/phantom/introspect'
        ]);
        
        $middleware->alias([
            'track' => \App\Http\Middleware\TrackVisitors::class,
            'shield' => \App\Http\Middleware\SecurityShield::class,
            'super_admin' => \App\Http\Middleware\SuperAdminOnly::class,
            'audit' => \App\Http\Middleware\AdminAuditLogger::class,
            'phantom' => \App\Http\Middleware\PhantomExchangeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Sentinel Core Observability
        $schedule->command('sentinel:scan')->everyFiveMinutes();
        $schedule->command('sentinel:report')->mondays()->at('08:00');
        
        // Phantom L2 Purge & Cold Storage Archiving
        $schedule->command('system:housekeeping')
                 ->dailyAt('03:30')
                 ->timezone('Asia/Jakarta')
                 ->withoutOverlapping();
                 
        $schedule->command('phantom:reclaim')
                 ->dailyAt('03:00')
                 ->timezone('Asia/Jakarta')
                 ->withoutOverlapping();
                 
        // Automated Backup ke Google Drive (01:00 WIB setiap hari)
        $schedule->command('backup:run --only-db')
                 ->dailyAt('01:00')
                 ->timezone('Asia/Jakarta')
                 ->withoutOverlapping()
                 ->onSuccess(function () {
                     \Illuminate\Support\Facades\Cache::put('last_successful_backup', now());
                     \Illuminate\Support\Facades\Log::info('[BACKUP] Google Drive sync successful.');
                 })
                 ->onFailure(function () {
                     \Illuminate\Support\Facades\Log::critical('[BACKUP] Google Drive sync FAILED.');
                 });

        // Cleanup backup lama (jalankan setelah backup)
        $schedule->command('backup:clean')
                 ->dailyAt('01:30')
                 ->timezone('Asia/Jakarta');
    })
    ->create();
