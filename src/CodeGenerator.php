<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ConflictException;
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
     * @param CodeGeneratorStrategyInterface $strategy
     * @param TemplateInterface[] $templates
     * @throws CodeGeneratorException
     */
    public function generate(CodeGeneratorStrategyInterface $strategy, array $templates): void
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

                $result = $strategy->run($template);
                $this->logger->debug('[GENERATE] Write result to '.$template->getOutputPath());
                $this->writer->write($template->getOutputPath(), $result);
            } catch (ConflictException $e) {
                $this->logger->debug('[GENERATE] Write result to '.$template->getOutputPath().'.generated');
                $this->writer->write($e->getFilepath(), $e->getSource());
            } catch (CodeGeneratorException $e) {
                $errors[] = $e->getMessage();
                $this->logger->error(
                    sprintf('[GENERATE] Error processing: %s', $e->getMessage()),
                    [
                        'output' => $template->getOutputPath(),
                        'template' => $template->getOutputPath(),
                        'vars' => $template->getTemplateVars(),
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
}
