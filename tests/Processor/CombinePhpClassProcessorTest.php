<?php
declare(strict_types=1);

namespace Dm\Tests\CodeGenerator\Processor;

use Dm\CodeGenerator\CodeGenerator;
use Dm\CodeGenerator\CodeGeneratorFactory;
use Dm\CodeGenerator\Configuration;
use Dm\CodeGenerator\Processor\CombinePhpClassProcessor;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CombinePhpClassProcessorTest extends TestCase
{
    public function testRender(): void
    {
        $logger = new NullLogger();
        $existingClass = <<<'PHP'
<?php
declare(strict_types=1);

namespace App\Application\MyFavourite\Dto;

use Swagger\Annotations as SWG;

/**
 * Class MyFavouriteDto
 * @package App\Application\MyFavourite\Dto
 *
 * @SWG\Definition(
 *     required={"id"}
 * )
 */
class MyFavouriteDto
{
    /**
     * Id
     * @var string
     * @SWG\Property(example="123")
     */
    public $id;
    
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}

PHP;

        $templateClass = <<<'PHP'
<?php
namespace App\Application\MyFavourite\Dto;

/**
 * Class MyFavouriteDto
 * @package App\Application\MyFavourite\Dto
 *
 * @SWG\Definition(
 *     required={"id"}
 * )
 */
class MyFavouriteDto
{
    /**
     * Id
     * @var string
     * @SWG\Property(example="123")
     */
    public $id;

    /**
     * Name
     * @var string
     */
    public $name;
    
    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}

PHP;
        $processor = new CombinePhpClassProcessor($logger);
        $processor->render($existingClass, $templateClass, []);

    }
}
