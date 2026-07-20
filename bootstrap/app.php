<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

// Customize storage path for Vercel serverless environment
if (isset($_ENV['VERCEL_URL']) || isset($_SERVER['VERCEL_URL'])) {
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
        mkdir($storagePath . '/framework/views', 0755, true);
        mkdir($storagePath . '/framework/cache/data', 0755, true);
        mkdir($storagePath . '/framework/sessions', 0755, true);
        mkdir($storagePath . '/logs', 0755, true);
    }
    $app->useStoragePath($storagePath);
}

return $app;
