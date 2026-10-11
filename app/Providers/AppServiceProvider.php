<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fortify 1.41+ merges its own 'passkeys' config over laravel/passkeys'
        // config/passkeys.php, breaking throttle/redirect/middleware.
        // Force our values AFTER all providers have booted.
        config([
            'passkeys.throttle'              => 'throttle:6,1',
            'passkeys.management_middleware' => [],
            'passkeys.redirect'              => '/dashboard',
        ]);

        // Safety: register named limiter in case any code uses throttle:passkeys
        RateLimiter::for('passkeys', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });
    }
}