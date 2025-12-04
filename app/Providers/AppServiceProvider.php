<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Detect HTTPS behind Railway reverse proxy
        if ($this->app->environment('production')) {

            // Jika request datang via proxy HTTPS (X-Forwarded-Proto), paksa scheme ke https
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                URL::forceScheme('https');
            }

            // Cara umum: tetap force https
            URL::forceScheme('https');
        }
    }
}
