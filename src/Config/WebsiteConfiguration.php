<?php

namespace App\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class WebsiteConfiguration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('crawler');

        $rootNode
            ->children()
                ->arrayNode('websites')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->booleanNode('trimGET')->end()
                            ->arrayNode('ignorePageUrlsWith')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}