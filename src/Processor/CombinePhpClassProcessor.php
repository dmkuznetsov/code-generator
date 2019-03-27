<?php
declare(strict_types=1);

namespace Dm\CodeGenerator\Processor;

use Dm\CodeGenerator\Exception\NotEqualClassnameException;
use Dm\CodeGenerator\Exception\NotEqualNamespaceException;
use Dm\CodeGenerator\Exception\ProcessorException;
use Dm\CodeGenerator\ProcessorInterface;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\PrettyPrinter;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinterAbstract;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class CombinePhpClassProcessor // implements ProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Parser
     */
    protected $parser;
    /**
     * @var PrettyPrinterAbstract
     */
    protected $printer;

    public function __construct(LoggerInterface $logger, Parser $parser, PrettyPrinterAbstract $printer)
    {
        $this->logger = $logger;
        $this->parser = $parser;
        $this->printer = $printer;
    }

    /**
     * @param string $originSource
     * @param string $templateSource
     * @return string
     * @throws ProcessorException
     */
    public function process(string $originSource, string $templateSource): string
    {
        if (empty($originSource)) {
            return $templateSource;
        }

        $originStmts = $this->parser->parse($originSource);
        $templateStmts = $this->parser->parse($templateSource);
        $this->checkNamespace($originStmts, $templateStmts);
        $this->checkClassname($originStmts, $templateStmts);
        $resultStmts = $this->updateUseStatements($originStmts, $templateStmts);

        return "<?php\n" . $this->printer->prettyPrint($resultStmts);
    }

    /**
     * @param Stmt[] $originStmts
     * @param Stmt[] $templateStmts
     * @return void
     * @throws NotEqualNamespaceException
     */
    protected function checkNamespace(array $originStmts, array $templateStmts): void
    {
        $func = function (array $stmts): ?string {
            $result = null;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Namespace_) {
                    $result = $stmt->name->toString();
                    break;
                }
            }

            return $result;
        };
        $originNamespace = $func($originStmts);
        $templateNamespace = $func($templateStmts);

        if ($originNamespace !== $templateNamespace) {
            throw new NotEqualNamespaceException(
                sprintf('Origin namespace "%s" not equal to "%s"', $originNamespace, $templateNamespace)
            );
        }
    }

    /**
     * @param Stmt[] $originStmts
     * @param Stmt[] $templateStmts
     * @return void
     * @throws NotEqualClassnameException
     */
    protected function checkClassname(array $originStmts, array $templateStmts): void
    {
        $func = function (array $stmts): ?string {
            $result = null;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Class_) {
                    $result = $stmt->name->toString();
                    break;
                }

                if ($stmt instanceof Namespace_) {
                    foreach ($stmt->stmts as $item) {
                        if ($item instanceof Class_) {
                            $result = $item->name->toString();
                            break;
                        }
                    }
                }
            }

            return $result;
        };
        $originClassname = $func($originStmts);
        $templateClassname = $func($templateStmts);

        if ($originClassname !== $templateClassname) {
            throw new NotEqualClassnameException(
                sprintf('Origin classname "%s" not equal to "%s"', $originClassname, $templateClassname)
            );
        }
    }

    /**
     * @param Stmt[] $originStmts
     * @param Stmt[] $templateStmts
     * @return Stmt[]
     */
    protected function updateUseStatements(array $originStmts, array $templateStmts): array
    {
        $func = function (array $stmts): array {
            $result = [];
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Use_) {
                    $result[] = $stmt;
                }
                if ($stmt instanceof Namespace_) {
                    foreach ($stmt->stmts as $item) {
                        if ($item instanceof Use_) {
                            $result[] = $item;
                        }
                    }
                }
            }

            return $result;
        };

        $originUses = $func($originStmts);
        $templateUses = $func($templateStmts);

        if (!count($templateUses)) {
            return $originStmts;
        }

        $result = $originStmts;
        $namespace = null;
        foreach ($result as $stmt) {
            if ($stmt instanceof Namespace_) {
                $namespace = $stmt;
                break;
            }
        }
        if (null !== $namespace) {
            $place = &$namespace->stmts;
        } else {
            $place = &$result->stmts;
        }

        if (!count($originUses)) {
            foreach ($templateUses as $stmt) {
                array_unshift($place, $stmt);
            }
        } else {

        }

        return $result;
    }
}
