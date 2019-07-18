<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor\Traits;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;

trait GetClassMethodTrait
{
    /**
     * @param Class_ $classStatement
     * @return ClassMethod|null
     */
    protected function getClassConstructorStatement(Class_ $classStatement): ?ClassMethod
    {
        foreach ($this->getClassMethodStatements($classStatement) as $method) {
            if ($method->name->toString() === '__construct') {
                return $method;
            }
        }

        return null;
    }

    /**
     * @param Class_ $classStatement
     * @return ClassMethod[]
     */
    protected function &getClassMethodStatements(Class_ $classStatement): array
    {
        $result = [];
        foreach ($classStatement->stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                $result[] = $stmt;
            }
        }

        return $result;
    }
}
