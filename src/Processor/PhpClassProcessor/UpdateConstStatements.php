<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassConstTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassTrait;
use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\InsertStatementTrait;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Parser;
use Psr\Log\LoggerInterface;

class UpdateConstStatements
{
    use GetClassTrait, GetClassConstTrait, InsertStatementTrait;

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

        $templateConsts = $this->getClassConstStatements($templateClass);
        if (!$templateConsts) {
            return $originStmts;
        }

        $originConsts = $this->getClassConstStatements($originClass);
        if (!$originConsts) {
            foreach (array_reverse($templateConsts) as $templateConst) {
                array_unshift($originClass->stmts, $templateConst);
            }

            return $originStmts;
        }

        $templateConstsCollection = [];
        $templateConstsFlagsCollection = [];
        foreach ($templateConsts as $templateConst) {
            foreach ($templateConst->consts as $const) {
                $templateConstsCollection[$const->name->toString()] = $const;
                $templateConstsFlagsCollection[$const->name->toString()] = $templateConst->flags;
            }
        }

        foreach ($originConsts as $originTrait) {
            foreach ($originTrait->consts as $const) {
                foreach ($templateConstsCollection as $templateTraitName => $templateConst) {
                    if ($const->name->toString() === $templateTraitName) {
                        unset($templateConstsCollection[$templateTraitName]);
                        unset($templateConstsFlagsCollection[$const->name->toString()]);
                    }
                }
            }
        }

        if (count($templateConstsCollection)) {
            foreach ($templateConstsCollection as $key => $item) {
                $const = new ClassConst([$item], $templateConstsFlagsCollection[$key]);
                $this->insertStatementClose($originClass, $const);
            }
        }

        return $originStmts;
    }
}
