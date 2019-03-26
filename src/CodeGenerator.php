<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

use PhpParser\ParserFactory;
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
     * @var TemplateEngine
     */
    protected $templateEngine;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $this->templateEngine = new TemplateEngine(
            $configuration->getLogger(),
            $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7)
        );
    }

    public function scan(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->configuration->getTemplatesDir())
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $template = $this->createTemplate($file->getPathname());
                $this->addTemplate($template);
            }
        }
    }

    /**
     * @param string $templatePath Relative template's path
     * @param array $templateVars
     * @param string|null $outputDir
     * @param string|null $outputFilename
     * @return TemplateInterface
     */
    public function createTemplate(
        string $templatePath,
        array $templateVars = [],
        ?string $outputDir = null,
        ?string $outputFilename = null
    ): TemplateInterface {
        $pathInfo = pathinfo($templatePath);
        $dir = trim(
            $this->getSuffix($pathInfo['dirname'], $this->configuration->getTemplatesDir()),
            DIRECTORY_SEPARATOR
        );
        $filename = $pathInfo['filename'];
        $basename = $pathInfo['basename'];
        $extension = $pathInfo['extension'];
        if (null === $outputDir) {
            $outputDir = $this->configuration->getOutputDir().(empty($dir) ? '' : DIRECTORY_SEPARATOR.$dir);
        }
        if (null === $outputFilename) {
            $outputFilename = $basename;
        }
        $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', $dir);
        $data = array_replace(
            [
                '_CG_FILE_NAME_' => $filename,
                '_CG_FILE_BASENAME_' => $basename,
                '_CG_FILE_DIR_' => $dir,
                '_CG_FILE_PATH_' => $dir.DIRECTORY_SEPARATOR.$filename,
                '_CG_FILE_EXTENSION_' => $extension,
                '_CG_NAMESPACE_' => $namespace,
            ],
            $templateVars,
            $this->configuration->getTemplateVars()
        );

        return new Template(
            $this->configuration->getTemplatesDir().DIRECTORY_SEPARATOR.$this->getSuffix(
                $templatePath,
                $this->configuration->getTemplatesDir()
            ),
            $outputDir,
            $outputFilename,
            $data
        );
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return string
     */
    protected function getSuffix(string $haystack, string $needle): string
    {
        $result = $haystack;
        if (strpos($haystack, $needle) === 0) {
            if (strlen($needle) < strlen($haystack)) {
                $result = substr($haystack, strlen($needle) + 1);
            } else {
                $result = '';
            }
        }

        return $result;
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
     * @throws CodeGeneratorException
     */
    public function generate(): void
    {
        foreach ($this->templates as $template) {
            $content = $this->templateEngine->parse($template);
            $this->save($template->getOutputDir(), $content);
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