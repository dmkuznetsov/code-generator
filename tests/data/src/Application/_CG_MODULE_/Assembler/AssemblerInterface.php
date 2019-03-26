<?php
declare(strict_types=1);

namespace App\Application\_DIRECTORY_\Assembler;

use App\Application\_DIRECTORY_\Dto\_FILENAME_Dto;

/**
 * Class _FILENAME_Dto
 * @package App\Application\_DIRECTORY_\Assembler
 */
interface _FILENAME_AssemblerInterface
{
    /**
     * @return _FILENAME_Dto
     */
    public function assemble(): _FILENAME_Dto;
}
