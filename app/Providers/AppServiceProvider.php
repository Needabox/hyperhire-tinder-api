<?php

namespace App\Providers;

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
        // Force HTTPS di production untuk mengatasi mixed content error
        if (app()->environment('production') || request()->isSecure()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
