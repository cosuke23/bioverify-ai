<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Biometric attempts are public, so limit them per IP to slow
        // down guessing and protect the CPU-bound recognition service.
        RateLimiter::for('recognition', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Ollama-backed chat, PDF questions and uploads.
        RateLimiter::for('ai', function (Request $request) {
            return Limit::perMinute(20)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                strtolower((string) $request->input('email')) . '|' . $request->ip()
            );
        });
    }
}
