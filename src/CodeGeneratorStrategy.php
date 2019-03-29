<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ConflictException;
use Octava\CodeGenerator\Exception\ProcessorException;
use Octava\CodeGenerator\Processor\PhpClassProcessor;
use PhpParser\Parser;
use Octava\CodeGenerator\Processor\SimpleProcessor;
use PhpParser\PrettyPrinterAbstract;
use Psr\Log\LoggerInterface;

class CodeGeneratorStrategy implements CodeGeneratorStrategyInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var PhpClassProcessor
     */
    protected $phpClassProcessor;
    /**
     * @var SimpleProcessor
     */
    protected $simpleProcessor;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ConfigurationInterface $configuration,
        LoggerInterface $logger,
        Parser $parser,
        PrettyPrinterAbstract $printer
    ) {
        $this->configuration = $configuration;
        $this->simpleProcessor = new SimpleProcessor($logger);
        $this->phpClassProcessor = new PhpClassProcessor($logger, $parser, $printer);
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     * @param TemplateInterface $template
     * @return string
     * @throws ConflictException
     * @throws ProcessorException
     */
    public function run(TemplateInterface $template): string
    {
        $templateSource = file_get_contents($template->getTemplatePath());
        $templateVars = $template->getTemplateVars();

        $this->logger->debug(
            '[PROCESS] Applying SimpleProcessor to file '.$template->getTemplatePath()
        );
        $result = $this->simpleProcessor->process('', $templateSource, $templateVars);
        $ext = pathinfo($template->getOutputPath(), PATHINFO_EXTENSION);
        if ($ext === 'php' && file_exists($template->getOutputPath())) {
            $originSource = file_get_contents($template->getOutputPath());
            $this->logger->debug(
                sprintf(
                    '[PROCESS] Applying PhpClassProcessor between %s and %s',
                    $template->getTemplatePath(),
                    $template->getOutputPath()
                )
            );
            try {
                $result = $this->phpClassProcessor->process($originSource, $result, $templateVars);
            } catch (ProcessorException $e) {
                throw new ConflictException($template->getOutputPath().'.generated', $result, $e);
            }
        } else {
            $this->logger->debug(
                sprintf(
                    '[PROCESS] Origin file %s not found. PhpClassProcessor skipped',
                    $template->getOutputPath()
                )
            );
        }

        return $result;
    }
}