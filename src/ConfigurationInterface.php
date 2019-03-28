<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Psr\Log\LoggerInterface;

interface ConfigurationInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * @return WriterInterface
     */
    public function getWriter(): WriterInterface;

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