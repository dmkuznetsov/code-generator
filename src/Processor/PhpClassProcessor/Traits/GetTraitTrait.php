<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor\Traits;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;

trait GetTraitTrait
{
    /**
     * @param Class_ $classStatement
     * @return TraitUse[]
     */
    protected function &getTraitStatements(Class_ $classStatement): array
    {
        $result = [];
        foreach ($classStatement->stmts as $stmt) {
            if ($stmt instanceof TraitUse) {
                $result[] = $stmt;
            }
        }

        return $result;
    }
}
