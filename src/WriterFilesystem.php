<?php
declare(strict_types=1);

namespace Octava\CodeGenerator;

use Octava\CodeGenerator\Exception\WriterException;

class WriterFilesystem implements WriterInterface
{
    /**
     * @param string $filepath
     * @param string $content
     * @return void
     * @throws WriterException
     */
    public function write(string $filepath, string $content): void
    {
        $dir = pathinfo($filepath, PATHINFO_DIRNAME);
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new WriterException(sprintf('Directory "%s" was not created', $dir));
        }

        if (!file_put_contents($filepath, $content)) {
            throw new WriterException(sprintf('File "%s" cannot be saved', $filepath));
        }
    }
}
