<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateImplementsStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateTraitsStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateTraitsStatementsTest extends TestCase
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

    public function testUpdateTraitsStatementsWithoutTemplateTraits(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    use OriginTrait, ExtraOriginTrait;
    use AdditionalOriginTrait;
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
    use OriginTrait, ExtraOriginTrait;
    use AdditionalOriginTrait;
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateTraitsStatementsWithoutOriginTraits(): void
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
    use TemplateTrait, ExtraTemplateTrait;
    use AdditionalTemplateTrait;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    use TemplateTrait, ExtraTemplateTrait;
    use AdditionalTemplateTrait;
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateTraitsStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    use OriginTrait, ExtraOriginTrait;
    use AdditionalOriginTrait, AdditionalTemplateTrait;
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    use TemplateTrait, ExtraTemplateTrait;
    use AdditionalTemplateTrait;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    use TemplateTrait, ExtraTemplateTrait;
    use OriginTrait, ExtraOriginTrait;
    use AdditionalOriginTrait, AdditionalTemplateTrait;
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateTraitsStatementsWithAlias(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    use OriginTrait, ExtraOriginTrait;
    use AdditionalOriginTrait, AdditionalTemplateTrait;
    use HelloWorld { sayHello as protected; }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    use TemplateTrait, ExtraTemplateTrait;
    use AdditionalTemplateTrait;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    use TemplateTrait, ExtraTemplateTrait;
    use OriginTrait, ExtraOriginTrait;
    use AdditionalOriginTrait, AdditionalTemplateTrait;
    use HelloWorld {
        sayHello as protected;
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
        $this->processor = new UpdateTraitsStatements($logger, $this->parser);
    }
}
