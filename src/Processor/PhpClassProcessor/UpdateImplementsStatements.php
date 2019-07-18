<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use Octava\CodeGenerator\Processor\PhpClassProcessor\Traits\GetClassTrait;
use PhpParser\Node\Stmt;
use PhpParser\Parser;
use Psr\Log\LoggerInterface;

class UpdateImplementsStatements
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
     */
    public function __invoke(array $originStmts, array $templateStmts): array
    {
        $originClass = $this->getClassStatement($originStmts);
        $templateClass = $this->getClassStatement($templateStmts);

        if (!$originClass || !$templateClass) {
            return $originStmts;
        }

        if (!$originClass->implements) {
            $originClass->implements = $templateClass->implements;
        } else {
            /**
             * @var $originInterface
             */
            foreach ($templateClass->implements as $templateInterface) {
                $found = false;
                foreach ($originClass->implements as $originInterface) {
                    if ($templateInterface->toString() === $originInterface->toString()) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $originClass->implements[] = $templateInterface;
                }
            }
        }

        return $originStmts;
    }
}
