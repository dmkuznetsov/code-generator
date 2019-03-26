<?php
declare(strict_types=1);

namespace Dm\Tests\CodeGenerator;

use Dm\CodeGenerator\CodeGenerator;
use Dm\CodeGenerator\CodeGeneratorFactory;
use Dm\CodeGenerator\Configuration;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CodeGeneratorFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $logger = new NullLogger();
        $templatesDir = __DIR__.DIRECTORY_SEPARATOR.'fixtures';
        $outputDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cg';
        $configuration = new Configuration($logger, $templatesDir, $outputDir, []);
        $codeGenerator = CodeGeneratorFactory::create($configuration);

        $this->assertInstanceOf(CodeGenerator::class, $codeGenerator);
    }
}