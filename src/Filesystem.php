<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\FilesystemException;

class Filesystem implements FilesystemInterface
{
    /**
     * @inheritDoc
     */
    public function dump(string $filepath, string $content): void
    {
        $dir = pathinfo($filepath, PATHINFO_DIRNAME);
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new FilesystemException(sprintf('Directory "%s" was not created', $dir));
        }

        if (!file_put_contents($filepath, $content)) {
            throw new FilesystemException(sprintf('File "%s" cannot be saved', $filepath));
        }
    }

    /**
     * @inheritDoc
     */
    public function read(string $filepath): string
    {
        if (!$this->exists($filepath)) {
            throw new FilesystemException(sprintf("File '%s' doesn't exists", $filepath));
        }

        return file_get_contents($filepath);
    }

    /**
     * @inheritdoc
     */
    public function exists(string $filepath): bool
    {
        return file_exists($filepath);
    }
}
