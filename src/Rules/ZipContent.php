<?php

namespace Orkhanahmadov\LaravelZipValidator\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ZipContent implements Rule
{
    /**
     * @var Collection
     */
    private $files;
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $storage;
    /**
     * @var string
     */
    private $workingDirectory;
    /**
     * @var ZipArchive
     */
    private $archive;
    /**
     * @var Collection
     */
    private $failedFiles;

    /**
     * Create a new rule instance.
     *
     * @param array|string $files
     * @param string|null $storage
     */
    public function __construct($files, ?string $storage = null)
    {
        $this->files = is_array($files) ? collect($files) : collect(explode(',', $files));
        $this->storage = Storage::disk($storage ?: config('filesystems.default'));
        $this->workingDirectory = uniqid('zip-');
        $this->archive = new ZipArchive();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param UploadedFile $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        dd($value->path());

        $storedZipPath = $this->storage->putFile($this->workingDirectory = uniqid('zip-'), $value);
        $this->extractZip($storedZipPath);

        $this->failedFiles = $this->files->reject(function ($file) {
            return $this->storage->exists($this->workingDirectory . '/' . $file);
        });

        $this->storage->deleteDirectory($this->workingDirectory);

        return ! $this->failedFiles->count();
    }

    private function extractZip(string $path): void
    {
//        $file = file_get_contents();
//
//        dd($file);

        $zip = zip_open(file_get_contents('https://file-examples.com/wp-content/uploads/2017/02/zip_2MB.zip'));

//        dd(var_dump($zip));

//        throw_unless(
//            $zip === true,
//            new FileException('Could not open file.')
//        );

        while ($file = zip_read($zip)) {
            echo  zip_entry_name($file).PHP_EOL;
        }

        die;

        dd(zip_read($zip));

        $this->archive->extractTo($this->storage->path($this->workingDirectory));
        $this->archive->close();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('zip-validator::messages.not_found', [
            'files' => $this->failedFiles->implode(', '),
        ]);
    }
}
