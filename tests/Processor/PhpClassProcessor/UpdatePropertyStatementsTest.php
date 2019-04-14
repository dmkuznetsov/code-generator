<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdatePropertyStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdatePropertyStatementsTest extends TestCase
{
    /**
     * @var UpdatePropertyStatements
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

    public function testUpdatePropertyStatementsWithoutTemplateProperties(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $originPublicProperty = 1;
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
    public $originPublicProperty = 1;
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdatePropertyStatementsWithoutOriginProperties(): void
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
    public $templatePublicProperty = 1;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public $templatePublicProperty = 1;
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
        $this->processor = new UpdatePropertyStatements($logger, $this->parser);
    }
}
