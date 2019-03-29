<?php
declare(strict_types=1);

namespace Octava\CodeGenerator\Exception;

use Octava\CodeGenerator\CodeGeneratorException;
use Throwable;

class ConflictException extends CodeGeneratorException
{
    /**
     * @var string
     */
    private $filepath;
    /**
     * @var string
     */
    private $source;

    public function __construct(string $filepath, string $source, Throwable $previous = null)
    {
        parent::__construct('Conflict', 0, $previous);
        $this->filepath = $filepath;
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }
}
