<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Illuminate\Http\UploadedFile;
use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\ZipValidator\ZipValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @var string
     */
    protected $filePath;
    /**
     * @var UploadedFile
     */
    protected $uploadedFile;

    protected function getPackageProviders($app)
    {
        return [
            ZipValidatorServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->filePath = __DIR__ . '/__fixtures__/file.zip';
        $this->uploadedFile = new UploadedFile(__DIR__ . '/__fixtures__/file.zip', 'file.zip');
    }
}
