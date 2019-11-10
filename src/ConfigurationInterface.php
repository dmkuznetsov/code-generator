<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Psr\Log\LoggerInterface;

interface ConfigurationInterface
{
    /**
     * Base templates directory
     * @return string
     */
    public function getBaseTemplatesDir(): string;

    /**
     * Base output directory
     * @return string
     */
    public function getBaseOutputDir(): string;

    /**
     * List of template vars
     * @return array
     */
    public function getTemplateVars(): array;

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * @return ProcessorInterface[]
     */
    public function getProcessors(): array;
}