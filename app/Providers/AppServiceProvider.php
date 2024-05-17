<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DistanceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DistanceService::class, function ($app) {
            return new DistanceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
