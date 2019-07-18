<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateMethodStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateMethodStatementsTest extends TestCase
{
    /**
     * @var UpdateMethodStatements
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

    public function testUpdateMethodsStatementsWithoutTemplateMethods(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function getClassName() : string
    {
        return static::class;
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
    public function getClassName() : string
    {
        return static::class;
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateMethodStatementsWithoutOriginMethods(): void
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
    public function getClassName() : string
    {
        return static::class;
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function getClassName() : string
    {
        return static::class;
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateMethodStatements(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function getOriginClassName() : string
    {
        return static::class;
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function getTemplateClassName() : string
    {
        return static::class;
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function getOriginClassName() : string
    {
        return static::class;
    }
    public function getTemplateClassName() : string
    {
        return static::class;
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function _testUpdateMethodStatementsConflict(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function getClassName() : string
    {
        return 'Origin classname';
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function getClassName() : string
    {
        return 'Template classname';
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function getClassName() : string
    {
        return 'Origin classname';
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateMethodStatementsDifferentVisibility(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function originPublicMethod()
    {
        return 'originPublicMethod';
    }

    protected function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }

    private function originPrivateMethod()
    {
        return 'originPrivateMethod';
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }

    protected function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }

    private function templatePrivateMethod()
    {
        return 'templatePrivateMethod';
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public function originPublicMethod()
    {
        return 'originPublicMethod';
    }
    protected function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }
    private function originPrivateMethod()
    {
        return 'originPrivateMethod';
    }
    public function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }
    protected function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }
    private function templatePrivateMethod()
    {
        return 'templatePrivateMethod';
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateStaticMethodStatementsDifferentVisibility(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public static function originPublicMethod()
    {
        return 'originPublicMethod';
    }

    protected static function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }

    private static function originPrivateMethod()
    {
        return 'originPrivateMethod';
    }
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    public static function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }

    protected static function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }

    private static function templatePrivateMethod()
    {
        return 'templatePrivateMethod';
    }
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public static function originPublicMethod()
    {
        return 'originPublicMethod';
    }
    protected static function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }
    private static function originPrivateMethod()
    {
        return 'originPrivateMethod';
    }
    public static function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }
    protected static function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }
    private static function templatePrivateMethod()
    {
        return 'templatePrivateMethod';
    }
}
PHP;

        $actualSourceStmts = $this->processor->__invoke(
            $this->parser->parse($originSource),
            $this->parser->parse($templateSource)
        );
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateAbstractMethodStatementsDifferentVisibility(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    abstract public function originPublicMethod();
    abstract protected function originProtectedMethod();
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    abstract public function templatePublicMethod();
    abstract protected function templateProtectedMethod();
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    public abstract function originPublicMethod();
    protected abstract function originProtectedMethod();
    public abstract function templatePublicMethod();
    protected abstract function templateProtectedMethod();
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
        $this->processor = new UpdateMethodStatements($logger, $this->parser);
    }
}
