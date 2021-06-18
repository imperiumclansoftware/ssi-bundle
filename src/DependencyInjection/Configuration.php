<?php

namespace ICS\SsiBundle\DependencyInjection;

/**
 * File for SsiBundle configuration definition
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * SsiBundle configuration
 *
 * @package SsiBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treebuilder = new TreeBuilder('ssi');
        $rootNode =$treebuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('keycloak')
                    ->children()
                        ->booleanNode('auto_create_user')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('active_directory')
                    ->children()
                        ->booleanNode('auto_create_user')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treebuilder;
    }
}
