<?php

namespace Orkhanahmadov\LaravelZipValidator\Tests;

use Orkhanahmadov\LaravelZipValidator\ZipContent;

class ZipContentTest extends TestCase
{
    public function test_returns_true_when_required_string_list_of_files_exist()
    {
        $validZip = file_get_contents(__DIR__ . '__fixtures__/valid.zip');

        $this->assertTrue(
            (new ZipContent())->passes('attribute', $validZip)
        );
    }

    public function test_returns_true_when_required_array_list_of_files_exist()
    {
        $this->markTestIncomplete();
    }

    public function test_returns_false_when_required_list_of_files_do_not_exist()
    {
        $this->markTestIncomplete();
    }
}
