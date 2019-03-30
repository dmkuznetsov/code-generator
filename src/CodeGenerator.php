<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ConflictException;
use Octava\CodeGenerator\Exception\ProcessorException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CodeGenerator
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var WriterInterface
     */
    protected $writer;

    public function __construct(
        ConfigurationInterface $configuration,
        WriterInterface $writer,
        LoggerInterface $logger = null
    ) {
        $this->configuration = $configuration;
        $this->logger = $logger ?? new NullLogger();
        $this->writer = $writer;
    }

    /**
     * Scan templates folder
     * @param TemplateFactory $templateFactory
     * @return TemplateInterface[]
     */
    public function scan(TemplateFactory $templateFactory): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->configuration->getTemplatesDir())
        );

        $templates = [];
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $this->logger->debug(sprintf('[SCAN] Found %s', $file->getPathname()));
                $templates[] = $templateFactory->create($file->getPathname());
            }
        }

        return $templates;
    }

    /**
     * @param TemplateInterface[] $templates
     * @param ProcessorInterface[] $processors
     * @throws CodeGeneratorException
     */
    public function generate(array $templates, array $processors): void
    {
        $errors = [];
        foreach ($templates as $template) {
            try {
                if (!$template instanceof TemplateInterface) {
                    $this->logger->emergency(
                        sprintf(
                            '[GENERATE] Template must be instance of TemplateInterface, %s given',
                            get_class($template)
                        )
                    );
                    throw new CodeGeneratorException(
                        sprintf('Template must be instance of TemplateInterface, %s given', get_class($template))
                    );
                }

                if (!file_exists($template->getTemplatePath())) {
                    $this->logger->emergency(sprintf('[GENERATE] Template %s not found', $template->getTemplatePath()));
                    throw new CodeGeneratorException(
                        sprintf('Template %s not found', $template->getTemplatePath())
                    );
                }
                $this->logger->debug(
                    '[GENERATE] Start processing',
                    [
                        'output' => $template->getOutputPath(),
                        'template' => $template->getOutputPath(),
                        'vars' => $template->getTemplateVars(),
                    ]
                );

                $this->process($template, $processors);
            } catch (CodeGeneratorException $e) {
                $errors[] = $e->getMessage();
                $this->logger->error(
                    sprintf('[GENERATE] Error processing: %s', $e->getMessage()),
                    [
                        'output' => $template->getOutputPath(),
                        'template' => $template->getOutputPath(),
                        'variables' => $template->getTemplateVars(),
                    ]
                );
            }
        }

        if (count($errors)) {
            throw new CodeGeneratorException(
                sprintf("Found %d errors:\n- %s", count($errors), implode("\n- ", $errors))
            );
        }
    }

    /**
     * @param TemplateInterface $template
     * @param ProcessorInterface[] $processors
     * @return string
     * @throws CodeGeneratorException
     */
    protected function process(TemplateInterface $template, array $processors): string
    {
        $originSource = '';
        if (file_exists($template->getOutputPath())) {
            $originSource = file_get_contents($template->getOutputPath());
        }
        /** @var string $templateSource */
        $templateSource = file_get_contents($template->getTemplatePath());
        if (!$templateSource) {
            throw new CodeGeneratorException(sprintf('Template source "%s" not found', $template->getTemplatePath()));
        }
        $templateVars = $template->getTemplateVars();
        $result = null;
        foreach ($processors as $processor) {
            if (!$processor instanceof ProcessorInterface) {
                throw new CodeGeneratorException(
                    sprintf('Processor must be instance of ProcessorInterface, %s given', get_class($processor))
                );
            }

            if (!$processor->canBeProcessed($template)) {
                $this->logger->debug(
                    sprintf(
                        '[PROCESS] Origin file %s not found. PhpClassProcessor skipped',
                        $template->getOutputPath()
                    )
                );
            } else {
                $this->logger->debug(
                    sprintf('[PROCESS] Applying %s to file '.$template->getTemplatePath(), get_class($template))
                );
                try {
                    $result = $processor->process($originSource, (string)($result || $templateSource), $templateVars);
                } catch (ProcessorException $e) {
                    $this->logger->debug('[PROCESS] Write result to '.$template->getOutputPath().'.generated');
                    $this->writer->write($template->getOutputPath(), (string)($result || $templateSource));
                    throw $e;
                }
            }
        }

        $this->logger->debug('[PROCESS] Write result to '.$template->getOutputPath());
        $this->writer->write($template->getOutputPath(), (string)($result || $templateSource));
    }
}
