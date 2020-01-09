<?php

namespace Orkhanahmadov\LaravelZipValidator;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ZipContent implements Rule
{
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
     * @param string|null $storage
     */
    public function __construct(?string $storage = null)
    {
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
