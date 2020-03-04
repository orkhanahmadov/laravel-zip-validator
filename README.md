# Laravel ZIP file validator

[![Latest Stable Version](https://poser.pugx.org/orkhanahmadov/laravel-zip-validator/v/stable)](https://packagist.org/packages/orkhanahmadov/laravel-zip-validator)
[![Latest Unstable Version](https://poser.pugx.org/orkhanahmadov/laravel-zip-validator/v/unstable)](https://packagist.org/packages/orkhanahmadov/laravel-zip-validator)
[![Total Downloads](https://img.shields.io/packagist/dt/orkhanahmadov/laravel-zip-validator)](https://packagist.org/packages/orkhanahmadov/laravel-zip-validator)
[![GitHub license](https://img.shields.io/github/license/orkhanahmadov/laravel-zip-validator.svg)](https://github.com/orkhanahmadov/laravel-zip-validator/blob/master/LICENSE.md)

[![Build Status](https://travis-ci.org/orkhanahmadov/laravel-zip-validator.svg?branch=master)](https://travis-ci.org/orkhanahmadov/laravel-zip-validator)
[![Test Coverage](https://api.codeclimate.com/v1/badges/588a51182465fa590e49/test_coverage)](https://codeclimate.com/github/orkhanahmadov/laravel-zip-validator/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/588a51182465fa590e49/maintainability)](https://codeclimate.com/github/orkhanahmadov/laravel-zip-validator/maintainability)
[![Quality Score](https://img.shields.io/scrutinizer/g/orkhanahmadov/laravel-zip-validator.svg)](https://scrutinizer-ci.com/g/orkhanahmadov/laravel-zip-validator)
[![StyleCI](https://github.styleci.io/repos/232924943/shield?branch=master)](https://github.styleci.io/repos/232924943)

Custom Laravel validation rule for checking ZIP file content.

## Requirements

- Laravel **^6.0** or **^7.0**
- PHP **7.2** or higher with `zip` extension enabled

Use version `1.5.0` for Laravel 5.8 support.

## Installation

You can install the package via composer:

```bash
composer require orkhanahmadov/laravel-zip-validator
```

## Usage

Use `ZipContent` rule with list of required files.

``` php
use Orkhanahmadov\ZipValidator\Rules\ZipContent;

public function rules()
{
    return [
        'file' => [
            'required',
            'file',
            'mimes:zip',
            new ZipContent('thumb.jpg', 'assets/logo.png'),
        ],
    ];
}
```

Pass list of required files/folders to the constructor of the validator.

You can pass files as different constructor arguments or as array.
If files are nested inside folders, pass relative path to file.

Validator will fail if any of the passed files does not exist in ZIP archive.

### Validating maximum file size

Validator also allows checking maximum size of each file inside ZIP archive.

Simply pass file name as array key and maximum size as value:
``` php
new ZipContent(['thumb.jpg' => 100000]);
```

Validator in above example will look for `thumb.jpg` file with maximum size of 100000 bytes (100KB).

You can also mix multiple files with name-only or name+size validation:
``` php
new ZipContent(['thumb.jpg' => 100000, 'logo.png']);
```

### Multiple files with "OR" validation

You can also pass multiple files with `|` symbol, if any of them exist in ZIP file validator will succeed.
``` php
new ZipContent('thumb.jpg|thumb.png|thumb.svg');
```

Validator in above example will look if `thumb.jpg`, `thumb.png` or `thumb.svg` file exists in ZIP.

Of course, you can also validate file size with "OR" validation:
``` php
new ZipContent(['thumb.jpg|thumb.png' => 100000]);
```

Above example will look if `thumb.jpg` or `thumb.png` file exists in ZIP and its file size is not bigger than 100000 bytes (100KB).

**Important** to keep in mind that when using "OR" validation with additional file size validation, 
validator will compare file size with the first matching element in ZIP archive.

### Rejecting empty files

By default, validator only checks if file with given name exists, 
it will return true even if file with matching name is empty (has size of 0 bytes).

You can pass array of files as first argument and 
`false` as second argument to constructor if you want validator to reject files with 0 bytes.
``` php
new ZipContent(['thumb.jpg', 'style.css'], false);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email ahmadov90@gmail.com instead of using the issue tracker.

## Credits

- [Orkhan Ahmadov](https://github.com/orkhanahmadov)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
