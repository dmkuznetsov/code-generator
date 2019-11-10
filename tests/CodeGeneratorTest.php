<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator;

use Octava\CodeGenerator\CodeGenerator;
use Octava\CodeGenerator\CodeGeneratorException;
use Octava\CodeGenerator\Configuration;
use Octava\CodeGenerator\ConfigurationInterface;
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
                    ['_CG_MODULE_' => 'MyFavourite']
                )
            );
        $this->assertFileEquals(
            __DIR__.'/_resultTemplates/src/Application/MyFavourite/MyFavouriteService.php',
            __DIR__.'/_data/cg/src/Application/MyFavourite/MyFavouriteService.php'
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
        $this->codeGenerator = new CodeGenerator($this->configuration, new Filesystem());
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
}
