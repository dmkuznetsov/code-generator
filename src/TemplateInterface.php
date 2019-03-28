<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

interface TemplateInterface
{
    /**
     * @return string
     */
    public function getTemplatePath(): string;

    /**
     * @return string
     */
    public function getOutputDir(): string;

    /**
     * @return string
     */
    public function getOutputFilename(): string;

    /**
     * @return string
     */
    public function getOutputPath(): string;

    /**
     * @return array
     */
    public function getTemplateVars(): array;
}