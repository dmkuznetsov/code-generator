<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Exception\ProcessorConflictClassExtendsException;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateExtendsStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateExtendsStatementsTest extends TestCase
{
    /**
     * @var UpdateExtendsStatements
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

    public function testUpdateExtendsStatementsWithoutTemplateClass(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname extends BaseClass
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname extends BaseClass
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateExtendsStatementsWithoutOriginClass(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname extends BaseClass
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateExtendsStatements(): void
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

class Classname extends BaseClass
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname extends BaseClass
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateExtendsStatementsConflict(): void
    {
        $this->expectException(ProcessorConflictClassExtendsException::class);

        $originSource = <<<'PHP'
<?php
namespace App;

class Classname extends OriginBaseClass
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname extends TemplateBaseClass
{
}
PHP;

        $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
    }


    protected function setUp(): void
    {
        $logger = new NullLogger();
        $this->printer = new PrettyPrinter\Standard();
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->processor = new UpdateExtendsStatements($logger, $this->parser);
    }
}
