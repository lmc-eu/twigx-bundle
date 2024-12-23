<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigXExtensionTest extends TestCase
{
    private ContainerBuilder $containerBuilder;

    /**
     * @param array<int, array<string, array<int, string>|string|false>> $configs
     */
    private function loadExtension(array $configs): void
    {
        $extension = new TwigXExtension();
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->registerExtension($extension);

        $extension->load($configs, $this->containerBuilder);
    }

    public function testShouldRegisterParameters(): void
    {
        $config = [
            'paths' => ['templates/'],
            'paths_alias' => 'ui-components',
        ];

        $this->loadExtension([$config]);

        $this->assertTrue($this->containerBuilder->hasParameter('twigx.paths'));
        $this->assertTrue($this->containerBuilder->hasParameter('twigx.paths_alias'));
    }
}
