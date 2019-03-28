<?php
declare(strict_types=1);

namespace App\Application\_DIRECTORY_\Assembler;

use App\Application\_DIRECTORY_\Dto\_FILENAME_Dto;

/**
 * Class _FILENAME_Dto
 * @package App\Application\_DIRECTORY_\Assembler
 */
class _FILENAME_Assembler implements _FILENAME_AssemblerInterface
{
    /**
     * @return _FILENAME_Dto
     */
    public function assemble(): _FILENAME_Dto
    {
        $dto = new _FILENAME_Dto();
        $dto->id = '111';

        return $dto;
    }
}
