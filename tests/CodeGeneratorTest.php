<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator;

use Octava\CodeGenerator\CodeGenerator;
use Octava\CodeGenerator\CodeGeneratorException;
use Octava\CodeGenerator\Configuration;
use Octava\CodeGenerator\ConfigurationInterface;
use Octava\CodeGenerator\Exception\ProcessorConflictClassnameException;
use Octava\CodeGenerator\Filesystem;
use Octava\CodeGenerator\Processor\PhpClassProcessor;
use Octava\CodeGenerator\Processor\SimpleProcessor;
use Octava\CodeGenerator\TemplateFactory;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CodeGeneratorTest extends TestCase
{
    /**
     * @var string
     */
    private $templatesBasePath;
    /**
     * @var string
     */
    private $outputDir;
    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @throws CodeGeneratorException
     */
    public function testSingleGeneration(): void
    {
        $this->codeGenerator
            ->generate(
                $this->templateFactory->create(
                    'src/Application/_CG_MODULE_/_CG_MODULE_Service.php',
                    'src/Application/_CG_MODULE_/_CG_MODULE_Service.php',
                    [
                        '_CG_MODULE_' => 'MyFavourite',
                        '_CG_SERVICE_NAME__LCFIRST_' => 'myFavourite',
                        '_CG_SERVICE_NAME_' => 'MyFavourite',
                    ]
                )
            );
        $this->assertFileEquals(
            __DIR__.'/_sampleTemplates/src/Application/MyFavourite/MyFavouriteService1.php',
            __DIR__.'/_data/cg/src/Application/MyFavourite/MyFavouriteService.php'
        );
    }

    /**
     * @throws CodeGeneratorException
     */
    public function testSingle2Generation(): void
    {
        $this->codeGenerator
            ->generate(
                $this->templateFactory->create(
                    'src/Application/_CG_MODULE_/MyFavouriteService.php',
                    'src/Application/_CG_MODULE_/MyFavouriteService.php',
                    [
                        '_CG_MODULE_' => 'MyFavourite',
                    ]
                )
            );
        $this->assertFileEquals(
            __DIR__.'/_sampleTemplates/src/Application/MyFavourite/MyFavouriteService2.php',
            __DIR__.'/_data/cg/src/Application/MyFavourite/MyFavouriteService.php'
        );
    }

    /**
     * @throws CodeGeneratorException
     */
    public function testComplexGeneration(): void
    {
        $this->codeGenerator
            ->generate(
                $this->templateFactory->create(
                    'src/Application/_CG_MODULE_/_CG_MODULE_Service.php',
                    'src/Application/_CG_MODULE_/_CG_MODULE_Service1.php',
                    [
                        '_CG_MODULE_' => 'MyFavourite',
                        '_CG_SERVICE_NAME__LCFIRST_' => 'myFavourite',
                        '_CG_SERVICE_NAME_' => 'MyFavourite',
                    ]
                )
            );
        $this->codeGenerator
            ->generate(
                $this->templateFactory->create(
                    'src/Application/_CG_MODULE_/MyFavouriteService.php',
                    'src/Application/_CG_MODULE_/MyFavouriteService2.php',
                    [
                        '_CG_MODULE_' => 'MyFavourite',
                    ]
                )
            );

        $configuration = new Configuration(__DIR__.'/_data/cg', __DIR__.'/_data/cg');
        $codeGenerator = $this->createCodeGenerator($configuration);
        $templateFactory = new TemplateFactory($configuration);
        $codeGenerator
            ->generate(
                $templateFactory->create(
                    'src/Application/MyFavourite/MyFavouriteService2.php',
                    'src/Application/MyFavourite/MyFavouriteService1.php'
                )
            );

        $this->assertFileEquals(
            __DIR__.'/_sampleTemplates/src/Application/MyFavourite/MyFavouriteServiceCombined.php',
            __DIR__.'/_data/cg/src/Application/MyFavourite/MyFavouriteService1.php'
        );
    }

    /**
     * @throws CodeGeneratorException
     */
    public function testExceptionComplexGeneration(): void
    {
        $this->expectException(CodeGeneratorException::class);
        $this->expectExceptionMessage(sprintf("Found 1 errors:\n- Origin classname \"OtherIceCreamService\" not equal to \"IceCreamService\""));
        $this->codeGenerator
            ->generate(
                $this->templateFactory->create(
                    'src/Application/IceCream/IceCreamService.php',
                    'src/Application/IceCream/IceCreamService.php'
                )
            );
        $this->codeGenerator
            ->generate(
                $this->templateFactory->create(
                    'src/Application/IceCream/OtherIceCreamService.php',
                    'src/Application/IceCream/OtherIceCreamService.php'
                )
            );

        $configuration = new Configuration(__DIR__.'/_data/cg', __DIR__.'/_data/cg');
        $codeGenerator = $this->createCodeGenerator($configuration);
        $templateFactory = new TemplateFactory($configuration);
        $codeGenerator
            ->generate(
                $templateFactory->create(
                    'src/Application/IceCream/OtherIceCreamService.php',
                    'src/Application/IceCream/IceCreamService.php'
                )
            );
    }

    protected function setUp(): void
    {
        $this->templatesBasePath = __DIR__.'/_templates';
        $this->outputDir = __DIR__.'/_data/cg';
        $printer = new PrettyPrinter\Standard();
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->configuration = new Configuration($this->templatesBasePath, $this->outputDir);
        $this->configuration
            ->setTemplateVars([])
            ->addProcessor(new SimpleProcessor())
            ->addProcessor(new PhpClassProcessor($parser, $printer))
        ;
        $this->templateFactory = new TemplateFactory($this->configuration);
        $this->codeGenerator = $this->createCodeGenerator($this->configuration);
    }

    protected function tearDown(): void
    {
        if (!is_dir($this->outputDir)) {
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->outputDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileInfo) {
            $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileInfo->getRealPath());
        }

        rmdir($this->outputDir);
    }

    private function createCodeGenerator(Configuration $configuration): CodeGenerator
    {
        $printer = new PrettyPrinter\Standard();
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $configuration
            ->addProcessor(new SimpleProcessor())
            ->addProcessor(new PhpClassProcessor($parser, $printer))
        ;
        return new CodeGenerator($this->configuration, new Filesystem());
    }
}
