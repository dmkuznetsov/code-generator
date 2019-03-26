<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

use Psr\Log\LoggerInterface;
use PhpParser\Parser;

class TemplateEngine
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(LoggerInterface $logger, Parser $parser)
    {
        $this->logger = $logger;
        $this->parser = $parser;
    }

    public function parse(TemplateInterface $template): string
    {
//        if (!file_exists($template->getOutputDir())) {
//            $content = file_get_contents($template->getTemplatePath());
//            $search = array_keys($template->getTemplateVars());
//            $replace = array_values($template->getTemplateVars());
//            $result = str_replace($search, $replace, $content);
//        } else {
            $tmp = $template->getTemplatePath();
            $code = file_get_contents($tmp);
            $ast = $this->parser->parse($code);
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