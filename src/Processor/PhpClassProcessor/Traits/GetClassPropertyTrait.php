<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Processor\PhpClassProcessor\Traits;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;

trait GetClassPropertyTrait
{
    /**
     * @param Class_ $classStatement
     * @return Property[]
     */
    protected function &getPropertyStatements(Class_ $classStatement): array
    {
        $result = [];
        foreach ($classStatement->stmts as $stmt) {
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $property) {
                    $result[] = new Property($stmt->flags, [$property], $stmt->getAttributes(), $stmt->type);
                }
            }
        }

        return $result;
    }
}
