<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ProcessorException;
use Octava\CodeGenerator\Exception\WriterException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CodeGenerator
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

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
                $templates[] = $templateFactory->create($file->getPathname());
            }
        }

        return $templates;
    }

    /**
     * @param ProcessorInterface $processor
     * @param TemplateInterface[] $templates
     * @throws CodeGeneratorException
     * @throws ProcessorException
     * @throws WriterException
     */
    public function generate(ProcessorInterface $processor, array $templates): void
    {
        foreach ($templates as $template) {
            if (!$template instanceof TemplateInterface) {
                throw new CodeGeneratorException(
                    sprintf('Template must be instance of TemplateInterface, %s given', get_class($template))
                );
            }

            if (!file_exists($template->getOutputPath())) {
                $originSource = '';
            } else {
                $originSource = file_get_contents($template->getOutputPath());
            }
            $templateSource = file_get_contents($template->getTemplatePath());
            $templateVars = $template->getTemplateVars();

            $result = $processor->process($originSource, $templateSource, $templateVars);
            $this->configuration
                ->getWriter()
                ->write(
                    $template->getOutputDir().DIRECTORY_SEPARATOR.$template->getOutputFilename(),
                    $result
                );
        }
    }
}
