<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\ProcessorException;
use Octava\CodeGenerator\Processor\PhpClassProcessor;
use PhpParser\Parser;
use Octava\CodeGenerator\Processor\SimpleProcessor;
use PhpParser\PrettyPrinterAbstract;

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

    public function __construct(
        ConfigurationInterface $configuration,
        Parser $parser,
        PrettyPrinterAbstract $printer
    ) {
        $this->configuration = $configuration;
        $this->simpleProcessor = new SimpleProcessor($this->configuration->getLogger());
        $this->phpClassProcessor = new PhpClassProcessor($this->configuration->getLogger(), $parser, $printer);
    }

    /**
     * @inheritdoc
     */
    public function run(TemplateInterface $template): string
    {
        $templateSource = file_get_contents($template->getTemplatePath());
        $templateVars = $template->getTemplateVars();

        $this->configuration->getLogger()->debug(
            '[PROCESS] Applying SimpleProcessor to file '.$template->getTemplatePath()
        );
        $result = $this->simpleProcessor->process('', $templateSource, $templateVars);
        $ext = pathinfo($template->getOutputPath(), PATHINFO_EXTENSION);
        if ($ext === 'php' && file_exists($template->getOutputPath())) {
            $originSource = file_get_contents($template->getOutputPath());
            $this->configuration->getLogger()->debug(
                sprintf(
                    '[PROCESS] Applying PhpClassProcessor between %s and %s',
                    $template->getTemplatePath(),
                    $template->getOutputPath()
                )
            );
            try {
                $result = $this->phpClassProcessor->process($originSource, $result, $templateVars);
                $this->configuration->getLogger()->debug('[PROCESS] Write result to '.$template->getOutputPath());
                $this->configuration->getWriter()->write($template->getOutputPath(), $result);
            } catch (ProcessorException $e) {
                $this->configuration->getLogger()->debug('[PROCESS] Write result to '.$template->getOutputPath().'.generated');
                $this->configuration->getWriter()->write($template->getOutputPath().'.generated', $result);
                throw $e;
            }
        } else {
            $this->configuration->getLogger()->debug(
                sprintf(
                    '[PROCESS] Origin file %s not found. PhpClassProcessor skipped',
                    $template->getOutputPath()
                )
            );
            $this->configuration->getLogger()->debug('[PROCESS] Write result to '.$template->getOutputPath());
            $this->configuration->getWriter()->write($template->getOutputPath(), $result);
        }

        return $result;
    }
}