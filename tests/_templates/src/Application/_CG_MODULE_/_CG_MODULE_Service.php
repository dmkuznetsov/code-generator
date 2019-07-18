<?php
declare(strict_types=1);

namespace App\Application\_DIRECTORY_;

use App\Application\_DIRECTORY_\Dto\_CG_MODULE_Dto;
use App\Application\_DIRECTORY_\Assembler\_CG_MODULE_AssemblerInterface;

/**
 * Class _CG_MODULE_Service
 * @package _CG_NAMESPACE_
 */
class _CG_MODULE_Service
{
    /** @var _CG_MODULE_AssemblerInterface */
    private $_VARNAME_Assembler;

    /**
     * _FILENAME_Service constructor.
     * @param _CG_MODULE_AssemblerInterface $_VARNAME_Assembler
     */
    public function __construct(_CG_MODULE_AssemblerInterface $_VARNAME_Assembler)
    {
        $this->_VARNAME_Assembler = $_VARNAME_Assembler;
    }

    public function _ACTION__MODULE_(string $username): _CG_MODULE_Dto
    {
        $dto = $this->_VARNAME_Assembler->assemble();

        return $dto;
    }
}
