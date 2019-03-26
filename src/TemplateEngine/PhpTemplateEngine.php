<?php
declare(strict_types=1);

namespace Dm\CodeGenerator\TemplateEngine;

use Dm\CodeGenerator\TemplateEngineInterface;
use PhpParser\ParserFactory;
use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class PhpTemplateEngine implements TemplateEngineInterface
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
    public function render(string $source, array $templateVars): string
    {
        $ast = $this->parser->parse($source);
        foreach ($ast as $stmt) {
//                $stmt->
        }
        // --- dump ---
        echo '<pre>';
        echo __FILE__.chr(10);
        echo __METHOD__.chr(10);
        var_dump($ast);
        echo '</pre>';
        exit;
        // --- // —
        $result = '';
//        }

        return $result;
    }
}