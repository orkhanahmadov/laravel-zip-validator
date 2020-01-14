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
     * @var bool|string
     */
    private $allowEmpty;
    /**
     * @var Collection
     */
    private $failedFiles;

    /**
     * Create a new rule instance.
     *
     * @param array|string $files
     * @param bool|string $allowEmpty
     */
    public function __construct($files, $allowEmpty = true)
    {
        $this->zip = new ZipArchive();
        $this->files = is_bool($allowEmpty) ? collect($files) : collect(func_get_args());
        $this->allowEmpty = $allowEmpty;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param UploadedFile $zipFile
     * @return bool
     */
    public function passes($attribute, $zipFile): bool
    {
        $zipContent = $this->readContent($zipFile);

        $this->failedFiles = $this->files->reject(function ($value, $key) use ($zipContent) {
            return $this->validate($zipContent, $value, $key);
        });

        return $this->failedFiles->count() === 0;
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
     * @param Collection $zipContent
     * @param string|int $value
     * @param string|int $key
     *
     * @return bool
     */
    public function validate(Collection $zipContent, $value, $key): bool
    {
        if (! is_int($value)) {
            $entityName = $this->contains($zipContent->pluck('name'), $value);

            if ($this->allowEmpty) {
                return (bool) $entityName;
            }

            return $zipContent->firstWhere('name', $entityName)['size'] > 0;
        }

        $entityName = $this->contains($zipContent->pluck('name'), $key);
        if (! $entityName) {
            return false;
        }

        $entitySize = $zipContent->firstWhere('name', $entityName)['size'];

        if ($this->allowEmpty) {
            return $entitySize <= $value;
        }

        return $entitySize > 0 && $entitySize <= $value;
    }

    /**
     * Checks if file name exists in ZIP file. Returns matching file name, null otherwise.
     *
     * @param Collection $names
     * @param string $search
     *
     * @return string|null
     */
    public function contains(Collection $names, string $search): ?string
    {
        $options = explode('|', $search);

        return $names->first(function ($name) use ($options) {
            return in_array($name, $options);
        });
    }

    /**
     * Reads ZIP file content and returns collection with result.
     *
     * @param UploadedFile $zipFile
     *
     * @return Collection
     */
    private function readContent(UploadedFile $zipFile): Collection
    {
        $zipOpen = $this->zip->open($zipFile->path());
        throw_unless($zipOpen === true, new ZipException($zipOpen));

        $content = collect();
        for ($i = 0; $i < $this->zip->count(); $i++) {
            $content->add($this->zip->statIndex($i));
        }

        $this->zip->close();

        return $content;
    }
}
