<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Exception\ConflictClassExtendsException;
use PhpParser\Node\Stmt;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class UpdateExtendsStatements
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

    public function __construct(LoggerInterface $logger, Parser $parser)
    {
        $this->logger = $logger;
        $this->parser = $parser;
    }

    /**
     * @param Stmt[] $originStmts
     * @param Stmt[] $templateStmts
     * @return Stmt[]
     * @throws ConflictClassExtendsException
     */
    public function __invoke(array $originStmts, array $templateStmts): array
    {
        $originClass = $this->getClassStatement($originStmts);
        $templateClass = $this->getClassStatement($templateStmts);

        if (!$originClass || !$templateClass) {
            return $originStmts;
        }

        if (!$originClass->extends) {
            $originClass->extends = $templateClass->extends;
        } elseif ($templateClass->extends
            && $originClass->extends->toString() !== $templateClass->extends->toString()) {
            throw new ConflictClassExtendsException(
                sprintf(
                    'Parent class "%s" of "%s" conflicts with parent class "%s" of "%s"',
                    $templateClass->extends->toString(),
                    $templateClass->name->toString(),
                    $originClass->extends->toString(),
                    $originClass->name->toString()
                )
            );
        }

        return $originStmts;
    }
}
