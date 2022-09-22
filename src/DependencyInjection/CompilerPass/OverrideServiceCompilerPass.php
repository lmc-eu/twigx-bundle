<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\DependencyInjection\CompilerPass;

use Lmc\TwigXBundle\Compiler\ComponentLexer;
use Lmc\TwigXBundle\DependencyInjection\TwigXExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $twigDefinition = $container->getDefinition('twig');

        $twigLoaderDefinition = $container->findDefinition('twig.loader');

        /** @var array<string> $paths */
        $paths = $container->getParameter(TwigXExtension::PARAMETER_PATHS);
        $pathAlias = $container->getParameter(TwigXExtension::PARAMETER_PATH_ALIAS);

        $this->addGlobPath($twigLoaderDefinition, TwigXExtension::DEFAULT_PARTIALS_PATH, TwigXExtension::DEFAULT_PARTIALS_ALIAS);

        foreach ($paths as $path) {
            $twigLoaderDefinition->addMethodCall('addPath', [$path, $pathAlias]);

            if ($path === TwigXExtension::DEFAULT_COMPONENTS_PATH) {
                $this->addGlobPath($twigLoaderDefinition, $path, TwigXExtension::DEFAULT_PATH_ALIAS);
            }
        }

        $twigDefinition->addMethodCall('setLexer', [new Reference(ComponentLexer::class)]);
    }

    private function addGlobPath(Definition $twigLoaderDefinition, string $path, string $alias): void
    {
        if ($paths = glob($path, GLOB_ONLYDIR)) {
            foreach ($paths as $path) {
                $twigLoaderDefinition->addMethodCall('addPath', [$path, $alias]);
            }
        }
    }
}
