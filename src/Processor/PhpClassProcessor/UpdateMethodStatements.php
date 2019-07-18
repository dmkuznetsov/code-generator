<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassMethodTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\InsertStatementTrait;
use PhpParser\Node\Stmt;
use PhpParser\Parser;
use Psr\Log\LoggerInterface;

class UpdateMethodStatements
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

        $templateMethods = $this->getClassMethodStatements($templateClass);
        if (!$templateMethods) {
            return $originStmts;
        }

        $originMethods = $this->getClassMethodStatements($originClass);
        if (!$originMethods) {
            foreach (array_reverse($templateMethods) as $templateMethod) {
                array_unshift($originClass->stmts, $templateMethod);
            }

            return $originStmts;
        }

        $templateMethodsCollection = [];
        foreach ($templateMethods as $templateMethod) {
            $templateMethodsCollection[$templateMethod->name->toString()] = $templateMethod;
        }

        foreach ($originMethods as $originMethod) {
            foreach ($templateMethodsCollection as $templateMethodName => $templateMethod) {
                if ($originMethod->name->toString() === $templateMethodName) {
                    unset($templateMethodsCollection[$templateMethodName]);
                }
            }
        }

        foreach ($templateMethodsCollection as $method) {
            $this->insertStatementClose($originClass, $method);
        }

        return $originStmts;
    }
}
