<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator\Processor;

use Octava\CodeGenerator\Processor\PhpClassProcessor;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;

class PhpClassProcessorTest extends TestCase
{
    /**
     * @var PhpClassProcessor
     */
    protected $processor;

    /**
     * @skiped
     */
    public function testProcess()
    {

    }

    protected function setUp(): void
    {
        $printer = new PrettyPrinter\Standard();
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->processor = new PhpClassProcessor($parser, $printer);
    }
}
