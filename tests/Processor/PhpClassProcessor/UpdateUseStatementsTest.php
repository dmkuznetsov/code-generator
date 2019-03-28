<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateUseStatements;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UpdateUseStatementsTest extends TestCase
{
    /**
     * @var UpdateUseStatements
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

    public function testUpdateUseStatementsNoUses(): void
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

class Classname {}
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

    public function testUpdateUseStatementsNoUsesInTemplate(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

use App\Exception;
class Classname
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

class Classname {}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

use App\Exception;
class Classname
{
}
PHP;

        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateUseStatementsAppendUses(): void
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

use App\Exception;

class Classname
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

use App\Exception;
class Classname
{
}
PHP;
        $actualSourceStmts = $this->processor->__invoke($this->parser->parse($originSource), $this->parser->parse($templateSource));
        $actualSource = $this->printer->prettyPrint($actualSourceStmts);
        $this->assertEquals($expectedSource, $actualSource);
    }

    public function testUpdateUseStatementsCombineUses(): void
    {
        $originSource = <<<'PHP'
<?php
namespace App;

use App\OriginException;
use App\Exception;
use App\RuntimeException;
class Classname
{
}
PHP;

        $templateSource = <<<'PHP'
<?php
namespace App;

use App\Exception;
use App\TemplateException;
use App\RuntimeException;
use App\DomainException;
class Classname
{
}
PHP;

        $expectedSource = <<<'PHP'
namespace App;

use App\DomainException;
use App\TemplateException;
use App\OriginException;
use App\Exception;
use App\RuntimeException;
class Classname
{
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
        $this->processor = new UpdateUseStatements($logger, $this->parser);
    }
}
