<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS when behind a reverse proxy (Railway, Heroku, etc.)
        // Railway always sends X-Forwarded-Proto: https
        if (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
            || isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            || config('app.env') === 'production'
        ) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
    }
}
