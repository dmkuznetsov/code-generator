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
    protected $outputPath;
    /**
     * @var array
     */
    protected $templateVars;

    /**
     * Template constructor.
     * @param string $templatePath
     * @param string $outputPath
     * @param array $templateVars
     */
    public function __construct(
        string $templatePath,
        string $outputPath,
        array $templateVars
    ) {
        $this->templatePath = $templatePath;
        $this->outputPath = $outputPath;
        $this->templateVars = $templateVars;
    }

    /**
     * @inheritDoc
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * @inheritDoc
     */
    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }
}