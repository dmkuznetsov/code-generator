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

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
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

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdatePropertyStatements(): void
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
    public $templatePublicProperty = 1;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public $originPublicProperty = 1;
    public $templatePublicProperty = 1;
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdatePropertyStatementsConflict(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $publicProperty = 1;
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $publicProperty = 2;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public $publicProperty = 1;
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdatePropertyStatementsDifferentVisibility(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $originPublicProperty = 'origin_public';
    protected $originProtectedProperty = 'origin_protected';
    private $originPrivateProperty = 'origin_private';
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $templatePublicProperty = 'template_public';
    protected $templateProtectedProperty = 'template_protected';
    private $templatePrivateProperty = 'template_private';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public $originPublicProperty = 'origin_public';
    protected $originProtectedProperty = 'origin_protected';
    private $originPrivateProperty = 'origin_private';
    public $templatePublicProperty = 'template_public';
    protected $templateProtectedProperty = 'template_protected';
    private $templatePrivateProperty = 'template_private';
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdatePropertyStatementsDifferentVisibilityOriginCombined(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty1 = 'origin_public2';
    protected $originProtectedProperty = null;
    private $originPrivateProperty1, $originPrivateProperty2 = 'origin_private2';
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $templatePublicProperty = 'template_public';
    protected $templateProtectedProperty = 'template_protected';
    private $templatePrivateProperty = 'template_private';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty1 = 'origin_public2';
    protected $originProtectedProperty = null;
    private $originPrivateProperty1, $originPrivateProperty2 = 'origin_private2';
    public $templatePublicProperty = 'template_public';
    protected $templateProtectedProperty = 'template_protected';
    private $templatePrivateProperty = 'template_private';
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdatePropertyStatementsDifferentVisibilityCombined(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty1 = 'origin_public2';
    protected $originProtectedProperty = null;
    private $originPrivateProperty1, $originPrivateProperty2 = 'origin_private2';
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public $templatePublicProperty1 = 'template_public1', $templatePublicProperty2 = 'template_public2';
    protected $templateProtectedProperty = 'template_protected';
    private $templatePrivateProperty1 = 'template_private', $templatePrivateProperty2 = 'template_private2';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty1 = 'origin_public2';
    protected $originProtectedProperty = null;
    private $originPrivateProperty1, $originPrivateProperty2 = 'origin_private2';
    public $templatePublicProperty1 = 'template_public1';
    public $templatePublicProperty2 = 'template_public2';
    protected $templateProtectedProperty = 'template_protected';
    private $templatePrivateProperty1 = 'template_private';
    private $templatePrivateProperty2 = 'template_private2';
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
        $this->processor = new UpdatePropertyStatements($logger, $this->parser);
    }
}
