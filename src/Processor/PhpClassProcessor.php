<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\GetClassTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateClassStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateExtendsStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateNamespaceStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateUseStatements;
use Octava\CodeGenerator\ProcessorInterface;
use Octava\CodeGenerator\TemplateInterface;
use PhpParser\PrettyPrinterAbstract;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;
use Psr\Log\NullLogger;

class PhpClassProcessor implements ProcessorInterface
{
    use GetClassTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Parser
     */
    protected $parser;
    /**
     * @var PrettyPrinterAbstract
     */
    protected $printer;

    public function __construct(Parser $parser, PrettyPrinterAbstract $printer, LoggerInterface $logger = null)
    {
        $this->parser = $parser;
        $this->printer = $printer;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritdoc
     */
    public function process(string $originSource, string $templateSource, array $templateVars = []): string
    {
        if (empty($originSource)) {
            return $templateSource;
        }

        $originStmts = $this->parser->parse($originSource);
        $resultStmts = $originStmts;
        $originClass = $this->getClassStatement($resultStmts);
        if ($originClass) {
            $templateStmts = $this->parser->parse($templateSource);
//        $resultStmts = $this->updateDefines($originStmts, $templateStmts);
            $resultStmts = (new UpdateNamespaceStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateUseStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateClassStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateExtendsStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
//        $resultStmts = $this->updateExtends($resultStmts, $templateStmts);
//        $resultStmts = $this->updateImplements($resultStmts, $templateStmts);
//        $resultStmts = $this->updateConstants($resultStmts, $templateStmts);
//        $resultStmts = $this->updateTraits($resultStmts, $templateStmts);
//        $resultStmts = $this->updateProperties($resultStmts, $templateStmts);
//        $resultStmts = $this->updateConstructor($resultStmts, $templateStmts);
//        $resultStmts = $this->updateMethods($resultStmts, $templateStmts);
        }

        return "<?php\n".$this->printer->prettyPrint($resultStmts);
    }

    /**
     * @param TemplateInterface $template
     * @return bool
     */
    public function canBeProcessed(TemplateInterface $template): bool
    {
        $ext = pathinfo($template->getOutputPath(), PATHINFO_EXTENSION);

        return $ext === 'php';
    }
}
