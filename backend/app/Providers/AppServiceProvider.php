<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Render terminates TLS for us, so the app receives plain HTTP. Force the
        // HTTPS scheme in production so generated asset/URL helpers (asset(), url())
        // emit https:// links and avoid mixed-content warnings in the browser.
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
