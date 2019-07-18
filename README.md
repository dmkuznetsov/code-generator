# PHP Кодогенератор

Кодогенератор для PHP, написанный на PHP.

Позволяет, на основании шаблонов файлов, генерировать файлы
по заданным параметрам.
Так же, дополняет уже существующие классы, при возникновении
конфликтов.


## Установка

```bash
composer require octava/code-generator --dev
```

## Предопределенные переменные шаблонов

`_CG_FILE_NAME_` - имя файла (`TestController` для файла `TestController.php`)
`_CG_FILE_NAME_UCFIRST_` - имя файла с большой буквы (`TestController` для файла `TestController.php`)
`_CG_FILE_NAME_LCFIRST_` - имя файла с маленькой буквы (`testController` для файла `TestController.php`)
`_CG_FILE_BASENAME_` => $basename  (`TestController` для файла `TestController.php`)
`_CG_FILE_DIR_` => $dir,
`_CG_FILE_PATH_` => $dir.DIRECTORY_SEPARATOR.$filename,
`_CG_FILE_EXTENSION_` - расширение файла (`php` для файла `TestController.php`)
`_CG_NAMESPACE_` - namespace (`App\UI` для файла `src/App/UI/TestController.php`)


## Примеры использования

```php
<?php
use Octava\CodeGenerator\CodeGenerator;
use Octava\CodeGenerator\Configuration;
use Octava\CodeGenerator\Writer;
use Octava\CodeGenerator\Processor\PhpClassProcessor;
use Octava\CodeGenerator\Processor\SimpleProcessor;
use Octava\CodeGenerator\TemplateFactory;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

// Определяем конфигурацию
$configuration = new Configuration(
    '_templates', // путь к шаблонам
    'src' // путь к результату
);

$generator = new CodeGenerator($configuration, new Writer());

// Ищем шаблоны
$templates = $generator->scan(new TemplateFactory($configuration));
// Определяем список процессоров для обработки шаблонов
$processors = [
    new SimpleProcessor(),
    new PhpClassProcessor((new ParserFactory)->create(ParserFactory::PREFER_PHP7), new Standard())
];
// Запускаем генерацию
$generator->generate($templates, $processors);
```

Примеры шаблонов для генерации смотрите в папке `tests/_templates`.


### TODO

#### Traits
- Область видимости в traits

#### Константы
- Перенос комментариев к константам

#### Методы класса
- Методы

#### Конструктор
- Расширение конструктора
- Обновление PHPDoc