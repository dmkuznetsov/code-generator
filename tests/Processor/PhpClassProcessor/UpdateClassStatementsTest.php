<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Exception\ConflictClassExtendsException;
use Octava\CodeGenerator\Exception\ConflictClassnameException;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateClassStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateExtendsStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateClassStatementsTest extends TestCase
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

    public function testUpdateClassStatementsWithoutTemplateClass(): void
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

PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateClassStatementsWithoutOriginClass(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

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
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateClassStatementsSameClassname(): void
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
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateClassStatementsConflict(): void
    {
        $this->expectException(ConflictClassnameException::class);

        $originSource = <<<'PHP'
<?php
namespace App;

class OriginClassname
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class TemplateClassname
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
        $this->processor = new UpdateClassStatements($logger, $this->parser);
    }
}
