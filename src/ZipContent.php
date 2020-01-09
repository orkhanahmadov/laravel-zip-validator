<?php

namespace Orkhanahmadov\LaravelZipValidator;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ZipContent implements Rule
{
    /**
     * @var array
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
        $this->files = is_array($files) ? $files : explode(',', $files);
        $this->storage = Storage::disk($storage);
        $this->archive = new ZipArchive();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        //
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
