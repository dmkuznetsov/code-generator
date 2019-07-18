<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Parser;
use Psr\Log\LoggerInterface;

class UpdateUseStatements
{
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
        /**
         * @param Stmt[] $stmts
         * @return Use_[] $func
         */
        $func = function (array $stmts): array {
            $result = [];
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Use_) {
                    $result[] = $stmt;
                }
                if ($stmt instanceof Namespace_) {
                    foreach ($stmt->stmts as $item) {
                        if ($item instanceof Use_) {
                            $result[] = $item;
                        }
                    }
                }
            }

            return $result;
        };

        $originUses = $func($originStmts);
        $templateUses = $func($templateStmts);

        if (!count($templateUses)) {
            return $originStmts;
        }

        /** @var Use_[] $result */
        $result = $originStmts;
        $namespace = null;
        foreach ($result as $stmt) {
            if ($stmt instanceof Namespace_) {
                $namespace = $stmt;
                break;
            }
        }
        if (null !== $namespace) {
            $place = &$namespace->stmts;
        } else {
            /** @noinspection PhpUndefinedFieldInspection */
            $place = &$result->stmts;
        }

        if (!count($originUses)) {
            foreach ($templateUses as $stmt) {
                array_unshift($place, $stmt);
            }
        } else {
            $originUsesFlat = [];
            foreach ($originUses as $originUse) {
                foreach ($originUse->uses as $use) {
                    $originUsesFlat[] = $use->name->toString();
                }
            }
            $originUsesFlat = array_unique($originUsesFlat);
            $templateUsesFlat = [];
            foreach ($templateUses as $templateUse) {
                foreach ($templateUse->uses as $use) {
                    $templateUsesFlat[] = $use->name->toString();
                }
            }
            $templateUsesFlat = array_unique($templateUsesFlat);
            $absentUses = array_diff($templateUsesFlat, $originUsesFlat);
            foreach ($templateUses as $templateUse) {
                foreach ($templateUse->uses as $use) {
                    if (in_array($use->name->toString(), $absentUses, true)) {
                        array_unshift($place, $templateUse);
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
