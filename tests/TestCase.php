<?php

namespace Orkhanahmadov\LaravelZipValidator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\LaravelZipValidator\LaravelZipValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelZipValidatorServiceProvider::class,
        ];
    }
}
