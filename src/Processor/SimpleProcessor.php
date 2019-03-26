<?php
declare(strict_types=1);

namespace Dm\CodeGenerator\Processor;

use Dm\CodeGenerator\ProcessorInterface;
use Psr\Log\LoggerInterface;

class SimpleProcessor implements ProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $source
     * @param array $templateVars
     * @return string
     */
    public function render(string $source, array $templateVars): string
    {
        $search = array_keys($templateVars);
        $replace = array_values($templateVars);
        return str_replace($search, $replace, $source);
    }
}
