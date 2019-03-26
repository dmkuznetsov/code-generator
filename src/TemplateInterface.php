<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

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
     * @return array
     */
    public function getTemplateVars(): array;
}