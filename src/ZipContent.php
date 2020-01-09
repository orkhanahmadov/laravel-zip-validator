<?php

namespace Orkhanahmadov\LaravelZipValidator;

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
        $zipFile = $this->storage->putFileAs($directory = uniqid('zip-'), $value, 'file.zip');

        throw_unless(
            $this->archive->open($this->storage->path($zipFile)) === true,
            new FileException('Could not open file.')
        );

        $this->archive->extractTo($this->storage->path($directory));
        $this->archive->close();

        $this->storage->delete($zipFile);

        $failedFiles = $this->files->reject(function ($file) use ($directory) {
            return $this->storage->exists($directory . '/' . $file);
        });

        $this->storage->deleteDirectory($directory);

        return ! $failedFiles->count();
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
