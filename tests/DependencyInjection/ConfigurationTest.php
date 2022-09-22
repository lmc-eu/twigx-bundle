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

CONFIG;

        $this->assertEquals($reference, $dumper->dump(new Configuration()));
    }
}
