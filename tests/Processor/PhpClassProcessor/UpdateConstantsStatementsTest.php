<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateConstStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateConstantsStatementsTest extends TestCase
{
    /**
     * @var UpdateConstStatements
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

    public function testUpdateConstStatementsWithoutTemplateConst(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    private const ORIGIN_PRIVATE = 'origin_private';
    protected const ORIGIN_PROTECTED = 'origin_protected';
    public const ORIGIN_PUBLIC = 'origin_public';
    const ORIGIN_CONST = 'origin';
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
    private const ORIGIN_PRIVATE = 'origin_private';
    protected const ORIGIN_PROTECTED = 'origin_protected';
    public const ORIGIN_PUBLIC = 'origin_public';
    const ORIGIN_CONST = 'origin';
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateConstStatementsWithoutOriginConst(): void
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
    private const TEMPLATE_PRIVATE = 'template_private';
    protected const TEMPLATE_PROTECTED = 'template_protected';
    public const TEMPLATE_PUBLIC = 'template_public';
    const TEMPLATE_CONST = 'template';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    private const TEMPLATE_PRIVATE = 'template_private';
    protected const TEMPLATE_PROTECTED = 'template_protected';
    public const TEMPLATE_PUBLIC = 'template_public';
    const TEMPLATE_CONST = 'template';
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateConstStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    private const ORIGIN_PRIVATE = 'origin_private';
    protected const ORIGIN_PROTECTED = 'origin_protected';
    public const ORIGIN_PUBLIC = 'origin_public';
    const ORIGIN_CONST = 'origin';
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    private const TEMPLATE_PRIVATE = 'template_private';
    protected const TEMPLATE_PROTECTED = 'template_protected';
    public const TEMPLATE_PUBLIC = 'template_public';
    const TEMPLATE_CONST = 'template';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    const TEMPLATE_CONST = 'template';
    public const TEMPLATE_PUBLIC = 'template_public';
    protected const TEMPLATE_PROTECTED = 'template_protected';
    private const TEMPLATE_PRIVATE = 'template_private';
    private const ORIGIN_PRIVATE = 'origin_private';
    protected const ORIGIN_PROTECTED = 'origin_protected';
    public const ORIGIN_PUBLIC = 'origin_public';
    const ORIGIN_CONST = 'origin';
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateConstStatementsWithComments(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    /**
     * ORIGIN_PRIVATE
     */
    private const ORIGIN_PRIVATE = 'origin_private';
    /**
     * ORIGIN_PROTECTED
     */
    protected const ORIGIN_PROTECTED = 'origin_protected';
    /**
     * ORIGIN_PUBLIC
     */
    public const ORIGIN_PUBLIC = 'origin_public';
    /**
     * ORIGIN_CONST
     */
    const ORIGIN_CONST = 'origin';
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    private const TEMPLATE_PRIVATE = 'template_private';
    protected const TEMPLATE_PROTECTED = 'template_protected';
    public const TEMPLATE_PUBLIC = 'template_public';
    const TEMPLATE_CONST = 'template';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    const TEMPLATE_CONST = 'template';
    public const TEMPLATE_PUBLIC = 'template_public';
    protected const TEMPLATE_PROTECTED = 'template_protected';
    private const TEMPLATE_PRIVATE = 'template_private';
    /**
     * ORIGIN_PRIVATE
     */
    private const ORIGIN_PRIVATE = 'origin_private';
    /**
     * ORIGIN_PROTECTED
     */
    protected const ORIGIN_PROTECTED = 'origin_protected';
    /**
     * ORIGIN_PUBLIC
     */
    public const ORIGIN_PUBLIC = 'origin_public';
    /**
     * ORIGIN_CONST
     */
    const ORIGIN_CONST = 'origin';
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
        $this->processor = new UpdateConstStatements($logger, $this->parser);
    }
}
