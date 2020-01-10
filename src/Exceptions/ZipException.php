<?php

namespace Orkhanahmadov\LaravelZipValidator\Exceptions;

class ZipException extends \RuntimeException
{
    public function __construct($zipErrorCode)
    {
        parent::__construct(
            'Could not open file. ZIP file error: ' . $zipErrorCode,
            0,
            null
        );
    }
}
