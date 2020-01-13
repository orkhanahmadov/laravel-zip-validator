<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Illuminate\Http\UploadedFile;
use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\ZipValidator\ZipValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @var UploadedFile
     */
    protected $file;

    protected function getPackageProviders($app)
    {
        return [
            ZipValidatorServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->file = new UploadedFile(__DIR__ . '/__fixtures__/file.zip', 'file.zip');
    }
}
