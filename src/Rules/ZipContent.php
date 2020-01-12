<?php

namespace Orkhanahmadov\ZipValidator\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Orkhanahmadov\ZipValidator\Exceptions\ZipException;
use ZipArchive;

class ZipContent implements Rule
{
    /**
     * @var ZipArchive
     */
    private $zip;
    /**
     * @var Collection
     */
    private $files;
    /**
     * @var Collection
     */
    private $failedFiles;

    /**
     * Create a new rule instance.
     *
     * @param array|string $files
     */
    public function __construct($files)
    {
        $this->zip = new ZipArchive();
        $this->files = is_array($files) ? collect($files) : collect(func_get_args());
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param UploadedFile $zip
     * @return bool
     */
    public function passes($attribute, $zip): bool
    {
        $content = $this->readContent($zip);

        $this->failedFiles = $this->files->reject(function ($value, $key) use ($content) {
            $names = $content->pluck('name');

            if (! is_int($value)) {
                return $names->contains($value);
            }

            if (! $names->contains($key)) {
                return false;
            }

            return $content->firstWhere('name', $key)['size'] <= $value;
        });

        return ! $this->failedFiles->count();
    }

    /**
     * Reads ZIP file content and returns collection with result.
     *
     * @param UploadedFile $value
     * @return Collection
     */
    private function readContent(UploadedFile $value): Collection
    {
        $zipOpen = $this->zip->open($value->path());
        throw_unless($zipOpen === true, new ZipException($zipOpen));

        $content = collect();
        for ($i = 0; $i < $this->zip->count(); $i++) {
            $content->add($this->zip->statIndex($i));
        }

        $this->zip->close();

        return $content;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('zipValidator::messages.failed', [
            'files' => $this->failedFiles->implode(', '),
        ]);
    }
}
