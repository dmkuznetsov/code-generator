<?php
declare(strict_types=1);

namespace App\Application\_DIRECTORY_;

use App\Application\_DIRECTORY_\Dto\_FULLFILENAME_Dto;
use App\Application\_DIRECTORY_\Assembler\_FULLFILENAME_AssemblerInterface;

/**
 * Class _FILENAME_Service
 * @package App\Application\_DIRECTORY_
 */
class _FILENAME_Service
{
    /** @var _FULLFILENAME_AssemblerInterface */
    private $_VARNAME_Assembler;

    /**
     * _FILENAME_Service constructor.
     * @param _FULLFILENAME_AssemblerInterface $_VARNAME_Assembler
     */
    public function __construct(_FULLFILENAME_AssemblerInterface $_VARNAME_Assembler)
    {
        $this->_VARNAME_Assembler = $_VARNAME_Assembler;
    }

    public function _ACTION__MODULE_(string $username): _FULLFILENAME_Dto
    {
        $dto = $this->_VARNAME_Assembler->assemble();

        return $dto;
    }
}
