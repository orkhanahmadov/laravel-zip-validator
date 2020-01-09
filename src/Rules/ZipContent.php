<?php

namespace Orkhanahmadov\LaravelZipValidator\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $storedZipPath = $this->storage->putFile($this->workingDirectory = uniqid('zip-'), $value);
        $this->extractZip($storedZipPath);

        $failedFiles = $this->files->reject(function ($file) {
            return $this->storage->exists($this->workingDirectory . '/' . $file);
        });

        $this->storage->deleteDirectory($this->workingDirectory);

        return ! $failedFiles->count();
    }

    private function extractZip(string $path): void
    {
        throw_unless(
            $this->archive->open($this->storage->path($path)) === true,
            new FileException('Could not open file.')
        );

        $this->archive->extractTo($this->storage->path($this->workingDirectory));
        $this->archive->close();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
