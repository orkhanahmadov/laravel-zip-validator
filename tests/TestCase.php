<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\ZipValidator\ZipValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ZipValidatorServiceProvider::class,
        ];
    }
}
