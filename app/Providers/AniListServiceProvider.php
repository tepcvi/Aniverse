<?php

namespace App\Providers;

use App\Services\AniListService;
use Illuminate\Support\ServiceProvider;

class AniListServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AniListService::class, function () {
            return new AniListService();
        });
    }

    public function boot(): void
    {
        //
    }
}
