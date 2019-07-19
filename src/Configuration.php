<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string[]
     */
    protected $templates;
    /**
     * @var array
     */
    protected $outputDir;
    /**
     * @var array
     */
    protected $templateVars;
    /**
     * @var string
     */
    protected $templatesRoot;
    /**
     * @var string
     */
    protected $namespace;

    /**
     * Configuration constructor.
     * @param string $outputDir
     * @param array $templates
     * @param array $templateVars
     * @param string $templatesRoot
     * @param string $namespace
     */
    public function __construct(
        string $outputDir,
        array $templates,
        array $templateVars = [],
        string $templatesRoot = '',
        string $namespace = ''
    ) {
        $this->outputDir = rtrim(trim($outputDir), DIRECTORY_SEPARATOR);
        foreach ($templates as $template) {
            $this->templates = rtrim(trim($template), DIRECTORY_SEPARATOR);
        }
        $this->templateVars = $templateVars;
        $this->templatesRoot = rtrim(trim($templatesRoot), DIRECTORY_SEPARATOR);
        $this->namespace = $namespace;
    }

    /**
     * @inheritDoc
     */
    public function getOutputDir(): string
    {
        return $this->outputDir;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    /**
     * @inheritDoc
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * @inheritDoc
     */
    public function getTemplatesRoot(): string
    {
        return $this->templatesRoot;
    }

    /**
     * @inheritDoc
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
