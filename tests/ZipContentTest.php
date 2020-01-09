<?php

namespace Orkhanahmadov\LaravelZipValidator\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Lang;
use Orkhanahmadov\LaravelZipValidator\Rules\ZipContent;

class ZipContentTest extends TestCase
{
    public function test_returns_true_when_required_string_list_of_files_exist()
    {
        $this->assertTrue(
            (new ZipContent('dummy.pdf,image.png,folder_1/text_file.txt'))
                ->passes(
                    'attribute',
                    new UploadedFile(__DIR__ . '/__fixtures__/file.zip', 'file.zip')
                )
        );

        $this->assertTrue(
            (new ZipContent([
                'dummy.pdf',
                'image.png',
                'folder_1/text_file.txt',
            ]))->passes(
                'attribute',
                new UploadedFile(__DIR__ . '/__fixtures__/file.zip', 'file.zip')
            )
        );
    }

    public function test_returns_false_when_required_list_of_files_do_not_exist()
    {
        Lang::addLines([
            'messages.not_found' => 'Following files does not exist in ZIP file: :files',
        ], Lang::getLocale(), 'zip-validator');

        $rule = new ZipContent([
            'dummy.pdf',
            'image_2.png',
            'folder_1/text_file.png',
        ]);

        $this->assertFalse(
            $rule->passes(
                'attribute',
                new UploadedFile(__DIR__ . '/__fixtures__/file.zip', 'file.zip')
            )
        );
        $this->assertSame(
            'Following files does not exist in ZIP file: image_2.png, folder_1/text_file.png',
            $rule->message()
        );
    }
}
