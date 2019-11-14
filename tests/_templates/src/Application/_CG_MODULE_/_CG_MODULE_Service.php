<?php
declare(strict_types=1);

namespace App\Application\_CG_MODULE_;

class _CG_MODULE_Service
{
    /**
     * @var string
     */
    private $_CG_SERVICE_NAME__LCFIRST_;

    /**
     * _CG_MODULE_Service constructor.
     * @param string $_CG_SERVICE_NAME__LCFIRST_
     */
    public function __construct(string $_CG_SERVICE_NAME__LCFIRST_)
    {
        $this->_CG_SERVICE_NAME__LCFIRST_ = $_CG_SERVICE_NAME__LCFIRST_;
    }

    /**
     * @return string
     */
    public function get_CG_SERVICE_NAME_(): string
    {
        return $this->_CG_SERVICE_NAME__LCFIRST_;
    }
}
