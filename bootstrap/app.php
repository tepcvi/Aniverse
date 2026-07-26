<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (needed for Railway, Heroku, etc.)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

// Force HTTPS in production (Railway reverse proxy terminates SSL)
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    URL::forceScheme('https');
}

// Vercel serverless environment: rewrite all writables to /tmp
$onVercel = isset($_ENV['VERCEL']) || isset($_ENV['VERCEL_URL']) || isset($_SERVER['VERCEL_URL']) || isset($_SERVER['VERCEL']);
if ($onVercel) {
    $storagePath = '/tmp/storage';
    $dirs = [
        $storagePath,
        $storagePath . '/framework',
        $storagePath . '/framework/views',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/logs',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
    $app->useStoragePath($storagePath);

    // Ensure the sqlite file path can be created on first write
    $tmpSqlite = '/tmp/database.sqlite';
    if (!file_exists($tmpSqlite)) {
        @touch($tmpSqlite);
    }

    // Always force array cache on Vercel — filesystem cache is per-instance and
    // not shared across cold starts. Using array keeps the app stateless.
    @$_ENV['CACHE_STORE'] = 'array';
    @$_SERVER['CACHE_STORE'] = 'array';
}

return $app;
