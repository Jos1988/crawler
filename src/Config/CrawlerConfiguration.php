<?php

namespace App\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CrawlerConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('crawler');

        $rootNode
            ->children()
                ->arrayNode('websites')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                            ->end()
                            ->arrayNode('ignorePageUrlsWith')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}