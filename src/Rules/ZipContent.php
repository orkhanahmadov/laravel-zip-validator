<?php

namespace Orkhanahmadov\ZipValidator\Rules;

use Illuminate\Contracts\Validation\Rule;
use Orkhanahmadov\ZipValidator\Validator;

class ZipContent implements Rule
{
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var \Illuminate\Support\Collection
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
        $this->validator = new Validator(
            is_bool($allowEmpty) ? $files : func_get_args(),
            $allowEmpty
        );
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param \Illuminate\Http\UploadedFile $zipFile
     * @return bool
     */
    public function passes($attribute, $zipFile): bool
    {
        $this->failedFiles = $this->validator->validate($zipFile->path());

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
}
