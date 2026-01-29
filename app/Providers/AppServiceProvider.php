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
        if (\Illuminate\Support\Str::startsWith(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        } elseif (\Illuminate\Support\Str::startsWith(config('app.url'), 'http://')) {
            \Illuminate\Support\Facades\URL::forceScheme('http');
        }
    }
}
