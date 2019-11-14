<?php
declare(strict_types=1);

namespace App\Application\_CG_MODULE_;

class MyFavouriteService implements \JsonSerializable
{
    /**
     * @var int
     */
    private $number;

    /**
     * MyFavouriteService constructor.
     * @param int $number
     */
    public function __construct(int $number = 10)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'number' => $this->number
        ];
    }
}
