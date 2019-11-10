<?php
declare(strict_types=1);

namespace Octava\Tests\CodeGenerator;

use Octava\CodeGenerator\Configuration;
use Octava\CodeGenerator\TemplateFactory;
use PHPUnit\Framework\TestCase;

class TemplateFactoryTest extends TestCase
{
    public function testTemplatePath(): void
    {
        $factory = new TemplateFactory(new Configuration('base_path', 'output_base_path'));
        $template = $factory->create('path/to/template.php', 'path/to/template_processed.php');
        $this->assertEquals('base_path/path/to/template.php', $template->getTemplatePath());
    }

    public function testTemplatePathWithVariables(): void
    {
        $factory = new TemplateFactory(new Configuration('base_path', 'output_base_path'));
        $template = $factory->create('path/to/_CG_TEMPLATE_.php', 'path/to/template_processed.php');
        $this->assertEquals('base_path/path/to/_CG_TEMPLATE_.php', $template->getTemplatePath());
    }

    public function testBaseTemplatePathWithVariables(): void
    {
        $factory = new TemplateFactory(new Configuration('base_path_CG_ROOT_', 'output_base_path'));
        $template = $factory->create('path/to/_CG_TEMPLATE_.php', 'path/to/template_processed.php');
        $this->assertEquals('base_path_CG_ROOT_/path/to/_CG_TEMPLATE_.php', $template->getTemplatePath());
    }

    public function testOutputPath(): void
    {
        $factory = new TemplateFactory(new Configuration('base_path', 'output_base_path'));
        $template = $factory->create('path/to/template.php', 'path/to/template_processed.php');
        $this->assertEquals('output_base_path/path/to/template_processed.php', $template->getOutputPath());
    }

    public function testOutputPathWithVariables(): void
    {
        $factory = new TemplateFactory(new Configuration('base_path', 'output_base_path'));
        $template = $factory->create(
            'path/to/_CG_TEMPLATE_.php',
            'path/to/_CG_TEMPLATE__processed.php',
            ['_CG_TEMPLATE_' => 'school']
        );
        $this->assertEquals('output_base_path/path/to/school_processed.php', $template->getOutputPath());
    }

    public function testBaseOutputPathWithVariables(): void
    {
        $configuration = new Configuration('base_path', 'output_base_path__CG_ROOT_', ['_CG_ROOT_' => 'home']);
        $factory = new TemplateFactory($configuration);
        $template = $factory->create(
            'path/to/_CG_TEMPLATE_.php',
            'path/to/_CG_TEMPLATE__processed.php',
            ['_CG_TEMPLATE_' => 'school']
        );
        $this->assertEquals('output_base_path_home/path/to/school_processed.php', $template->getOutputPath());
    }

    public function testTemplateVariables(): void
    {
        $configuration = new Configuration(
            'base_path', 'output_base_path__CG_ROOT_', [
            '_CG_ROOT_' => 'home',
            '_CG_TEMPLATE_' => 'childhood',
        ]
        );
        $factory = new TemplateFactory($configuration);
        $template = $factory->create('path/to/table.php', 'path/to/table_processed.php', ['_CG_TEMPLATE_' => 'school']);
        $this->assertEquals(
            [
                '_CG_FILE_NAME_' => 'table',
                '_CG_FILE_NAME_UCFIRST_' => 'Table',
                '_CG_FILE_NAME_LCFIRST_' => 'table',
                '_CG_FILE_BASENAME_' => 'table.php',
                '_CG_FILE_DIR_' => 'path/to',
                '_CG_FILE_PATH_' => 'path/to/table',
                '_CG_FILE_EXTENSION_' => 'php',
                '_CG_TEMPLATE_' => 'school',
                '_CG_ROOT_' => 'home',
            ],
            $template->getTemplateVars()
        );
    }
}
