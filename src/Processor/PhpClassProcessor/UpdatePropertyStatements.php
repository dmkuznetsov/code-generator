<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassPropertyTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\InsertStatementTrait;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class UpdatePropertyStatements
{
    use GetClassPropertyTrait, GetClassTrait, InsertStatementTrait;

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

        $templateProperties = $this->getPropertyStatements($templateClass);
        if (!$templateProperties) {
            return $originStmts;
        }

        $originProperties = $this->getPropertyStatements($originClass);
        if (!$originProperties) {
            foreach (array_reverse($templateProperties) as $templateProperty) {
                array_unshift($originClass->stmts, $templateProperty);
            }

            return $originStmts;
        }

        $templatePropertiesCollection = [];
        foreach ($templateProperties as $templateProperty) {
            $templatePropertiesCollection[$templateProperty->props[0]->name->toString()] = $templateProperty;
        }

        foreach ($originProperties as $originProperty) {
            foreach ($templatePropertiesCollection as $templatePropertyName => $templateProperty) {
                if ($originProperty->props[0]->name->toString() === $templatePropertyName) {
                    unset($templatePropertiesCollection[$templatePropertyName]);
                }
            }
        }

        foreach ($templatePropertiesCollection as $property) {
            $this->insertStatementClose($originClass, $property);
        }

        return $originStmts;
    }
}
