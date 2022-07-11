<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;

class ConfigurationTest extends TestCase
{
    public function testConfigurationDefinition(): void
    {
        $dumper = new YamlReferenceDumper();
        $reference = <<<CONFIG
twigx:
    paths:                []
    paths_alias:          spirit
    css_class_prefix:     null
    html_syntax_lexer:    true
    icons:
        paths:                []
        alias:                icons-assets\n
CONFIG;

        $this->assertEquals($reference, $dumper->dump(new Configuration()));
    }
}
