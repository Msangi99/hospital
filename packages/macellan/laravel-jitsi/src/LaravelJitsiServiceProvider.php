<?php

namespace Macellan\LaravelJitsi;

use Illuminate\Support\ServiceProvider;

class LaravelJitsiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/jitsi.php', 'jitsi');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/jitsi.php' => config_path('jitsi.php'),
        ], 'laravel-jitsi-config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-jitsi');
    }
}
