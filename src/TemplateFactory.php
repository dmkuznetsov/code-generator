<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

class TemplateFactory
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $templatePath Relative template's path
     * @param array $templateVars
     * @param string|null $outputDir
     * @param string|null $outputFilename
     * @return TemplateInterface
     */
    public function create(
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
}