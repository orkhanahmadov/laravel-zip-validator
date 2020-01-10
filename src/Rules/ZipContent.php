<?php

namespace Orkhanahmadov\LaravelZipValidator\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Orkhanahmadov\LaravelZipValidator\Exceptions\ZipException;
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
        $content = $this->readZip($value);

        $this->failedFiles = $this->files->reject(function ($file) use ($content) {
            return $content->contains($file);
        });

        return ! $this->failedFiles->count();
    }

    private function readZip(UploadedFile $value): Collection
    {
        $zip = zip_open($value->path());

        throw_unless(!is_int($zip), new ZipException($zip));

        $content = collect();
        while ($file = zip_read($zip)) {
            $content->add(zip_entry_name($file));
        }

        zip_close($zip);

        return $content;
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
