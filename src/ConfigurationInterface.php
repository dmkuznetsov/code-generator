<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

interface ConfigurationInterface
{
    /**
     * Directory for result files
     * @return string
     */
    public function getOutputDir(): string;

    /**
     * List of path to templates
     * @return string[]
     */
    public function getTemplates(): array;

    /**
     * List of template vars
     * @return array
     */
    public function getTemplateVars(): array;

    /**
     * Template root directory
     * @return string
     */
    public function getTemplatesRoot(): string;

    /**
     * Namespace for template root directory
     * @return string
     */
    public function getNamespace(): string;
}