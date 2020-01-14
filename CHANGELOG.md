# Changelog

All notable changes to `laravel-zip-validator` will be documented in this file

## 1.4.0 - 2020-01-14

- "Allow empty" rule introduced as second constructor argument. By default set to `true`

## 1.3.0 - 2020-01-13

- "OR" file validation added.
You can pass multiple files with `|`, 
validator will succeed if any of the files exist in ZIP file. Example: `document.pdf|homework.doc|my-doc.pdf`

## 1.2.2 - 2020-01-12

- ZIP content collection simplified with `statIndex()` usage

## 1.2.1 - 2020-01-12

- PHP ZIP helper methods replaced with ZipArchive class methods

## 1.2.0 - 2020-01-10

- Ability to check file size in ZIP

## 1.1.0 - 2020-01-10

- Namespace changes

## 1.0.0 - 2020-01-10

- Initial release
