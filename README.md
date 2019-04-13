# PHP Кодогенератор

Кодогенератор для PHP, написанный на PHP.

Позволяет, на основании шаблонов файлов, генерировать файлы
по заданным параметрам.
Так же, дополняет уже существующие классы, при возникновении
конфликтов.


## Установка

```bash
composer require octava/generator --dev
```

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


### TODO

#### Traits
- Расположение в классе
- Область видимости в traits

#### Константы
- Расположение в классе
- Перенос комментариев к константам

#### Свойства класса
- Свойства
- Расположение в классе

#### Методы класса
- Методы
- Расположение в классе

#### Конструктор
- Расширение конструктора
- Обновление PHPDoc