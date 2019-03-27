<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

use Dm\CodeGenerator\Exception\ProcessorException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CodeGenerator
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var TemplateInterface[]
     */
    protected $templates;
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $this->templateFactory = new TemplateFactory($configuration);
    }

    /**
     * Scan templates folder
     */
    public function scan(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->configuration->getTemplatesDir())
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $template = $this->templateFactory->create($file->getPathname());
                $this->addTemplate($template);
            }
        }
    }

    /**
     * @param TemplateInterface $template
     * @return $this
     */
    public function addTemplate(TemplateInterface $template): self
    {
        $this->templates[] = $template;

        return $this;
    }

    /**
     * @param ProcessorInterface[] $processors
     * @throws CodeGeneratorException
     */
    public function generate(array $processors): void
    {
        foreach ($processors as $processor) {
            if (!$processor instanceof ProcessorInterface) {
                throw new ProcessorException(
                    sprintf('Only ProcessorException is supported. %s given', get_class($processor))
                );
            }
            foreach ($this->templates as $template) {
                $content = $this->templateEngine->render($template);
                $this->save($template->getOutputDir(), $content);
            }
        }
    }

    /**
     * @param string $filepath
     * @param string $content
     * @throws CodeGeneratorException
     */
    protected function save(string $filepath, string $content): void
    {
        $dir = pathinfo($filepath, PATHINFO_DIRNAME);
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new CodeGeneratorException(sprintf('Directory "%s" was not created', $dir));
        }

        file_put_contents($filepath, $content);
    }
}
