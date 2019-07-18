<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetTraitTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\InsertStatementTrait;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\TraitUse;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class UpdateTraitsStatements
{
    use GetClassTrait, GetTraitTrait, InsertStatementTrait;

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

        $templateTraits = $this->getTraitStatements($templateClass);
        if (!$templateTraits) {
            return $originStmts;
        }

        $originTraits = $this->getTraitStatements($originClass);
        if (!$originTraits) {
            foreach (array_reverse($templateTraits) as $templateTrait) {
                array_unshift($originClass->stmts, $templateTrait);
            }

            return $originStmts;
        }

        $templateTraitsCollection = [];
        foreach ($templateTraits as $templateTrait) {
            foreach ($templateTrait->traits as $trait) {
                $templateTraitsCollection[$trait->toString()] = $trait;
            }
        }

        foreach ($originTraits as $originTrait) {
            foreach ($originTrait->traits as $trait) {
                foreach ($templateTraitsCollection as $templateTraitName => $templateTrait) {
                    if ($trait->toString() === $templateTraitName) {
                        unset($templateTraitsCollection[$templateTraitName]);
                    }
                }
            }
        }

        if (count($templateTraitsCollection)) {
            $trait = new TraitUse($templateTraitsCollection);
            $this->insertStatementClose($originClass, $trait);
        }

        return $originStmts;
    }
}
