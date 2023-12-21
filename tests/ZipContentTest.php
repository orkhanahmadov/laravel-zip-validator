<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Illuminate\Support\Facades\Lang;
use Orkhanahmadov\ZipValidator\Rules\ZipContent;

class ZipContentTest extends TestCase
{
    public function testReturnsTrueWhenRequiredStringListOfFilesExist()
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

    public function testDefaultErrorFromTranslations()
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

    public function testReturnsFalseWhenRequiredListOfFilesDoNotExist()
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
