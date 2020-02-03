<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Orkhanahmadov\ZipValidator\Validator;

class ValidatorTest extends TestCase
{
    public function test_validates_with_string_and_array_list_of_files()
    {
        $this->assertCount(
            0,
            Validator::rules('dummy.pdf', false)->validate($this->filePath)
        );

        $this->assertCount(
            0,
            Validator::rules(['dummy.pdf', 'image.png', 'folder_1/text_file.txt'])->validate($this->filePath)
        );

        $failedFiles = Validator::rules(['dummy.pdf', 'image.png', 'folder_1/text_file_2.txt'])->validate($this->filePath);
        $this->assertCount(1, $failedFiles);
        $this->assertSame('folder_1/text_file_2.txt', $failedFiles->first());
    }

    public function test_file_size_check()
    {
        $this->assertCount(
            0,
            Validator::rules([
                'dummy.pdf' => 14000, // 13264
                'folder_1/text_file.txt' => 16
            ])->validate($this->filePath)
        );
    }

    public function test_returns_true_if_one_of_the_options_exist()
    {
        $this->assertCount(
            0,
            Validator::rules([
                'dummy.pdf' => 14000,
                'folder_1/text.text|folder_1/text_file.txt' => 16,
            ])->validate($this->filePath)
        );
    }

    public function test_returns_false_if_file_size_does_not_meet_the_requirements()
    {
        $failedFiles = Validator::rules(['dummy.pdf' => 14000, 'folder_1/text_file.txt' => 10])->validate($this->filePath);

        $this->assertCount(1, $failedFiles);
        $this->assertSame('folder_1/text_file.txt', $failedFiles->first());
    }

    public function test_returns_false_when_valid_file_size_but_wrong_file_name_passed()
    {
        $failedFiles = Validator::rules(['dummy.pdf' => 14000, 'folder_1/text_file_2.txt' => 16])->validate($this->filePath);

        $this->assertCount(1, $failedFiles);
        $this->assertSame('folder_1/text_file_2.txt', $failedFiles->first());
    }

    public function test_true_when_valid_files_passed_as_mixed_simple_array_and_size_check()
    {
        $this->assertCount(
            0,
            Validator::rules(['dummy.pdf', 'folder_1/text_file.txt' => 17])->validate($this->filePath)
        );
    }

    public function test_false_when_invalid_file_passed_with_valid_file_and_size()
    {
        $failedFiles = Validator::rules(['dummy.pdf', 'folder_1/text_file.txt' => 10])->validate($this->filePath);

        $this->assertCount(
            1,
            $failedFiles
        );
        $this->assertSame('folder_1/text_file.txt', $failedFiles->first());
    }

    public function test_validate_method()
    {
        $zipContent = collect([
            ['name' => 'one', 'size' => 10],
            ['name' => 'two', 'size' => 20],
            ['name' => 'ten', 'size' => 0],
        ]);

        $this->assertTrue(Validator::rules([])->checkFile($zipContent, 'two', 0));
        $this->assertTrue(Validator::rules([])->checkFile($zipContent, 10, 'one'));
        $this->assertTrue(Validator::rules([])->checkFile($zipContent, 'ten', 0));
        $this->assertTrue(Validator::rules([])->checkFile($zipContent, 'ten', 10));
        $this->assertFalse(Validator::rules([])->checkFile($zipContent, 9, 'one'));
        $this->assertFalse(Validator::rules([])->checkFile($zipContent, 'three', 0));
        $this->assertFalse(Validator::rules([])->checkFile($zipContent, 30, 'three'));
        $this->assertFalse(Validator::rules([], false)->checkFile($zipContent, 'ten', 0));
        $this->assertFalse(Validator::rules([], false)->checkFile($zipContent, 10, 'ten'));
    }

    public function test_contains_method()
    {
        $names = collect(['one', 'two']);
        $zip = new Validator([]);

        $this->assertSame('two', $zip->contains($names, 'two'));
        $this->assertSame('one', $zip->contains($names, 'one|four'));
        $this->assertSame('one', $zip->contains($names, 'one|two'));
        $this->assertSame('one', $zip->contains($names, 'two|one'));
        $this->assertNull($zip->contains($names, 'three|four'));
        $this->assertNull($zip->contains($names, 'three'));
    }
}
