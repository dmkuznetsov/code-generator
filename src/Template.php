<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

class Template implements TemplateInterface
{
    /**
     * @var string
     */
    protected $templatePath;
    /**
     * @var string
     */
    protected $outputDir;
    /**
     * @var string
     */
    protected $outputFilename;
    /**
     * @var array
     */
    protected $templateVars;

    public function __construct(
        string $templatePath,
        string $outputDir,
        string $outputFilename,
        array $templateVars
    ) {
        $this->templatePath = $templatePath;
        $this->outputDir = $outputDir;
        $this->outputFilename = $outputFilename;
        $this->templateVars = $templateVars;
    }

    /**
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * @return array
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    /**
     * @return string
     */
    public function getOutputPath(): string
    {
        return $this->getOutputDir().DIRECTORY_SEPARATOR.$this->getOutputFilename();
    }

    /**
     * @return string
     */
    public function getOutputDir(): string
    {
        return $this->outputDir;
    }

    /**
     * @return string
     */
    public function getOutputFilename(): string
    {
        return $this->outputFilename;
    }
}