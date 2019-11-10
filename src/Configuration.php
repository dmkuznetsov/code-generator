<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Configuration implements ConfigurationInterface
{
    use LoggerAwareTrait;
    /**
     * @var array
     */
    protected $baseTemplateDir;
    /**
     * @var string
     */
    protected $baseOutputDir;
    /**
     * @var array
     */
    protected $templateVars;
    /**
     * @var ProcessorInterface[]
     */
    protected $processors;

    /**
     * Configuration constructor.
     * @param string $baseTemplateDir
     * @param string $baseOutputDir
     */
    public function __construct(string $baseTemplateDir, string $baseOutputDir = '')
    {
        $this->baseTemplateDir = rtrim(trim($baseTemplateDir), DIRECTORY_SEPARATOR);
        $this->baseOutputDir = rtrim(trim($baseOutputDir), DIRECTORY_SEPARATOR);
        $this->logger = new NullLogger();
        $this->templateVars = [];
        $this->processors = [];
    }

    /**
     * @inheritDoc
     */
    public function getBaseTemplatesDir(): string
    {
        return $this->baseTemplateDir;
    }

    /**
     * @inheritDoc
     */
    public function getBaseOutputDir(): string
    {
        return $this->baseOutputDir;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    /**
     * @param array $templateVars
     * @return $this
     */
    public function setTemplateVars(array $templateVars): self
    {
        $this->templateVars = $templateVars;

        return $this;
    }

    /**
     * @param ProcessorInterface $processor
     * @return Configuration
     */
    public function addProcessor(ProcessorInterface $processor): self
    {
        $processor->setLogger($this->getLogger());
        $this->processors[] = $processor;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return ProcessorInterface[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }
}
