<?php
declare(strict_types=1);

namespace Dm\Tests\CodeGenerator;

use Dm\CodeGenerator\CodeGenerator;
use Dm\CodeGenerator\Configuration;
use Dm\CodeGenerator\TemplateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
     * @throws \ReflectionException
     */
    public function testScan(): void
    {
        $this->codeGenerator->scan();
        $reflection = new \ReflectionClass($this->codeGenerator);
        $property = $reflection->getProperty('templates');
        $property->setAccessible(true);
        /** @var TemplateInterface[] $actualTemplates */
        $actualTemplates = $property->getValue($this->codeGenerator);
        $property->setAccessible(false);

        $actual = [];
        foreach ($actualTemplates as $template) {
            $actual[$template->getTemplatePath()] = $template->getOutputDir() . DIRECTORY_SEPARATOR . $template->getOutputFilename();
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
            $expected[$this->templatesDir . DIRECTORY_SEPARATOR . $templatePath] = $this->outputDir . DIRECTORY_SEPARATOR . $outputPath;
        }
        $this->assertSame($expected, $actual);
    }

//    public function testGenerate()
//    {
//        $this->codeGenerator->scan();
//        $this->codeGenerator->generate();
//    }

    protected function setUp(): void
    {
        $logger = new NullLogger();
        $this->templatesDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        $this->outputDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cg';
        $configuration = new Configuration($logger, $this->templatesDir, $this->outputDir,
            ['_CG_MODULE_' => 'MyFavourite']);
        $this->codeGenerator = new CodeGenerator($configuration);
    }

    protected function tearDown()
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
