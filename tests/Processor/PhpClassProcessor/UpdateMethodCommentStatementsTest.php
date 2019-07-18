<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateMethodStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateMethodCommentStatementsTest extends TestCase
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
    /**
     * Return current class name
     * @return string
     */
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
    /**
     * Return current class name
     * @return string
     */
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
    /**
     * Return current class name
     * @return string
     */
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
    /**
     * Return current class name
     * @return string
     */
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
    /**
     * Return origin class name
     * @return string
     */
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
    /**
     * Return template class name
     * @return string
     */
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
    /**
     * Return origin class name
     * @return string
     */
    public function getOriginClassName() : string
    {
        return static::class;
    }
    /**
     * Return template class name
     * @return string
     */
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
    /**
     * Return origin class name
     * @return string
     */
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
    /**
     * Return template class name
     * @return string
     */
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
    /**
     * Return origin class name
     * @return string
     */
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
    /**
     * @return string
     */
    public function originPublicMethod()
    {
        return 'originPublicMethod';
    }

    /**
     * @return string
     */
    protected function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }

    /**
     * @return string
     */
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
    /**
     * @return string
     */
    public function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }

    /**
     * @return string
     */
    protected function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }

    /**
     * @return string
     */
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
    /**
     * @return string
     */
    public function originPublicMethod()
    {
        return 'originPublicMethod';
    }
    /**
     * @return string
     */
    protected function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }
    /**
     * @return string
     */
    private function originPrivateMethod()
    {
        return 'originPrivateMethod';
    }
    /**
     * @return string
     */
    public function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }
    /**
     * @return string
     */
    protected function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }
    /**
     * @return string
     */
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
    /**
     * @return string
     */
    public static function originPublicMethod()
    {
        return 'originPublicMethod';
    }

    /**
     * @return string
     */
    protected static function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }

    /**
     * @return string
     */
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
    /**
     * @return string
     */
    public static function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }

    /**
     * @return string
     */
    protected static function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }

    /**
     * @return string
     */
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
    /**
     * @return string
     */
    public static function originPublicMethod()
    {
        return 'originPublicMethod';
    }
    /**
     * @return string
     */
    protected static function originProtectedMethod()
    {
        return 'originProtectedMethod';
    }
    /**
     * @return string
     */
    private static function originPrivateMethod()
    {
        return 'originPrivateMethod';
    }
    /**
     * @return string
     */
    public static function templatePublicMethod()
    {
        return 'templatePublicMethod';
    }
    /**
     * @return string
     */
    protected static function templateProtectedMethod()
    {
        return 'templateProtectedMethod';
    }
    /**
     * @return string
     */
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
    /**
     * @return string
     */
    abstract public function originPublicMethod();
    /**
     * @return string
     */
    abstract protected function originProtectedMethod();
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname
{
    /**
     * @return string
     */
    abstract public function templatePublicMethod();
    /**
     * @return string
     */
    abstract protected function templateProtectedMethod();
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

class Classname
{
    /**
     * @return string
     */
    public abstract function originPublicMethod();
    /**
     * @return string
     */
    protected abstract function originProtectedMethod();
    /**
     * @return string
     */
    public abstract function templatePublicMethod();
    /**
     * @return string
     */
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
