<?php
declare(strict_types=1);

namespace Dm\CodeGenerator\Processor;

use Dm\CodeGenerator\Exception\NotEqualClassnameException;
use Dm\CodeGenerator\Exception\NotEqualNamespaceException;
use Dm\CodeGenerator\Processor\PhpClassProcessor\UpdateUseStatements;
use Dm\CodeGenerator\ProcessorInterface;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\PrettyPrinterAbstract;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class PhpClassProcessor implements ProcessorInterface
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
     * @inheritdoc
     */
    public function process(string $originSource, string $templateSource, array $templateVars = []): string
    {
        if (empty($originSource)) {
            return $templateSource;
        }

        $originStmts = $this->parser->parse($originSource);
        $templateStmts = $this->parser->parse($templateSource);
        $this->checkNamespace($originStmts, $templateStmts);
        $this->checkClassname($originStmts, $templateStmts);

        $resultStmts = $originStmts;
//        $resultStmts = $this->updateDefines($originStmts, $templateStmts);
        $resultStmts = (new UpdateUseStatements($this->logger, $this->parser))($resultStmts, $templateStmts);
//        $resultStmts = $this->updateExtends($resultStmts, $templateStmts);
//        $resultStmts = $this->updateImplements($resultStmts, $templateStmts);
//        $resultStmts = $this->updateConstants($resultStmts, $templateStmts);
//        $resultStmts = $this->updateTraits($resultStmts, $templateStmts);
//        $resultStmts = $this->updateProperties($resultStmts, $templateStmts);
//        $resultStmts = $this->updateConstructor($resultStmts, $templateStmts);
//        $resultStmts = $this->updateMethods($resultStmts, $templateStmts);

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
}
