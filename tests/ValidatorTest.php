<?php

namespace Orkhanahmadov\ZipValidator\Tests;

use Orkhanahmadov\ZipValidator\Validator;

class ValidatorTest extends TestCase
{
    public function testValidatesWithStringAndArrayListOfFiles()
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

    public function testFileSizeCheck()
    {
        $this->assertCount(
            0,
            Validator::rules([
                'dummy.pdf' => 14000, // 13264
                'folder_1/text_file.txt' => 16
            ])->validate($this->filePath)
        );
    }

    public function testCheckWithWildcard()
    {
        $this->assertCount(
            0,
            Validator::rules(['*.pdf'])->validate($this->filePath) // dummy.pdf exists
        );

        $this->assertCount(
            0,
            Validator::rules(['*.doc|folder_1/*.txt'])->validate($this->filePath) // doc does not exist but txt does
        );

        $this->assertCount(
            1,
            Validator::rules(['*.doc|*.xls'])->validate($this->filePath) // both does not exist
        );

        $this->assertCount(
            1,
            Validator::rules(['*.pdf|*.xls' => 13000])->validate($this->filePath) // both does not exist
        );
    }

    public function testReturnsTrueIfOneOfTheOptionsExist()
    {
        $this->assertCount(
            0,
            Validator::rules([
                'dummy.pdf' => 14000,
                'folder_1/text.text|folder_1/text_file.txt' => 16,
            ])->validate($this->filePath)
        );
    }

    public function testReturnsFalseIfFileSizeDoesNotMeetTheRequirements()
    {
        $failedFiles = Validator::rules(['dummy.pdf' => 14000, 'folder_1/text_file.txt' => 10])->validate($this->filePath);

        $this->assertCount(1, $failedFiles);
        $this->assertSame('folder_1/text_file.txt', $failedFiles->first());
    }

    public function testReturnsFalseWhenValidFileSizeButWrongFileNamePassed()
    {
        $failedFiles = Validator::rules(['dummy.pdf' => 14000, 'folder_1/text_file_2.txt' => 16])->validate($this->filePath);

        $this->assertCount(1, $failedFiles);
        $this->assertSame('folder_1/text_file_2.txt', $failedFiles->first());
    }

    public function testTrueWhenValidFilesPassedAsMixedSimpleArrayAndSizeCheck()
    {
        $this->assertCount(
            0,
            Validator::rules(['dummy.pdf', 'folder_1/text_file.txt' => 17])->validate($this->filePath)
        );
    }

    public function testFalseWhenInvalidFilePassedWithValidFileAndSize()
    {
        $failedFiles = Validator::rules(['dummy.pdf', 'folder_1/text_file.txt' => 10])->validate($this->filePath);

        $this->assertCount(
            1,
            $failedFiles
        );
        $this->assertSame('folder_1/text_file.txt', $failedFiles->first());
    }

    public function testValidateMethod()
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

    public function testContainsMethod()
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
