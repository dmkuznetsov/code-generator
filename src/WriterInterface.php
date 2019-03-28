<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\WriterException;

interface WriterInterface
{
    /**
     * @param string $filepath
     * @param string $content
     * @return void
     * @throws WriterException
     */
    public function write(string $filepath, string $content): void ;
}
