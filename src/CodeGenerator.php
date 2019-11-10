<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ProcessorException;

class CodeGenerator
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    public function __construct(
        ConfigurationInterface $configuration,
        FilesystemInterface $filesystem
    ) {
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;
    }

    /**
     * @param TemplateInterface ...$templates
     * @throws CodeGeneratorException
     */
    public function generate(TemplateInterface ...$templates): void
    {
        if (!count($templates)) {
            $this->configuration->getLogger()->info('[GENERATE] No templates given');

            return;
        }

        $errors = [];
        foreach ($templates as $template) {
            try {
                if (!$template instanceof TemplateInterface) {
                    $this->configuration->getLogger()->emergency(
                        sprintf(
                            '[GENERATE] Template must be instance of TemplateInterface, %s given',
                            get_class($template)
                        )
                    );
                    throw new CodeGeneratorException(
                        sprintf('Template must be instance of TemplateInterface, %s given', get_class($template))
                    );
                }

                if (!$this->filesystem->exists($template->getTemplatePath())) {
                    $this->configuration->getLogger()->emergency(
                        sprintf('[GENERATE] Template %s not found', $template->getTemplatePath())
                    );
                    throw new CodeGeneratorException(
                        sprintf('Template %s not found', $template->getTemplatePath())
                    );
                }
                $this->configuration->getLogger()->debug(
                    '[GENERATE] Start processing',
                    [
                        'template_path' => $template->getTemplatePath(),
                        'output_path' => $template->getOutputPath(),
                        'template_vars' => $template->getTemplateVars(),
                    ]
                );

                $this->process($template, $this->configuration->getProcessors());
            } catch (CodeGeneratorException $e) {
                $errors[] = $e->getMessage();
                $this->configuration->getLogger()->error(
                    sprintf('[GENERATE] Error processing: %s', $e->getMessage()),
                    [
                        'template_path' => $template->getTemplatePath(),
                        'output_path' => $template->getOutputPath(),
                        'template_vars' => $template->getTemplateVars(),
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
    public function process(TemplateInterface $template, array $processors): string
    {
        $originSource = '';
        if ($this->filesystem->exists($template->getOutputPath())) {
            $originSource = $this->filesystem->read($template->getOutputPath());
        }
        /** @var string $templateSource */
        $templateSource = $this->filesystem->read($template->getTemplatePath());
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
                $this->configuration->getLogger()->debug(
                    sprintf(
                        '[PROCESS] Origin file %s not found. PhpClassProcessor skipped',
                        $template->getOutputPath()
                    )
                );
            } else {
                $this->configuration->getLogger()->debug(
                    sprintf('[PROCESS] Applying %s to file '.$template->getTemplatePath(), get_class($template))
                );
                try {
                    $result = $processor->process($originSource, $result ?? $templateSource, $templateVars);
                } catch (ProcessorException $e) {
                    $this->configuration->getLogger()->debug(
                        '[PROCESS] Write result to '.$template->getOutputPath().'.generated'
                    );
                    $this->filesystem->dump($template->getOutputPath(), $result ?? $templateSource);
                    throw $e;
                }
            }
        }

        if (empty($result) && !count($processors)) {
            $result = $templateSource;
        }
        $this->configuration->getLogger()->debug('[PROCESS] Write result to '.$template->getOutputPath());
        $this->filesystem->dump($template->getOutputPath(), (string)$result);

        return $result;
    }
}
