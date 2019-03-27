<?php
declare(strict_types=1);

namespace Dm\Tests\CodeGenerator\Processor;

use Dm\CodeGenerator\Exception\NotEqualClassnameException;
use Dm\CodeGenerator\Exception\NotEqualNamespaceException;
use Dm\CodeGenerator\Processor\CombinePhpClassProcessor;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CombinePhpClassProcessorTest extends TestCase
{
    /**
     * @var CombinePhpClassProcessor
     */
    protected $processor;

    public function testNamespaceNotEqual(): void
    {
        $this->expectException(NotEqualNamespaceException::class);

        $originSource = <<<'PHP'
<?php
namespace App\OriginNamespace;

PHP;

        $templateSource = <<<'PHP'
<?php
namespace App\DifferentNamespace;

PHP;

        $this->processor->process($originSource, $templateSource);
    }

    public function testNamespaceEmptyOrigin(): void
    {
        $this->expectException(NotEqualNamespaceException::class);

        $originSource = <<<'PHP'
<?php

PHP;

        $templateSource = <<<'PHP'
<?php
namespace App\DifferentNamespace;

PHP;

        $this->processor->process($originSource, $templateSource);
    }

    public function testNamespaceEmptyTemplate(): void
    {
        $this->expectException(NotEqualNamespaceException::class);

        $originSource = <<<'PHP'
<?php
namespace App\OriginNamespace;

PHP;

        $templateSource = <<<'PHP'
<?php

PHP;

        $this->processor->process($originSource, $templateSource);
    }

    public function testClassnameNotEqual(): void
    {
        $this->expectException(NotEqualClassnameException::class);

        $originSource = <<<'PHP'
<?php
namespace App;

class OriginClassname {}

PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class DifferentClassname {}

PHP;

        $this->processor->process($originSource, $templateSource);
    }

    public function testClassnameNotEqualWithoutNamespace(): void
    {
        $this->expectException(NotEqualClassnameException::class);

        $originSource = <<<'PHP'
<?php

class OriginClassname {}

PHP;

        $templateSource = <<<'PHP'
<?php

class DifferentClassname {}

PHP;

        $this->processor->process($originSource, $templateSource);
    }

    public function testClassnameEmptyOrigin(): void
    {
        $this->expectException(NotEqualClassnameException::class);

        $originSource = <<<'PHP'
<?php
namespace App;

PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class DifferentClassname {}

PHP;

        $this->processor->process($originSource, $templateSource);
    }

    public function testClassnameEmptyTemplate(): void
    {
        $this->expectException(NotEqualClassnameException::class);

        $originSource = <<<'PHP'
<?php
namespace App;

class OriginClassname {}

PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

PHP;

        $this->processor->process($originSource, $templateSource);
    }

    public function testUpdateUseStatementsNoUses(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname {}
PHP;

        $actualSource = $this->processor->process($originSource, $templateSource);
        $this->assertEquals($originSource, $actualSource);
    }

    public function testUpdateUseStatementsNoUsesInTemplate(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

use App\Exception;
class Classname
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname {}
PHP;

        $actualSource = $this->processor->process($originSource, $templateSource);
        $this->assertEquals($originSource, $actualSource);
    }

    public function testUpdateUseStatementsAppendUses(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

use App\Exception;

class Classname
{
}
PHP;

        $expectedSource = <<<'PHP'
<?php
namespace App;

use App\Exception;
class Classname
{
}
PHP;
        $actualSource = $this->processor->process($originSource, $templateSource);
        $this->assertEquals($expectedSource, $actualSource);
    }

    protected function setUp(): void
    {
        $logger = new NullLogger();
        $printer = new PrettyPrinter\Standard();
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->processor = new CombinePhpClassProcessor($logger, $parser, $printer);
    }
}
