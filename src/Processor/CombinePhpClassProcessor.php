<?php
declare(strict_types=1);

namespace Dm\CodeGenerator\Processor;

use Dm\CodeGenerator\ProcessorInterface;
use PhpParser\PrettyPrinter;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
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

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @param string $source
     * @param array $templateVars
     * @return string
     */
    public function render(string $existingSource, string $source, array $templateVars): string
    {
        $existingSourceAst = $this->parser->parse($existingSource);
//        $sourceAst = $this->parser->parse($source);
        $this->modifyFunction('__construct', $existingSourceAst);


        return '';
    }

    private function modifyFunction(string $methodName, array $asts)
    {

        $prettyPrinter = new PrettyPrinter\Standard;

        foreach ($asts as $ast) {
            if ($ast instanceof Namespace_) {
                foreach ($ast->stmts as $namespaceAst) {
                    if ($namespaceAst instanceof Class_) {
                        foreach ($namespaceAst->stmts as $classAst) {
                            if ($classAst instanceof ClassMethod) {
                                foreach ($namespaceAst->stmts as $methodAst) {
                                    if ($methodAst->name->name === $methodName) {
//                                        $methodAst->params = [];
                                        echo $prettyPrinter->prettyPrint([$methodAst]);
                                        exit;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
