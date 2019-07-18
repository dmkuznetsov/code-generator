<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateClassStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateConstructorStatement;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateConstStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateExtendsStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateImplementsStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateMethodStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateNamespaceStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdatePropertyStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateTraitsStatements;
use Octava\CodeGenerator\Processor\PhpClassProcessor\UpdateUseStatements;
use Octava\CodeGenerator\ProcessorInterface;
use Octava\CodeGenerator\TemplateInterface;
use PhpParser\Parser;
use PhpParser\PrettyPrinterAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class PhpClassProcessor implements ProcessorInterface
{
    use GetClassTrait;

    private const EXT = 'php';

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
            $resultStmts = (new UpdateNamespaceStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateUseStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateClassStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateExtendsStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateImplementsStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateMethodStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateConstructorStatement($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdatePropertyStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateConstStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
            $resultStmts = (new UpdateTraitsStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
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

        return $ext === static::EXT;
    }
}
