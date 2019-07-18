<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassMethodTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\InsertStatementTrait;
use PhpParser\Node\Stmt;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class UpdateConstructorStatement
{
    use GetClassMethodTrait, GetClassTrait, InsertStatementTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(LoggerInterface $logger, Parser $parser)
    {
        $this->logger = $logger;
        $this->parser = $parser;
    }

    /**
     * @param Stmt[] $originStmts
     * @param Stmt[] $templateStmts
     * @return Stmt[]
     */
    public function __invoke(array $originStmts, array $templateStmts): array
    {
        $originClass = $this->getClassStatement($originStmts);
        $templateClass = $this->getClassStatement($templateStmts);

        if (!$originClass || !$templateClass) {
            return $originStmts;
        }

        $templateConstructor = $this->getClassConstructorStatement($templateClass);
        if (!$templateConstructor) {
            return $originStmts;
        }

        $originConstructor = $this->getClassConstructorStatement($originClass);
        if (!$originConstructor) {
            $this->insertStatementClose($originClass, $templateConstructor);

            return $originStmts;
        }

        $templateArgumentsCollection = [];
        foreach ($templateConstructor->params as $templateArgument) {
            $templateArgumentsCollection[(string)$templateArgument->var->name] = $templateArgument;
        }

        foreach ($originConstructor->params as $originArgument) {
            foreach ($templateArgumentsCollection as $templateArgumentName => $templateArgument) {
                if ((string)$originArgument->var->name === $templateArgumentName) {
                    unset($templateArgumentsCollection[$templateArgumentName]);
                }
            }
        }

        foreach ($templateArgumentsCollection as $argument) {
            $originConstructor->params[] = $argument;
        }

        return $originStmts;
    }
}
