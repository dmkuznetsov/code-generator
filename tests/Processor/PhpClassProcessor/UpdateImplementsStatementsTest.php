<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateImplementsStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateImplementsStatementsTest extends TestCase
{
    /**
     * @var UpdateImplementsStatements
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

    public function testUpdateImplementsStatementsWithoutImplements(): void
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

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateImplementsStatementsWithoutTemplateImplements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname implements OriginInterface
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

class Classname implements OriginInterface
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateImplementsStatementsWithoutOriginImplements(): void
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

class Classname implements TemplateImplements
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname implements TemplateImplements
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateMultipleImplementsStatements(): void
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

class Classname implements TemplateImplements, OriginImplements
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname implements TemplateImplements, OriginImplements
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }


    public function testUpdateImplementsStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname implements OriginInterface, BaseInterface
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname implements TemplateInterface, BaseInterface, ExtraTemplateInterface
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname implements OriginInterface, BaseInterface, TemplateInterface, ExtraTemplateInterface
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }


    protected function setUp(): void
    {
        $logger = new NullLogger();
        $this->printer = new PrettyPrinter\Standard();
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->processor = new UpdateImplementsStatements($logger, $this->parser);
    }
}
