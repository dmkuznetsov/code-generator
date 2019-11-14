# PHP Кодогенератор

Умный кодогенератор для PHP, написанный на PHP.

[![SymfonyInsight](https://insight.symfony.com/projects/ca91786d-2532-45da-b2b7-24acad77ad55/big.svg)](https://insight.symfony.com/projects/ca91786d-2532-45da-b2b7-24acad77ad55)

Позволяет, на основании шаблонов файлов, генерировать файлы по заданным параметрам.
Так же, умеет совмещать код в классах.


## Установка

```bash
composer require octava/code-generator --dev
```

## Предопределенные переменные шаблонов

`_CG_FILE_NAME_` - имя файла (`TestController` для файла `TestController.php`)

`_CG_FILE_NAME_UCFIRST_` - имя файла с большой буквы (`TestController` для файла `TestController.php`)

`_CG_FILE_NAME_LCFIRST_` - имя файла с маленькой буквы (`testController` для файла `TestController.php`)

`_CG_FILE_BASENAME_` => имя файла  (`TestController` для файла `TestController.php`)

`_CG_FILE_DIR_` => директория файла (`path/to/file` для файла `path/to/file/TestController.php`),

`_CG_FILE_PATH_` => путь к файлу без расширения (`path/to/file/TestController` для файла `path/to/file/TestController.php`),

`_CG_FILE_EXTENSION_` - расширение файла (`php` для файла `TestController.php`)

`_CG_FILE_NAMESPACE__` - расширение файла (`path\to\file` для файла `path/to/file/TestController.php`)


## Примеры использования

```php
<?php
use Octava\CodeGenerator\CodeGenerator;
use Octava\CodeGenerator\Configuration;
use Octava\CodeGenerator\Filesystem;
use Octava\CodeGenerator\Processor\PhpClassProcessor;
use Octava\CodeGenerator\Processor\SimpleProcessor;
use Octava\CodeGenerator\TemplateFactory;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

$configuration = new Configuration('base/templates/path', 'base/output/dir');
$configuration
    ->setTemplateVars([])
    ->addProcessor(new SimpleProcessor())
    ->addProcessor(new PhpClassProcessor((new ParserFactory)->create(ParserFactory::PREFER_PHP7), new Standard()))
;
$templateFactory = new TemplateFactory($configuration);
$codeGenerator = new CodeGenerator($this->configuration, new Filesystem());

$codeGenerator
    ->generate(
        $templateFactory->create(
            'src/Application/_CG_MODULE_/_CG_MODULE_Service.php',
            'src/Application/_CG_MODULE_/_CG_MODULE_Service.php',
            ['_CG_MODULE_' => 'MyFavourite']
        )
    );
```
