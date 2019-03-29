<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $templatesDir;
    /**
     * @var string
     */
    protected $outputDir;
    /**
     * @var array
     */
    protected $templateVars;

    public function __construct(
        string $templatesDir,
        string $outputDir,
        array $templateVars = []
    ) {
        $this->templatesDir = rtrim(trim($templatesDir), DIRECTORY_SEPARATOR);
        $this->outputDir = rtrim(trim($outputDir), DIRECTORY_SEPARATOR);
        $this->templateVars = $templateVars;
    }

    /**
     * @return string
     */
    public function getOutputDir(): string
    {
        return $this->outputDir;
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
    public function getTemplatesDir(): string
    {
        return $this->templatesDir;
    }
}
