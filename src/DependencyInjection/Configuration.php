<?php

namespace ICS\SsiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treebuilder = new TreeBuilder('ssibundle');

        $rootNode =$treebuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('keycloak')
                    ->children()
                        ->booleanNode('auto_create_user')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treebuilder;
    }
}
