<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

use Dm\CodeGenerator\Processor\CombinePhpClassProcessor;
use Dm\CodeGenerator\Processor\SimpleProcessor;
use Psr\Log\LoggerInterface;

class TemplateEngine
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var SimpleProcessor
     */
    protected $simpleEngine;
    /**
     * @var CombinePhpClassProcessor
     */
    protected $phpEngine;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->simpleEngine = new SimpleProcessor($logger);
        $this->phpEngine = new CombinePhpClassProcessor($logger);
    }

    /**
     * @param TemplateInterface $template
     * @return string
     */
    public function render(TemplateInterface $template): string
    {
        $source = file_get_contents($template->getTemplatePath());
        $templateVars = $template->getTemplateVars();
        $result = $this->simpleEngine->render($source, $templateVars);
        if (file_exists($template->getOutputDir() . DIRECTORY_SEPARATOR . $template->getOutputFilename())) {
            $result = $this->phpEngine->render($result, $templateVars);
        }

        return $result;
    }
}
