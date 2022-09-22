<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('twigx');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Symfony 3
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('twigx');
        }

        $rootNode
            ->children()
            ->arrayNode('paths')
            ->scalarPrototype()->end()
            ->end()
            ->scalarNode('paths_alias')
            ->defaultValue(TwigXExtension::DEFAULT_PATH_ALIAS)
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
