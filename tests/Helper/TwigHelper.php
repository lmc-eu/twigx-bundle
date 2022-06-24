<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\Helper;

use Lmc\TwigXBundle\Compiler\ComponentLexer;
use Lmc\TwigXBundle\DependencyInjection\CompilerPass\OverrideServiceCompilerPass;
use Lmc\TwigXBundle\DependencyInjection\TwigXExtension;
use Lmc\TwigXBundle\Twig\PropsExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigHelper
{
    /**
     * @param array<string> $extendedComponentsPath
     */
    public static function setup(
        string $defaultTemplatePath,
        string $defaultAlias,
        ?string $prefix = null,
        array $extendedComponentsPath = []
    ): Environment {
        $loader = new FilesystemLoader($defaultTemplatePath);
        $paths = array_merge($extendedComponentsPath, [TwigXExtension::DEFAULT_COMPONENTS_PATH]);

        foreach ($paths as $path) {
            $loader->addPath($path, $defaultAlias);
        }

        $loader->addPath(TwigXExtension::DEFAULT_PARTIALS_PATH, TwigXExtension::DEFAULT_PARTIALS_ALIAS);
        $loader->addPath(TwigXExtension::DEFAULT_COMPONENTS_PATH, TwigXExtension::DEFAULT_PATH_ALIAS);

        $twig = new Environment($loader, [
            'cache' => false,
        ]);

        $propsExtension = new PropsExtension();
        $twig->addExtension($propsExtension);

        if ($prefix) {
            $twig->addGlobal(OverrideServiceCompilerPass::GLOBAL_PREFIX_TWIG_VARIABLE, $prefix);
        }

        $twig->setLoader($loader);
        $twig->setLexer(new ComponentLexer($twig, [], $defaultAlias));

        return $twig;
    }
}
