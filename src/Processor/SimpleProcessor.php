<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor;

use Octava\CodeGenerator\ProcessorInterface;
use Octava\CodeGenerator\TemplateInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SimpleProcessor implements ProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
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
