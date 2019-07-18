<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdatePropertyStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdatePropertyCommentStatementsTest extends TestCase
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
    /**
     * Origin public property
     * @var int
     */
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
    /**
     * Origin public property
     * @var int
     */
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
    /**
     * Template public property
     * @var int
     */
    public $templatePublicProperty = 1;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    /**
     * Template public property
     * @var int
     */
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
    /**
     * Origin public property
     * @var int
     */
    public $originPublicProperty = 1;
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    /**
     * Template public property
     * @var int
     */
    public $templatePublicProperty = 1;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    /**
     * Origin public property
     * @var int
     */
    public $originPublicProperty = 1;
    /**
     * Template public property
     * @var int
     */
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
    /**
     * Public property 1
     * @var int
     */
    public $publicProperty = 1;
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    /**
     * Public property 2
     * @var int
     */
    public $publicProperty = 2;
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    /**
     * Public property 1
     * @var int
     */
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
    /**
     * Origin public property
     * @var Classname|null
     */
    public $originPublicProperty = null;
    /**
     * Origin protected property
     * @var string
     */
    protected $originProtectedProperty = 'origin_protected';
    /**
     * Origin private property
     * @var null|string
     */
    private $originPrivateProperty = 'origin_private';
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    /**
     * Template public property
     * @var Classname|null
     */
    public $templatePublicProperty = null;
    /**
     * Template protected property
     * @var string
     */
    protected $templateProtectedProperty = 'template_protected';
    /**
     * Template private property
     * @var string|null
     */
    private $templatePrivateProperty = 'template_private';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    /**
     * Origin public property
     * @var Classname|null
     */
    public $originPublicProperty = null;
    /**
     * Origin protected property
     * @var string
     */
    protected $originProtectedProperty = 'origin_protected';
    /**
     * Origin private property
     * @var null|string
     */
    private $originPrivateProperty = 'origin_private';
    /**
     * Template public property
     * @var Classname|null
     */
    public $templatePublicProperty = null;
    /**
     * Template protected property
     * @var string
     */
    protected $templateProtectedProperty = 'template_protected';
    /**
     * Template private property
     * @var string|null
     */
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
    /**
     * Origin public properties
     * @var string $originPublicProperty1
     * @var string $originPublicProperty2
     */
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty2 = 'origin_public2';
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
    /**
     * Origin public properties
     * @var string $originPublicProperty1
     * @var string $originPublicProperty2
     */
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty2 = 'origin_public2';
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
    /**
     * Origin public properties
     * @var string $originPublicProperty1
     * @var string $originPublicProperty2
     */
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty2 = 'origin_public2';
    protected $originProtectedProperty = null;
    private $originPrivateProperty1, $originPrivateProperty2 = 'origin_private2';
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    /**
     * Template public properties
     * @var string $templatePublicProperty1
     * @var string $templatePublicProperty2
     */
    public $templatePublicProperty1 = 'template_public1', $templatePublicProperty2 = 'template_public2';
    protected $templateProtectedProperty = 'template_protected';
    private $templatePrivateProperty1 = 'template_private', $templatePrivateProperty2 = 'template_private2';
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    /**
     * Origin public properties
     * @var string $originPublicProperty1
     * @var string $originPublicProperty2
     */
    public $originPublicProperty1 = 'origin_public1', $originPublicProperty2 = 'origin_public2';
    protected $originProtectedProperty = null;
    private $originPrivateProperty1, $originPrivateProperty2 = 'origin_private2';
    /**
     * Template public properties
     * @var string $templatePublicProperty1
     * @var string $templatePublicProperty2
     */
    public $templatePublicProperty1 = 'template_public1';
    /**
     * Template public properties
     * @var string $templatePublicProperty1
     * @var string $templatePublicProperty2
     */
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
