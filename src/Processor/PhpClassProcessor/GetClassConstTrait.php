<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;

trait GetClassConstTrait
{
    /**
     * @param Class_ $classStatement
     * @return ClassConst[]
     */
    protected function &getClassConstStatements(Class_ $classStatement): array
    {
        $result = [];
        foreach ($classStatement->stmts as $stmt) {
            if ($stmt instanceof ClassConst) {
                $result[] = $stmt;
            }
        }

        return $result;
    }
}
