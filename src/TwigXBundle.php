<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle;

use Lmc\TwigXBundle\DependencyInjection\CompilerPass\OverrideServiceCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TwigXBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideServiceCompilerPass(), PassConfig::TYPE_OPTIMIZE, -10);
    }
}
