<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Psr\Log\LoggerInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var string
     */
    protected $templatesDir;
    /**
     * @var string
     */
    protected $outputDir;
    /**
     * @var array
     */
    protected $templateVars;
    /**
     * @var WriterInterface
     */
    protected $writer;

    public function __construct(
        LoggerInterface $logger,
        WriterInterface $writer,
        string $templatesDir,
        string $outputDir,
        array $templateVars = []
    ) {
        $this->logger = $logger;
        $this->writer = $writer;
        $this->templatesDir = rtrim(trim($templatesDir), DIRECTORY_SEPARATOR);
        $this->outputDir = rtrim(trim($outputDir), DIRECTORY_SEPARATOR);
        $this->templateVars = $templateVars;
    }

    /**
     * @return string
     */
    public function getOutputDir(): string
    {
        return $this->outputDir;
    }

    /**
     * @return array
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }


    /**
     * @return WriterInterface
     */
    public function getWriter(): WriterInterface
    {
        return $this->writer;
    }

    /**
     * @return string
     */
    public function getTemplatesDir(): string
    {
        return $this->templatesDir;
    }
}
