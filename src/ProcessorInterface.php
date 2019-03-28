<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ProcessorException;

interface ProcessorInterface
{
    /**
     * @param string $originSource
     * @param string $templateSource
     * @param array $templateVars
     * @throws ProcessorException
     * @return string
     */
    public function process(string $originSource, string $templateSource, array $templateVars = []): string;
}
