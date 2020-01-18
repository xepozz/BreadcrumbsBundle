<?php

namespace Xepozz\BreadcrumbsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('breadcrumbs');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('separator')
                    ->defaultValue('/')
                ->end()
                ->scalarNode('separatorClass')
                    ->defaultValue('separator')
                ->end()
                ->scalarNode('listId')
                    ->defaultValue('wo-breadcrumbs')
                ->end()
                ->scalarNode('listClass')
                    ->defaultValue('breadcrumb')
                ->end()
                ->scalarNode('itemClass')
                    ->defaultValue('')
                ->end()
                ->scalarNode('linkRel')
                    ->defaultValue('')
                ->end()
                ->scalarNode('locale')
                    ->defaultNull()
                ->end()
                ->scalarNode('translation_domain')
                    ->defaultNull()
                ->end()
                ->scalarNode('viewTemplate')
                    ->defaultValue('@Breadcrumbs/microdata.html.twig')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
