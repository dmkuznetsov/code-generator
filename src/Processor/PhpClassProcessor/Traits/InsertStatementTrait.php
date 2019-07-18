<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor\Traits;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;

trait InsertStatementTrait
{
    /**
     * @param Class_ $class
     * @param Stmt[] $stmts
     * @return void
     */
    protected function insertStatementClose(Class_ $class, Stmt ...$stmts): void
    {
        $position = 0;
        $classname = get_class(current($stmts));
        foreach ($class->stmts as $key => $value) {
            if ($value instanceof $classname) {
                $position = $key;
            }
        }

        $tmp = [];
        foreach ($class->stmts as $key => $value) {
            $tmp[] = $value;
            if ($key === $position) {
                foreach ($stmts as $stmt) {
                    $tmp[] = $stmt;
                }
            }
        }
        $class->stmts = $tmp;
    }
}
