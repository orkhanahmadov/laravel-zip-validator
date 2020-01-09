<?php

namespace Orkhanahmadov\LaravelZipValidator\Tests;

use Illuminate\Http\UploadedFile;
use Orkhanahmadov\LaravelZipValidator\Rules\ZipContent;

class ZipContentTest extends TestCase
{
    public function test_returns_true_when_required_string_list_of_files_exist()
    {
        $this->assertTrue(
            (new ZipContent('dummy.pdf,image.png,folder_1/text_file.txt'))
                ->passes(
                    'attribute',
                    new UploadedFile(__DIR__ . '/__fixtures__/valid.zip', 'file.zip')
                )
        );

        $this->assertTrue(
            (new ZipContent([
                'dummy.pdf',
                'image.png',
                'folder_1/text_file.txt',
            ]))->passes(
                'attribute',
                new UploadedFile(__DIR__ . '/__fixtures__/valid.zip', 'file.zip')
            )
        );
    }

    public function test_returns_false_when_required_list_of_files_do_not_exist()
    {
        $rule = new ZipContent([
            'dummy.pdf',
            'image.png',
            'folder_1/text_file.txt',
        ]);

        $this->assertFalse(
            $rule->passes(
                'attribute',
                new UploadedFile(__DIR__ . '/__fixtures__/invalid.zip', 'file.zip')
            )
        );
        $this->assertSame('', $rule->message());
    }
}
