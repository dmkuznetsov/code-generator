<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CodeGenerator
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
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
                $this->configuration->getLogger()->debug(sprintf('[SCAN] Found %s', $file->getPathname()));
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
                    $this->configuration
                        ->getLogger()
                        ->emergency(
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
                    $this->configuration
                        ->getLogger()
                        ->emergency(sprintf('[GENERATE] Template %s not found', $template->getTemplatePath()));
                    throw new CodeGeneratorException(
                        sprintf('Template %s not found', $template->getTemplatePath())
                    );
                }
                $this->configuration
                    ->getLogger()
                    ->debug(
                        '[GENERATE] Start processing',
                        [
                            'output' => $template->getOutputPath(),
                            'template' => $template->getOutputPath(),
                            'vars' => $template->getTemplateVars(),
                        ]
                    );

                $strategy->run($template);
            } catch (CodeGeneratorException $e) {
                $errors[] = $e->getMessage();
                $this->configuration
                    ->getLogger()
                    ->error(
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
            throw new CodeGeneratorException(sprintf("Found %d errors:\n- %s", count($errors), implode("\n- ", $errors)));
        }
    }
}
