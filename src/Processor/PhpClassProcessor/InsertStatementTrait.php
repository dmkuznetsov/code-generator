<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;

trait InsertStatementTrait
{
    /**
     * @param Class_ $class
     * @param Stmt $stmt
     * @return void
     */
    protected function insertStatementClose(Class_ $class, Stmt $stmt): void
    {
        $position = 0;
        $classname = get_class($stmt);
        foreach ($class->stmts as $key => $value) {
            if ($value instanceof $classname) {
                $position = $key;
            }
        }

        $tmp = [];
        foreach ($class->stmts as $key => $value) {
            $tmp[] = $value;
            if ($key === $position) {
                $tmp[] = $stmt;
            }
        }
        $class->stmts = $tmp;
    }
}
