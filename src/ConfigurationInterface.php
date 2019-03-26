<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

use Psr\Log\LoggerInterface;

interface ConfigurationInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

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
}