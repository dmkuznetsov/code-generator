<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator;

use Octava\CodeGenerator\CodeGenerator;
use Octava\CodeGenerator\Configuration;
use Octava\CodeGenerator\ConfigurationInterface;
use Octava\CodeGenerator\Processor\PhpClassProcessor;
use Octava\CodeGenerator\Processor\SimpleProcessor;
use Octava\CodeGenerator\TemplateFactory;
use Octava\Tests\_data\TestWriter;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

class CodeGeneratorTest extends TestCase
{
    /**
     * @var CodeGenerator
     */
    protected $codeGenerator;
    /**
     * @var string
     */
    protected $templatesDir;
    /**
     * @var string
     */
    protected $outputDir;
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    public function testScan(): void
    {
        $actualTemplates = $this->codeGenerator->scan(new TemplateFactory($this->configuration));
        $actual = [];
        foreach ($actualTemplates as $template) {
            $actual[$template->getTemplatePath()] = $template->getOutputDir(
                ).DIRECTORY_SEPARATOR.$template->getOutputFilename();
        }
        ksort($actual);

        $expected = [];
        $expectedFiles = [
            'src/Application/_CG_MODULE_/Assembler/Assembler.php' => 'src/Application/MyFavourite/Assembler/Assembler.php',
            'src/Application/_CG_MODULE_/Assembler/AssemblerInterface.php' => 'src/Application/MyFavourite/Assembler/AssemblerInterface.php',
            'src/Application/_CG_MODULE_/Dto/Dto.php' => 'src/Application/MyFavourite/Dto/Dto.php',
            'src/Application/_CG_MODULE_/Service.php' => 'src/Application/MyFavourite/Service.php',
            'src/UI/_CG_MODULE_/Form/Form.php' => 'src/UI/MyFavourite/Form/Form.php',
            'src/UI/_CG_MODULE_/Model/RequestModel.php' => 'src/UI/MyFavourite/Model/RequestModel.php',
            'src/UI/_CG_MODULE_Controller.php' => 'src/UI/MyFavouriteController.php',
            'tests/api/v1/_CG_MODULE_Cest.php' => 'tests/api/v1/MyFavouriteCest.php',
        ];
        foreach ($expectedFiles as $templatePath => $outputPath) {
            $expected[$this->templatesDir.DIRECTORY_SEPARATOR.$templatePath] = $this->outputDir.DIRECTORY_SEPARATOR.$outputPath;
        }
        $this->assertSame($expected, $actual);
    }

    public function _testGenerate(): void
    {
//        $actualTemplates = $this->codeGenerator->scan(new TemplateFactory($this->configuration));
//        $this->codeGenerator->generate();
        $configuration = new Configuration(
            __DIR__.DIRECTORY_SEPARATOR.'_templates',
            sys_get_temp_dir().DIRECTORY_SEPARATOR.'cg'
        );
        $writer = new TestWriter();
        $generator = new CodeGenerator($configuration, $writer);
        $templates = $generator->scan(new TemplateFactory($this->configuration));
        $printer = new Standard();
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $processors = [
            new SimpleProcessor(),
            new PhpClassProcessor($parser, $printer)
        ];
        $generator->generate($templates, $processors);
    }

    protected function setUp(): void
    {
        $writer = new TestWriter();
        $this->templatesDir = __DIR__.DIRECTORY_SEPARATOR.'_templates';
        $this->outputDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cg';
        $this->configuration = new Configuration(
            $this->templatesDir,
            $this->outputDir,
            ['_CG_MODULE_' => 'MyFavourite']
        );
        $this->codeGenerator = new CodeGenerator($this->configuration, $writer);
    }

    protected function tearDown(): void
    {
//        if (!is_dir($this->outputDir)) {
//            return;
//        }
//
//        $files = new RecursiveIteratorIterator(
//            new RecursiveDirectoryIterator($this->outputDir, RecursiveDirectoryIterator::SKIP_DOTS),
//            RecursiveIteratorIterator::CHILD_FIRST
//        );
//
//        foreach ($files as $fileInfo) {
//            $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
//            $todo($fileInfo->getRealPath());
//        }
//
//        rmdir($this->outputDir);
    }
}
