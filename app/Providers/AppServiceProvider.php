<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Exceptions\Auth\TooManyRequestsException;

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
        Model::unguard();

        // Register custom rate limiter for email verification resends
        RateLimiter::for('verification-resend', function ($request) {
            return Limit::perHour(3)->by($request->email . '|' . $request->ip())->response(function () {
                throw new TooManyRequestsException(
                    'You have sent too many verification email requests. Please try again in an hour.'
                );
            });
        });
    }
}
