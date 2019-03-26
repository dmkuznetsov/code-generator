<?php
declare(strict_types=1);

namespace Dm\CodeGenerator;

class CodeGeneratorFactory
{
    /**
     * @param ConfigurationInterface $configuration
     * @return CodeGenerator
     */
    public function create(ConfigurationInterface $configuration): CodeGenerator
    {
        $templateFactory = new TemplateFactory($configuration);
        $templateEngine = new TemplateEngine($configuration->getLogger());

        return new CodeGenerator($configuration);
    }
}