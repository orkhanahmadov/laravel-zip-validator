<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Illuminate\Support\Facades\Lang;
use Orkhanahmadov\ZipValidator\Rules\ZipContent;

class ZipContentTest extends TestCase
{
    public function test_returns_true_when_required_string_list_of_files_exist()
    {
        $this->assertTrue(
            (new ZipContent('dummy.pdf', 'image.png', 'folder_1/text_file.txt'))
                ->passes('attribute', $this->file)
        );

        $this->assertTrue(
            (new ZipContent([
                'dummy.pdf',
                'image.png',
                'folder_1/text_file.txt',
            ]))->passes('attribute', $this->file)
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

        $this->assertFalse($rule->passes('attribute', $this->file));
        $this->assertSame(
            'image_2.png, folder_1/text_file.png',
            $rule->message()
        );
    }

    public function test_file_size_check()
    {
        $this->assertTrue(
            (new ZipContent([
                'dummy.pdf' => 14000, // 13264
                'folder_1/text_file.txt' => 16,
            ]))->passes('attribute', $this->file)
        );
    }

    public function test_returns_true_if_one_of_the_options_exist()
    {
        $this->assertTrue(
            (new ZipContent([
                'dummy.pdf' => 14000,
                'folder_1/text.text|folder_1/text_file.txt' => 16,
            ]))->passes('attribute', $this->file)
        );
    }

    public function test_returns_false_if_file_size_does_not_meet_the_requirements()
    {
        $this->assertFalse(
            (new ZipContent([
                'dummy.pdf' => 14000,
                'folder_1/text_file.txt' => 10,
            ]))->passes('attribute', $this->file)
        );
    }

    public function test_returns_false_when_valid_file_size_but_wrong_file_name_passed()
    {
        $this->assertFalse(
            (new ZipContent([
                'dummy.pdf' => 14000, // 13264
                'folder_1/text_file_2.txt' => 16,
            ]))->passes('attribute', $this->file)
        );
    }

    public function test_true_when_valid_files_passed_as_mixed_simple_array_and_size_check()
    {
        $this->assertTrue(
            (new ZipContent([
                'dummy.pdf',
                'folder_1/text_file.txt' => 17,
            ]))->passes('attribute', $this->file)
        );
    }

    public function test_false_when_invalid_file_passed_with_valid_file_and_size()
    {
        $this->assertFalse(
            (new ZipContent([
                'dummy.pdf',
                'folder_1/text_file.txt' => 10,
            ]))->passes('attribute', $this->file)
        );
    }

    public function test_validate_method()
    {
        $zipContent = collect([
            [
                'name' => 'one',
                'size' => 10,
            ],
            [
                'name' => 'two',
                'size' => 20,
            ],
        ]);
        $zip = new ZipContent('field');

        $this->assertTrue($zip->validate($zipContent, 'two', 0));
        $this->assertTrue($zip->validate($zipContent, 10, 'one'));
        $this->assertFalse($zip->validate($zipContent, 9, 'one'));
        $this->assertFalse($zip->validate($zipContent, 'three', 0));
        $this->assertFalse($zip->validate($zipContent, 30, 'three'));
    }

    public function test_contains_method()
    {
        $names = collect(['one', 'two']);
        $zip = new ZipContent('field');

        $this->assertSame('two', $zip->contains($names, 'two'));
        $this->assertSame('one', $zip->contains($names, 'one|four'));
        $this->assertNull($zip->contains($names, 'three|four'));
        $this->assertNull($zip->contains($names, 'three'));
    }
}
