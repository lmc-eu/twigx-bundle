<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\DependencyInjection\CompilerPass;

use Lmc\TwigXBundle\Compiler\ComponentLexer;
use Lmc\TwigXBundle\DependencyInjection\TwigXExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public const GLOBAL_PREFIX_TWIG_VARIABLE = '_twigxClassPrefix';

    public function process(ContainerBuilder $container): void
    {
        $twigDefinition = $container->getDefinition('twig');

        $twigLoaderDefinition = $container->findDefinition('twig.loader');

        /** @var array<string> $paths */
        $paths = $container->getParameter(TwigXExtension::PARAMETER_PATHS);
        $pathAlias = $container->getParameter(TwigXExtension::PARAMETER_PATH_ALIAS);
        $isLexer = $container->getParameter(TwigXExtension::PARAMETER_HTML_SYNTAX_LEXER);
        $classPrefix = $container->getParameter(TwigXExtension::PARAMETER_CSS_CLASS_PREFIX);
        /** @var array<string> $iconsPaths */
        $iconsPaths = $container->getParameter(TwigXExtension::PARAMETER_ICONS_PATHS);
        $iconsPathAlias = $container->getParameter(TwigXExtension::PARAMETER_ICONS_PATH_ALIAS);

        $twigLoaderDefinition->addMethodCall('addPath', [TwigXExtension::DEFAULT_PARTIALS_PATH, TwigXExtension::DEFAULT_PARTIALS_ALIAS]);

        foreach ($paths as $path) {
            $twigLoaderDefinition->addMethodCall('addPath', [$path, $pathAlias]);

            if ($path === TwigXExtension::DEFAULT_COMPONENTS_PATH) {
                $twigLoaderDefinition->addMethodCall('addPath', [$path, TwigXExtension::DEFAULT_PATH_ALIAS]);
            }
        }

        foreach ($iconsPaths as $iconPath) {
            $twigLoaderDefinition->addMethodCall('addPath', [$iconPath, $iconsPathAlias]);
        }

        $twigDefinition->addMethodCall('addGlobal', [self::GLOBAL_PREFIX_TWIG_VARIABLE, $classPrefix]);

        if ($isLexer) {
            $twigDefinition->addMethodCall('setLexer', [new Reference(ComponentLexer::class)]);
        }
    }
}
