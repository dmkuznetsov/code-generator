<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\FilesystemException;

interface FilesystemInterface
{
    /**
     * @param string $filepath
     * @return bool
     */
    public function exists(string $filepath): bool;

    /**
     * @param string $filepath
     * @param string $content
     * @return void
     * @throws FilesystemException
     */
    public function dump(string $filepath, string $content): void;

    /**
     * @param string $filepath
     * @return string
     * @throws FilesystemException
     */
    public function read(string $filepath): string;
}
