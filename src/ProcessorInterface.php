<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ProcessorException;
use Psr\Log\LoggerInterface;

interface ProcessorInterface
{
    /**
     * @param string $originSource
     * @param string $templateSource
     * @param array $templateVars
     * @return string
     * @throws ProcessorException
     */
    public function process(string $originSource, string $templateSource, array $templateVars = []): string;

    /**
     * @param TemplateInterface $template
     * @return bool
     */
    public function canBeProcessed(TemplateInterface $template): bool;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);
}
