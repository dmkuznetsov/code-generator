<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;

trait GetClassTrait
{
    /**
     * @param array $stmts
     * @return Class_|null
     */
    protected function &getClassStatement(array $stmts): ?Class_
    {
        $result = null;
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Class_) {
                $result = $stmt;
                break;
            }
            if ($stmt instanceof Namespace_) {
                foreach ($stmt->stmts as $item) {
                    if ($item instanceof Class_) {
                        $result = $item;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
