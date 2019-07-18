<?php
declare(strict_types=1);

namespace App\Application\_DIRECTORY_\Assembler;

use App\Application\_DIRECTORY_\Dto\_CG_MODULE_Dto;

/**
 * Class _FILENAME_Dto
 * @package App\Application\_DIRECTORY_\Assembler
 */
class _CG_MODULE_Assembler implements _CG_MODULE_AssemblerInterface
{
    /**
     * @return _CG_MODULE_Dto
     */
    public function assemble(): _CG_MODULE_Dto
    {
        $dto = new _CG_MODULE_Dto();
        $dto->id = '111';

        return $dto;
    }
}
