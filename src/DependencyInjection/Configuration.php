<?php

declare(strict_types=1);

namespace DevBase\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('user');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('debug')->defaultValue('%kernel.debug%')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
