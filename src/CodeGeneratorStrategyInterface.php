<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ProcessorException;

interface CodeGeneratorStrategyInterface
{
    /**
     * @param TemplateInterface $template
     * @return string
     * @throws ProcessorException
     */
    public function run(TemplateInterface $template): string;
}