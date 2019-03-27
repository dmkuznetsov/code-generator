<?php
declare(strict_types=1);

namespace Dm\CodeGenerator\Processor;

use Dm\CodeGenerator\Exception\ConflictClassnameException;
use Dm\CodeGenerator\Exception\NotEqualNamespaceException;
use Dm\CodeGenerator\Processor\PhpClassProcessor\GetClassTrait;
use Dm\CodeGenerator\Processor\PhpClassProcessor\UpdateClassStatements;
use Dm\CodeGenerator\Processor\PhpClassProcessor\UpdateExtendsStatements;
use Dm\CodeGenerator\Processor\PhpClassProcessor\UpdateNamespaceStatements;
use Dm\CodeGenerator\Processor\PhpClassProcessor\UpdateUseStatements;
use Dm\CodeGenerator\ProcessorInterface;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\PrettyPrinterAbstract;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

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

    public function __construct(LoggerInterface $logger, Parser $parser, PrettyPrinterAbstract $printer)
    {
        $this->logger = $logger;
        $this->parser = $parser;
        $this->printer = $printer;
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
        $templateStmts = $this->parser->parse($templateSource);

        $resultStmts = $originStmts;
//        $resultStmts = $this->updateDefines($originStmts, $templateStmts);
        $resultStmts = (new UpdateNamespaceStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
        $resultStmts = (new UpdateUseStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
        $originClass = $this->getClassStatement($resultStmts);
        if ($originClass) {
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
}
