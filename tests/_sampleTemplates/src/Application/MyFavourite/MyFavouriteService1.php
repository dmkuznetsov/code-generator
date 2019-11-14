<?php
declare(strict_types=1);

namespace App\Application\MyFavourite;

class MyFavouriteService
{
    /**
     * @var string
     */
    private $myFavourite;

    /**
     * MyFavouriteService constructor.
     * @param string $myFavourite
     */
    public function __construct(string $myFavourite)
    {
        $this->myFavourite = $myFavourite;
    }

    /**
     * @return string
     */
    public function getMyFavourite(): string
    {
        return $this->myFavourite;
    }
}
