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
            $namesInsideZip = $content->pluck('name');

            if (! is_int($value)) {
                return $this->contains($namesInsideZip, $value);
            }

            $matchingName = $this->contains($namesInsideZip, $key);
            if (! $matchingName) {
                return false;
            }

            return $content->firstWhere('name', $matchingName)['size'] <= $value;
        });

        return ! $this->failedFiles->count();
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

    /**
     * Reads ZIP file content and returns collection with result.
     *
     * @param UploadedFile $value
     *
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
     * Checks if file name exists in ZIP file. Returns matching file name, null otherwise.
     *
     * @param Collection $namesInsideZip
     * @param string $search
     *
     * @return string|null
     */
    private function contains(Collection $namesInsideZip, string $search): ?string
    {
        $options = explode('|', $search);

        return $namesInsideZip->first(function ($name) use ($options) {
            return in_array($name, $options);
        });
    }
}
