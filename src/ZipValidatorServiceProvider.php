<?php

namespace Orkhanahmadov\ZipValidator;

use Illuminate\Support\ServiceProvider;

class ZipValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'zipValidator');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/zipValidator'),
            ], 'lang');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
