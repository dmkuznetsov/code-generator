<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor;

use Octava\CodeGenerator\ProcessorInterface;
use Octava\CodeGenerator\TemplateInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class SimpleProcessor implements ProcessorInterface
{
    use LoggerAwareTrait;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * @inheritdoc
     */
    public function process(string $originSource, string $templateSource, array $templateVars = []): string
    {
        $search = array_keys($templateVars);
        $replace = array_values($templateVars);

        return str_replace($search, $replace, $templateSource);
    }

    /**
     * @param TemplateInterface $template
     * @return bool
     */
    public function canBeProcessed(TemplateInterface $template): bool
    {
        return true;
    }
}
