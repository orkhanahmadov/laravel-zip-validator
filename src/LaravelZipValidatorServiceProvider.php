<?php

namespace Orkhanahmadov\LaravelZipValidator;

use Illuminate\Support\ServiceProvider;

class LaravelZipValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'zip-validator');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/zip-validator'),
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
