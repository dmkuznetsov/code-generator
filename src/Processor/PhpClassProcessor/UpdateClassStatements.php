<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Exception\ConflictClassnameException;
use PhpParser\Node\Stmt;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class UpdateClassStatements
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
     * @throws ConflictClassnameException
     */
    public function __invoke(array $originStmts, array $templateStmts): array
    {
        $originClass = $this->getClassStatement($originStmts);
        $templateClass = $this->getClassStatement($templateStmts);

        if (!$templateClass) {
            return $originStmts;
        }
        if (!$originClass) {
            $originStmts[] = $templateClass;
            return $originStmts;
        }

        if ($originClass->name->toString() !== $templateClass->name->toString()) {
            throw new ConflictClassnameException(
                sprintf(
                    'Origin classname "%s" not equal to "%s"',
                    $originClass->name->toString(),
                    $templateClass->name->toString()
                )
            );
        }

        return $originStmts;
    }
}
