<?php

namespace Orkhanahmadov\ZipValidator;

use Illuminate\Support\Collection;
use Orkhanahmadov\ZipValidator\Exceptions\ZipException;
use ZipArchive;

class Validator
{
    /**
     * @var Collection
     */
    private $files;
    /**
     * @var bool
     */
    private $allowEmpty;

    /**
     * ZipValidator constructor.
     *
     * @param array|string $files
     * @param bool $allowEmpty
     */
    public function __construct($files, bool $allowEmpty = true)
    {
        $this->files = Collection::make($files);
        $this->allowEmpty = $allowEmpty;
    }

    /**
     * Static function instantiate Validator class with given rules
     *
     * @param array|string $files
     *
     * @param bool $allowEmpty
     *
     * @return Validator
     */
    public static function rules($files, bool $allowEmpty = true): Validator
    {
        return new static($files, $allowEmpty);
    }

    /**
     * Validates ZIP content with given file path.
     *
     * @param string $filePath
     *
     * @return Collection
     */
    public function validate(string $filePath): Collection
    {
        $zipContent = $this->readContent($filePath);

        return $this->files
            ->reject(function ($value, $key) use ($zipContent) {
                return $this->checkFile($zipContent, $value, $key);
            })->map(function ($value, $key) {
                return is_int($key) ? $value : $key;
            });
    }

    /**
     * @param Collection $zipContent
     * @param string|int $value
     * @param string|int $key
     *
     * @return bool
     */
    public function checkFile(Collection $zipContent, $value, $key): bool
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
     * @param string $filePath
     *
     * @return Collection
     */
    private function readContent(string $filePath): Collection
    {
        $zip = new ZipArchive();
        $zipOpen = $zip->open($filePath);
        throw_unless($zipOpen === true, new ZipException($zipOpen));

        $content = collect();
        for ($i = 0; $i < $zip->count(); $i++) {
            $content->add($zip->statIndex($i));
        }

        $zip->close();

        return $content;
    }
}
