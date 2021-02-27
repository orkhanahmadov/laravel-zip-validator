<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Illuminate\Support\Facades\Lang;
use Orkhanahmadov\ZipValidator\Rules\ZipContent;

class ZipContentTest extends TestCase
{
    public function test_returns_true_when_required_string_list_of_files_exist()
    {
        $this->assertTrue(
            (new ZipContent('dummy.pdf', false))
                ->passes('attribute', $this->uploadedFile)
        );

        $this->assertTrue(
            (new ZipContent('dummy.pdf', 'image.png', 'folder_1/text_file.txt'))
                ->passes('attribute', $this->uploadedFile)
        );

        $this->assertTrue(
            (new ZipContent([
                'dummy.pdf',
                'image.png',
                'folder_1/text_file.txt',
            ]))->passes('attribute', $this->uploadedFile)
        );
    }

    public function test_default_error_from_translations()
    {
        $rule = new ZipContent([
            'dummy.pdf',
            'image_2.png',
            'folder_1/text_file.png',
        ]);

        $this->assertFalse($rule->passes('attribute', $this->uploadedFile));
        $this->assertSame(
            'Following files in ZIP archive do not meet requirements: image_2.png, folder_1/text_file.png',
            $rule->message()
        );
    }

    public function test_returns_false_when_required_list_of_files_do_not_exist()
    {
        Lang::addLines([
            'messages.failed' => ':files',
        ], Lang::getLocale(), 'zipValidator');

        $rule = new ZipContent([
            'dummy.pdf',
            'image_2.png',
            'folder_1/text_file.png',
        ]);

        $this->assertFalse($rule->passes('attribute', $this->uploadedFile));
        $this->assertSame(
            'image_2.png, folder_1/text_file.png',
            $rule->message()
        );
    }
}
