<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

interface ProcessorInterface
{
    /**
     * @param string $source
     * @param array $templateVars
     * @return string
     */
    public function render(string $source, array $templateVars): string;
}