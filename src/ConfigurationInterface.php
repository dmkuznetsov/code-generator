<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

interface ConfigurationInterface
{
    /**
     * @return string
     */
    public function getOutputDir(): string;

    /**
     * @return string
     */
    public function getTemplatesDir(): string;

    /**
     * @return array
     */
    public function getTemplateVars(): array;

    /**
     * @return string
     */
    public function getNamespace(): string;
}