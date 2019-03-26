<?php
declare(strict_types=1);

namespace Dm\Tests\CodeGenerator;

use Dm\CodeGenerator\CodeGenerator;
use Dm\CodeGenerator\Configuration;
use Dm\CodeGenerator\TemplateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

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

        $expected = [
            $this->templatesDir.DIRECTORY_SEPARATOR.'index.php' => $this->outputDir.DIRECTORY_SEPARATOR.'index.php',
            $this->templatesDir.DIRECTORY_SEPARATOR.'sub/sub.php' => $this->outputDir.DIRECTORY_SEPARATOR.'sub/sub.php',
        ];
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
        $this->templatesDir = __DIR__.DIRECTORY_SEPARATOR.'templates';
        $this->outputDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cg';
        $configuration = new Configuration($logger, $this->templatesDir, $this->outputDir, []);
        $this->codeGenerator = new CodeGenerator($configuration);
    }
}