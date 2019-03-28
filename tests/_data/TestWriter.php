<?php
declare(strict_types=1);

namespace Octava\Tests\_data;

use Octava\CodeGenerator\WriterInterface;

class TestWriter implements WriterInterface
{
    /**
     * @inheritdoc
     */
    public function write(string $filepath, string $content): void
    {
    }
}
