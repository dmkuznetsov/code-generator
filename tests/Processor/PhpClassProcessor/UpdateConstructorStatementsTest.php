<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateConstructorStatement;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateConstructorStatementsTest extends TestCase
{
    /**
     * @var UpdateConstructorStatement
     */
    protected $processor;
    /**
     * @var Parser
     */
    protected $parser;
    /**
     * @var PrettyPrinter\Standard
     */
    protected $printer;

    public function _testUpdateConstructorStatementWithoutTemplateConstructor(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct()
    {
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function __construct()
    {
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function _testUpdateConstructorStatementWithoutOriginConstructor(): void
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

class Classname
{
    public function __construct()
    {
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function __construct()
    {
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateConstructorArgumentStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($a)
    {
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($b)
    {
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function __construct($a, $b)
    {
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateConstructorArgumentsConflictStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($a = 1)
    {
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($a = 2)
    {
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function __construct($a = 1)
    {
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateConstructorBodyStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($a = 1)
    {
        $this->a = 1;
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($a = 2)
    {
        $this->a = 2;
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function __construct($a = 1)
    {
        $this->a = 1;
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateConstructorMergeBodyStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($a)
    {
        $this->a = $a;
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function __construct($b)
    {
        $this->b = $b;
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function __construct($a, $b)
    {
        $this->a = $a;
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }


    protected function setUp(): void
    {
        $logger = new NullLogger();
        $this->printer = new PrettyPrinter\Standard();
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->processor = new UpdateConstructorStatement($logger, $this->parser);
    }
}
