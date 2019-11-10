<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

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
     * @param string $outputPath
     * @param array $templateVars
     * @return TemplateInterface
     */
    public function create(
        string $templatePath,
        string $outputPath,
        array $templateVars = []
    ): TemplateInterface {
        $templatePath = ltrim(trim($templatePath), DIRECTORY_SEPARATOR);
        $outputPath = ltrim(trim($outputPath), DIRECTORY_SEPARATOR);
        $pathInfo = pathinfo($templatePath);
        $dir = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $basename = $pathInfo['basename'];
        $extension = $pathInfo['extension'];

        $data = array_replace(
            [
                '_CG_FILE_NAME_' => $filename,
                '_CG_FILE_NAME_UCFIRST_' => ucfirst($filename),
                '_CG_FILE_NAME_LCFIRST_' => lcfirst($filename),
                '_CG_FILE_BASENAME_' => $basename,
                '_CG_FILE_DIR_' => $dir,
                '_CG_FILE_PATH_' => $dir.DIRECTORY_SEPARATOR.$filename,
                '_CG_FILE_EXTENSION_' => $extension,
            ],
            $templateVars,
            $this->configuration->getTemplateVars()
        );

        $outputDir = str_replace(array_keys($data), array_values($data), $this->configuration->getBaseOutputDir());
        if (!empty($outputDir)) {
            $outputDir .= DIRECTORY_SEPARATOR;
        }
        $outputPath = $outputDir.str_replace(array_keys($data), array_values($data), $outputPath);

        return new Template($this->configuration->getBaseTemplatesDir().DIRECTORY_SEPARATOR.$templatePath, $outputPath, $data);
    }
}
